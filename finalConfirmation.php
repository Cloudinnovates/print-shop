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
}
if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}


$printShopCost = $_POST['printShopCost'];
$creditCardCost = $_POST['creditCardCost'];

if ($printShopCost === null) {
$printShopCost = 0;
}
if ($creditCardCost === null) {
$creditCardCost == 0;
}
// Update the amount to pay with Print Shop and a Credit Card.
$sqlUpdateCart = '
UPDATE PrintShopCart, UserToPrintShopCart
SET PrintShopCart.cartPayWithPS = '.$printShopCost.', PrintShopCart.cartPayWithCard = '.$creditCardCost.'
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId AND PrintShopCart.cartId = '.$_SESSION['cartId'].'
';
$conn->exec($sqlUpdateCart);
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
<!-- Set Stripe Publishable Key -->
<script type="text/javascript">
  // This identifies your website in the createToken call below
  Stripe.setPublishableKey("pk_test_r9w5S2ZQxzKMY3x4ewl0fT27");
</script>

<header>
<img class="headerLogo" src="Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop</h1>
<div id="userAccount">
<img class="userPhoto" src="../../../userPhotos/<?php 
if (!($photoUrl)) {
echo 'defaultPhotoSmall.jpg';
}
else {
echo $photoUrl; 	
}
?>" alt="user-photo" />
<p class="usernameText">Hello, <br/><?php echo $firstName." ".$lastName ?><br/><br/>
<a href="logout.php">Log Out</a>
</p>
</div>
</header>
<nav>
<ul id="navLinks">
<a class="navLink" href="index.php"><li>Home</li></a>
<a class="navLink" href="create.php"><li>Create</li></a>
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
<a href="billing.php" class="sideLink"><li class="active-step">Step 2: Billing</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Confirm Order Details</h2>
<hr/>
<p>Before completing your order, review the following information carefully. Read the Terms and Conditions,
and accept them by putting a check in the box to the left. Your order will be printed within 72 hours.
<br/>
If your Print Shop Balance did not cover the full order cost, click "Pay With Card" to pay the remaining balance 
and finalize your order. 
</p>
</div><br/>
<!-- Customer shopping carts are inserted here -->
<div id="user-cart-title"><img src="Images/cart-icon.png" alt="cart-icon" /> Your Cart</div>
<div id="user-cart-list">
<div id="start-order"></div>
<?php
// Gets shipping info entered from session variables
$shipCompany = $_SESSION['shipCompany'];
$shipName = $_SESSION['shipName'];
$shipStreet = $_SESSION['shipStreet'];
$shipCity = $_SESSION['shipCity'];
$shipState = $_SESSION['shipState'];
$shipZipCode = $_SESSION['shipZipCode'];
$shipPhone = $_SESSION['shipPhone'];


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
	$orderTotalString = number_format($orderTotal, 2);
	$stripeTotal = $cartPayWithCard*100;
}

// Get Print projects that are currently in the users cart as well as the quantities and other information 
// about the project.
$sqlGetCartItems = '
SELECT PrintShopCartItem.cartId, PrintShopCartItem.printProjId, PrintShopCartItem.skuId, PrintShopCartItem.qty,
PrintShopCartItem.price,
PrintProject.printProjId, PrintProject.printProdId, PrintProject.printProjName, 
PrintProduct.printProdId, PrintProduct.printProdName, PrintProductSku.skuId, PrintProductSku.skuPrice
FROM PrintShopCartItem, PrintProject, PrintProduct, PrintProductSku
WHERE PrintShopCartItem.cartId = '.$_SESSION['cartId'].' AND PrintShopCartItem.printProjId = 
PrintProject.printProjId AND PrintProject.printProdId = PrintProduct.printProdId AND 
PrintProductSku.skuId = PrintShopCartItem.skuId
';

$rows = $conn->query($sqlGetCartItems);
// Echo through each print project in the cart
foreach ($rows as $row) {
	$qty = $row['qty'];
	$printProjId = $row['printProjId'];
	$printProjName = $row['printProjName'];
	$printProdName = $row['printProdName'];
	$productPrice = $row['price'];
	
	echo '
<div id="cart'.$printProjId.'"class="user-cart">
<div class="user-cart-desc">
<p>'.$printProjName.' - '.$printProdName.'</p>
<strong>Quantity</strong>: '.$qty.'</p>
</div>
<div class="user-cart-options">
<p><strong>Price</strong>: $<span class="price">'.$productPrice.'</span><br/>
</p>
</div>
</div>
	';

}
?>
<hr/>
<p id="cart-total-line">
<strong>Shipping</strong>: $<?php echo $cartShipping ?><br/><br/>
<strong>Order Total</strong>: $<span id="subtotal"><?php echo $orderTotalString ?></span>
</p>
</div>
<br/>


<form id="payment-form" action="charge.php" method="POST">
<fieldset>
<legend><b>Order Details:</b></legend>
<b>Shipping Information</b>
<p>
Company Name: <b><?php echo $shipCompany; ?></b><br/>
Name: <b><?php echo $shipName; ?></b><br/>
Street Address: <b><?php echo $shipStreet; ?></b><br/>
City, State: <b><?php echo $shipCity.", ".$shipState; ?></b><br/>
Zip Code: <b><?php echo $shipZipCode; ?></b><br/><br/>
Phone Number: <b><?php echo $shipPhone; ?></b>
</p>

<hr/>

<b>Terms and Conditions</b>
<p>
Please proofread the high-resolution self-print file(s) before submitting this order. By checking the terms and conditions box, you are confirming the following:<br/><br/>
<ol>
<li>You have proofed the document(s) and are completely satisfied with the layout, photography and content for all pieces contained in the order.</li>
<li>Provided components are original or have sufficient usage rights for application and do not infringe the copyright rights of any person or entity.</li>
<li>Fireworks Over America, and any parties involved in the development of Print Shop assume no liability for errors, omissions or copyright infringement on the part of the user.</li>
</ol>
<input id="agree" type="checkbox" name="agree" /> <b>I Accept The Terms and Conditions</b>
</p>

<hr/>
<b>Special Instructions?</b><br/>
<textarea name="instructions" rows="10" cols="40"></textarea><br/><br/>
<b>Payment Details</b>
<p>
Amount to deduct from Print Shop balance: <b>$<?php echo $cartPayWithPS; ?></b><br/>
Amount to pay with credit card: <b>$<?php echo $cartPayWithCard; ?></b><br/><br/>
<p>You must accept the terms and conditions to continue.</p>

<?php 
// If user has a balance remaining after Print Shop applied, allow to pay with card. If not, give continue button.

if ($cartPayWithCard > 0) {
	echo '
	<div id="payment-button">
    <span class="important-text">Click below to enter your credit card details. <br/><br/>The window that opens will allow you
    to enter your information over a secure connection. <b>Be sure you are ready to place your order before clicking the "Pay" button</b>.</span>
    </p>
    <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_r9w5S2ZQxzKMY3x4ewl0fT27"
    data-amount="<?php echo $stripeTotal; ?>"
    data-name="Fireworks Over America"
    data-description="Print Shop Order"
    data-image="store-logo.jpg"
    data-locale="auto">
    </script>
    </div>
	';
	// This variable tells the next page whether or not to use Stripe to process a payment.
	$_SESSION['useStripe'] = 1;
}
else {
	echo '
	<div id="payment-button">
    <span class="important-text">Your Print Shop Balance covered the full order amount. 
	Please click continue to finalize your order.</span>
    </p>
    <a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
    </div>
	';
	// Equal to 0, no payment is necessary.
	$_SESSION['useStripe'] = 0;
}

?>


  <script>
$("#payment-button").hide();

$("#agree").change(function() {
	if (this.checked) {
		$("#payment-button").show();
	}
	else {
		$("#payment-button").hide();
	}

});

// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
	$("#payment-form").submit();	
});
</script>
</fieldset>
</form>

</div>

</div>



</body>
</html>
