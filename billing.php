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

if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}

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
<h2>Select Payment Method</h2>
<hr/>
<p>Select a method of payment for this order and complete the necessary information. 
Special instructions for shipping can be included in the space provided. Click CONTINUE to 
submit this order and view your order confirmation.</p>
</div><br/>
<!-- Customer shopping carts are inserted here -->
<div id="user-cart-title"><img src="Images/cart-icon.png" alt="cart-icon" /> Your Cart</div>
<div id="user-cart-list">
<div id="start-order"></div>
<?php
$shipCost = $_POST['shipMethod'];
// Update the shipping in database
$sql = 'UPDATE UserToPrintShopCart, PrintShopCart 
SET cartShipping = '.$shipCost.'
WHERE UserToPrintShopCart.userId = '.$userId.' AND UserToPrintShopCart.cartId = '.$_SESSION['cartId'].'
AND UserToPrintShopCart.cartId = PrintShopCart.cartId';

$conn->exec($sql);

// Checks for the main cart information
$sqlCheckCart = '
SELECT PrintShopCart.cartId, PrintShopCart.cartShipping, PrintShopCart.cartTotalPrice,
UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$cartShipping = number_format($row['cartShipping'], 2);
	$cartTotal = $row['cartTotalPrice'];
	$orderTotalString = number_format($cartShipping + $cartTotal, 2);
	$orderTotal = $cartShipping + $cartTotal;
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
<div id="cart'.$printProjId.'" class="user-cart">
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


<form id="payment-form" action="finalConfirmation.php" method="POST">
<fieldset>
<legend><b>Payment Options:</b></legend>
<p>
Payments can be made using a credit card and your remaining Print Shop account balance.
</p>
<h3>Order Total: $<span id="cart-total"><?php echo $orderTotal ?></span></h3>
<hr/>
<span class="payment-errors"></span>
<strong>Apply Your Print Shop Balance?</strong><br/>
<p>Amount to use: 
$<input type="text" id="printShopCost" name="printShopCost" size="5" value="0.00"/> from your balance of 
<strong>$<span id="psBalance"><?php echo $userPsBalance ?></span></strong>
</p>
<hr/>

<strong>Credit Card</strong>
<p>Amount to pay: 
$<input type="text" id="credit-card-amount" name="creditCardCost" size="5" readonly/></p>
<img src="Images/cardLogos.png" alt="credit-card-logos" /><br/>

<p>Please click continue to review your order. You may then finalize your order, and enter payment details if necessary.</p>
<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
</fieldset>

</form>

</div>

</div>

<script>
var originalTotal = parseFloat($("#cart-total").text());
var psBalance = parseFloat($("#psBalance").text());
// These variables are set when the user has correctly entered their information.
var cardCorrect = false;
var cvcCorrect = false;
var expirDateCorrect = false;
// Hide the "info entered correctly span element" Show later when information is all entered correctly.
$("#infoCorrect").hide();

// Set the credit card amount to the amount due.
$("#credit-card-amount").val(originalTotal);

// If PS Balance is 0, disable the form fields for value entry.
if (psBalance == 0.00) {
	$("#printShopCost").prop("disabled", true);
	$("#credit-card-amount").val(originalTotal);
}
// Code for processing the current balance.
// Clear the print shop cost field when focused on.
$("#printShopCost").on("focus", function() {
	$("#printShopCost").val("");
});
$("#printShopCost").on("blur", function() {
	if (!$(this).val()) {
		$("#printShopCost").val("0.00");
		$("#cart-total").text(originalTotal);
		$("#credit-card-amount").val(originalTotal);
	}
});
// Change order total as you type
$("#printShopCost").on("input", function() {
	if ($("#printShopCost").val() > psBalance) {
		$("#printShopCost").val(psBalance);
	}
	if ($("#printShopCost").val() > originalTotal) {
		$("#printShopCost").val(originalTotal);
	}
		var currentTotal = originalTotal;
		var printShopAmount = parseFloat($("#printShopCost").val());
		var newTotal = currentTotal - printShopAmount;
		newTotal = newTotal.toFixed(2);
		$("#credit-card-amount").val(newTotal);
});

// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
		$("#payment-form").submit();
});
 
</script>
</body>
</html>
