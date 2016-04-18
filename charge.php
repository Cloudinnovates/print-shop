<?php
session_start();
include '../../functions.php';

$conn = connect_DB();

$user_details = validate_user($conn);

foreach ($user_details as $user_detail) {
	$userId = $user_detail['userId'];
	$username = $user_detail['userUsername'];
	$email = $user_detail['userEmail'];
	$company = $user_detail['userCompany'];
	$firstName = $user_detail['userFirstName'];
	$lastName = $user_detail['userLastName'];
	$streetAdd = $user_detail['addStreet'];
	$cityAdd = $user_detail['addCity'];
	$stateAdd = $user_detail['addState'];
	$zipAdd = $user_detail['addZip'];
	$bio = $user_detail['userBio'];
	$phoneNumber = $user_detail['userPhone'];
	$photoUrl = $user_detail['userPhotoUrl'];
	$acctType = $user_detail['userType'];
	$priceTier = $user_detail['userPriceTier'];
	$isApproved = $user_detail['userIsApproved'];
	$userPsBalance = $user_detail['userPsBalance'];
}
require_once('./Stripe/init.php');


if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}

// Get last order confirmation number that was placed. Add one to this number and use it as the 
// confirmation number for this order.

$sqlGetLastConfNo = '
SELECT PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized
FROM PrintShopCart
WHERE PrintShopCart.cartIsFinalized = 1
ORDER BY PrintShopCart.cartConfNo
';

$rows = $conn->query($sqlGetLastConfNo);
$rowsCount = $rows->rowCount();

if ($rowsCount >= 1) {
	foreach ($rows as $row) {
		$cartConfNo = $row['cartConfNo'];
	}
	$cartConfNo = $cartConfNo + 1;
}
else {
	$cartConfNo = 1000;
}

// Checks for the main cart information, this code has been placed up here so that the amount to pay with card is accessible
// to stripe.
$sqlCheckCart = '
SELECT PrintShopCart.cartId, PrintShopCart.cartShipping, PrintShopCart.cartTotalPrice, PrintShopCart.cartPayWithPS,
PrintShopCart.cartPayWithCard, UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$cartShipping = $row['cartShipping'];
	$cartTotal = $row['cartTotalPrice'];
	$cartPayWithPS = $row['cartPayWithPS'];
	$cartPayWithCard = $row['cartPayWithCard'];
	$orderTotal = $cartShipping + $cartTotal;
	$stripeTotal = $cartPayWithCard*100;
}

// Processes the payment via the Stripe API

// Updates the cart with the final order details

$currDate = date("m/d/Y");

$sqlUpdateCart = '
UPDATE PrintShopCart, UserToPrintShopCart
SET PrintShopCart.cartConfNo = '.$cartConfNo.', PrintShopCart.cartIsFinalized = 1, cartDateFinalized = "'.$currDate.'",
PrintShopCart.cartInstructions = "'.$_POST['instructions'].'", PrintShopCart.cartIsDeleted = 0
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = '.$_SESSION['cartId'].' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$conn->exec($sqlUpdateCart);

// If the form on final confirmation was POSTed, process payment and give success. Else redirect to various erros.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['useStripe'] == 1) {
// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey("sk_test_fYKCmuYE7J97JC7UwjD83e6J");

// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];

// Create the charge on Stripe's servers - this will charge the user's card
try {
  $charge = \Stripe\Charge::create(array(
    "amount" => $stripeTotal, // amount in cents, again
    "currency" => "usd",
    "source" => $token,
    "description" => "Charge for ".$firstName." ".$lastName,
	"metadata" => array("State" => $stateAdd, "Zip Code" => $zipAdd)
    ));
} catch(\Stripe\Error\Card $e) {
  // The card has been declined
}
}

// Update Print Shop Balance Total
$sqlCheckPSBalance = '
SELECT User.userId, User.userPsBalance
FROM User
WHERE User.userId = '.$userId.'
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$userPSBalance = $row['userPsBalance'];
}

$newPSBalance = $userPsBalance - $cartPayWithPS;

$sqlUpdatePSBalance = '
UPDATE User
SET User.userPsBalance = '.$newPSBalance.'
WHERE User.userId = '.$userId.'
';
$conn->exec($sqlUpdatePSBalance);

// ----------------------------------
// Send email 

// Get the products purchased
$sql = '
SELECT PrintShopCart.cartId, PrintShopCart.cartConfNo, 
PrintShopCartItem.cartId, PrintShopCartItem.printProjId, 
PrintShopCartItem.qty, PrintShopCartItem.laminated, 
PrintProject.printProjId, 
PrintProject.printProjFile, PrintProject.printProjQty, 
PrintProject.printProdId, 
PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType
FROM PrintShopCart, PrintShopCartItem, PrintProject, PrintProduct
WHERE PrintShopCart.cartId = '.$_SESSION['cartId'].' AND PrintShopCart.cartId =
PrintShopCartItem.cartId AND PrintShopCartItem.printProjId = PrintProject.printProjId AND 
PrintProject.printProdId = PrintProduct.printProdId
';


$rows = $conn->query($sql);
	// Get info needed for email population and UPDATE each print project file name
	// This adds the order confirmation # to the end of the file name
	foreach ($rows as $row) {
	$cartConfNo = $row['cartConfNo'];
	// Original name of file, for use when renaming the PDF
	$file = $row['printProjFile'];
	// Filename -.pdf for renaming in  database 
	$fileName = substr($row['printProjFile'], 0, -9);
	$printProjId = $row['printProjId'];
	$sqlUpdateFileName = '
	UPDATE PrintProject
	SET PrintProject.printProjFile = "'.$fileName.'_'.$cartConfNo.'.pdf"
	WHERE PrintProject.printProjId = '.$printProjId.'
	';
	$conn->exec($sqlUpdateFileName);
	// Update the PDF file name to match
	rename('Output/'.$file, 'Output/'.$fileName.'_'.$cartConfNo.'.pdf');
	}
	
	// Send The Email
	$to = 'scott@fireworksoveramerica.com';
	$to2 = 'customerservice@bassprintsolutions.com';
	$to3 = 'jeff@fireworksoveramerica.com';
	
	$subject = 'PrintShop Order - Order #'.$cartConfNo.' - '.$currDate.'';

	$headers = "From: PrintShopOrders\r\n";
	$headers .= "Reply-To: scott@fireworksoveramerica.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$message = '<h2>Fireworks Over America -- Print Shop Order</h2>
	<p>Print Shop Order #: '.$cartConfNo.'</p><br/>
	<h3>Information</h3>
	<p>Customer Name: '.$firstName.' '.$lastName.'</p>
	<p>Customer Company: '.$_SESSION['shipCompany'].'</p>
	<p>Address: '.$streetAdd.'<br/>
	'.$cityAdd.', '.$stateAdd.'<br/>
	'.$zipAdd.'</p>
	<p>Phone Number: '.$phoneNumber.'</p><br/>
	<h3>Products</h3>
	<table cellpadding="5">
	<tr><td><b>Product</b></td><td><b>Qty</b></td><td><b>Direct Link To File</b></td></tr>
	';
	$rows = $conn->query($sql);
	foreach ($rows as $row) {
		if ($row['laminated'] == 1) {
			$laminated = "Yes";
		} else {
			$laminated = "No";
		}
		$projName = str_replace(' ', '%20', $row['printProjFile']);

		$message .= '
	<tr><td>'.$row['printProdName'].'</td><td>'.$row['qty'].'</td>
	<td><a href="http://fireworksoveramerica.com/CMS/print-shop/Output/'.$projName.'" target="_BLANK">http://fireworksoveramerica.com/CMS/print-shop/Output/'.$projName.'</a><br/>
	';
	if ($row['printProdType'] == "description-card") {
		$message .= 'Laminated? '.$laminated.'<br/>
		Qty of Cards: '.$row['printProjQty'].'</td>';
	} else {
		$message .= '</td>';
	}
	$message .= '
	</tr>
	';
	}
	$message .= '
	</table>
	<p>Special Instructions: '.$_POST['instructions'].'</p>
	<h3>Enter A Tracking Number</h3>
	<p>Click below to enter a tracking number for this order, which will then mark the order as shipped for the customer.<br/>
	<a href="http://www.fireworksoveramerica.com/CMS/print-shop/loginPage.php" target="_BLANK">www.fireworksoveramerica.com/CMS/print-shop/loginPage.php</a></p>
	';
	
	$msg = wordwrap($message,70);
	// Mail to Scott Knox
	mail($to, $subject, $msg, $headers, '-fscott@fireworksoveramerica.com');
	mail($to2, $subject, $msg, $headers, '-fscott@fireworksoveramerica.com');
	mail($to3, $subject, $msg, $headers, '-fscott@fireworksoveramerica.com');

// ----------------------------------
// ----------------------------------
// Send Order Confirmation Email to customer
	// Send The Email
	$to = $email;
	
	$subject = 'PrintShop Order - Order #'.$cartConfNo.' - '.$currDate.'';

	$headers = "From: PrintShopOrders\r\n";
	$headers .= "Reply-To: scott@fireworksoveramerica.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$message = '<h2>Fireworks Over America -- Your Print Shop Order</h2>
	<p>Print Shop Order #: '.$cartConfNo.'</p><br/>
	<h3>Information</h3>
	<p>Name: '.$firstName.' '.$lastName.'</p>
	<p>Company: '.$_SESSION['shipCompany'].'</p>
	<p>Address: '.$streetAdd.'<br/>
	'.$cityAdd.', '.$stateAdd.'<br/>
	'.$zipAdd.'</p>
	<p>Phone Number: '.$phoneNumber.'</p><br/>
	<h3>Products</h3>
	<table cellpadding="5">
	<tr><td><b>Product</b></td><td><b>Qty</b></td><td><b>Direct Link To File</b></td></tr>
	';
	$rows = $conn->query($sql);
	foreach ($rows as $row) {
		if ($row['laminated'] == 1) {
			$laminated = "Yes";
		} else {
			$laminated = "No";
		}
		
		$projName = str_replace(' ', '%20', $row['printProjFile']);

		$message .= '
	<tr><td>'.$row['printProdName'].'</td><td>'.$row['qty'].'</td>
	<td><a href="http://fireworksoveramerica.com/CMS/print-shop/Output/'.$projName.'" target="_BLANK">http://fireworksoveramerica.com/CMS/print-shop/Output/'.$projName.'</a><br/>
	';
	if ($row['printProdType'] == "description-card") {
		$message .= 'Laminated? '.$laminated.'<br/>
		Qty of Cards: '.$row['printProjQty'].'</td>';
	} else {
		$message .= '</td>';
	}
	$message .= '
	</tr>
	';
	}
	$message .= '
	</table>
	<p>Instructions Provided: '.$_POST['instructions'].'</p>
	<h3>Track your order:</h3>
	<p>Click the link below to see your order status and track your order. If 
	you are asked to log in first, log in and then click this link again.<br/>
	<a href="http://www.fireworksoveramerica.com/CMS/print-shop/viewOrder.php?orderNo='.$_SESSION['cartId'].'" target="_BLANK">http://www.fireworksoveramerica.com/CMS/print-shop/viewOrder.php?orderNo='.$_SESSION['cartId'].'</a></p>
	<br/><br/>
	<b>Please call (800) 345-3957 if you experience any problem, or feel free to contact 
	us be email.</b>
	';
	
	$msg = wordwrap($message,70);
	// Mail to Scott Knox
	mail($to, $subject, $msg, $headers, '-fscott@fireworksoveramerica.com');

//------------------------------------
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
<meta name="robots" content="noindex">
  <title>Fireworks Over America - Print Shop</title>
  <meta name="description" content="Create and print high quality marketing materials for your business.">
  <meta name="author" content="Scott Knox">

  <link rel="stylesheet" href="Styles/layout.css">
  <script src="Scripts/jquery-1.11.3.min.js"></script>
  <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>

<header>
<img class="headerLogo" src="Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop</h1>
<div id="userAccount">
<img class="userPhoto" src="../../userPhotos/<?php echo $photoUrl ?>" alt="user-photo" />
<p class="usernameText">Hello, <br/><?php echo $firstName." ".$lastName ?><br/><br/>
<a href="logout.php">Log Out</a>
</p>
</div>
</header>
<nav>
<ul id="navLinks">
<a class="navLink" href="index.php"><li>Home</li></a>
<a class="navLink" href="index.php"><li>Create</li></a>
<a class="navLink" href="projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="myAccount.php"><li>My Account</li></a>
<a class="navLink" href="resources.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="index.php" class="sideLink"><li>Your Projects</li></a>
<a href="shipping.php" class="sideLink"><li>Step 1: Shipping</li></a>
<a href="billing.php" class="sideLink"><li>Step 2: Billing</li></a>
<a href="projectsHome.php" class="sideLink"><li class="active-step">Step 3: Confirmation</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Order Confirmation</h2>
<hr/>
<p class="success-text">Success</p>
</div>

<p>Your order is complete. You will receive a receipt in the email currently on your account with your order details.<br/><br/>
We appreciate your business and having you as a continued customer. If you experience any problems, or have questions/comments
feel free to email us at:<br/><br/>
<b>scott@fireworksoveramerica.com</b><br/><br/>
Click continue to return to your account
</p>

<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>

</div>

</div>

<script>
// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
	window.location.replace("projectsHome.php");	
});
</script>

</body>
</html>
