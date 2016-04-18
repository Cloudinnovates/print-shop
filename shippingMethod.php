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

// Gather input from the shipping form
$_SESSION['shipCompany'] = $_POST['shipCompany'];
$_SESSION['shipName'] = $_POST['shipName'];
$_SESSION['shipStreet'] = $_POST['shipStreet'];
$_SESSION['shipCity'] = $_POST['shipCity'];
$_SESSION['shipState'] = $_POST['shipState'];
$_SESSION['shipZipCode'] = $_POST['shipZipCode'];
$_SESSION['shipPhone'] = $_POST['shipPhone'];
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
<a href="shipping.php" class="sideLink"><li class="active-step">Step 1: Shipping</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Choose A Shipping Method</h2>
<hr/>
<p>Choose a shipping method for your order. </p>
</div>
<!-- Customer shopping carts are inserted here -->
<div id="user-cart-title"><img src="Images/cart-icon.png" alt="cart-icon" /> Your Cart</div>
<div id="user-cart-list">
<div id="start-order"></div>
<?php
// Checks for the main cart information
$sqlCheckCart = '
SELECT PrintShopCart.cartId,PrintShopCart.cartTotalPrice, PrintShopCart.cartTotalWeight,
UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$cartTotal = $row['cartTotalPrice'];
	$cartWeight = $row['cartTotalWeight'];
}

// Get Print projects that are currently in the users cart as well as the quantities and other information 
// about the project.
$sqlGetCartItems = '
SELECT PrintShopCartItem.cartId, PrintShopCartItem.printProjId, PrintShopCartItem.skuId, PrintShopCartItem.qty,
PrintShopCartItem.price,
PrintProject.printProjId, PrintProject.printProdId, PrintProject.printProjName, 
PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType, 
PrintProductSku.skuId, PrintProductSku.skuPrice
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
	$printProdType = $row['printProdType'];
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
<p id="cart-total-line"><strong>Subtotal</strong>: $<span id="subtotal"><?php echo $cartTotal ?></span>
</p>
</div>
<br/><br/>

<?php
// Get the correct shipping cost. This is done by checking the zone the state the user chose is in
// and comapring it (and the cart weight) against the Fex Ex Rate table. 
$sqlGetRate = 'SELECT *
FROM FedExZone, FedExRate
WHERE FedExZone.zoneState = "'.$_SESSION['shipState'].'"
AND FedExZone.zoneNum = FedExRate.rateZone';

$fedExRates = $conn->query($sqlGetRate);

foreach ($fedExRates as $rate) {
	if ($cartWeight == $rate['rateWeight']) {
		if ($printProdType == "banner" || $printProdType == "editable-banner") {
			$shippingCost = number_format($rate['rateCost'] + 12.00, 2);
		} else {
			$shippingCost = number_format($rate['rateCost'] + 3.00, 2);
		}
		break;
	}
}
?>
<form id="shipping" action="billing.php" method="POST">
<fieldset>
<legend><b>Choose A Shipping Method:</b></legend>
<p>Choose a method: 
<select id="shipMethod" name="shipMethod">
<option value="<?php echo $shippingCost; ?>">FedEx Ground - $<?php echo $shippingCost; ?></option>
<?php 
if ($userId = 23 || $userId = 24 || $userId = 28 || $userId = 438 || $userId = 364) {
	echo '<option value="0.00">Free Pickup - $0.00</option>';
}
?>
</select>
</p>

<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
</fieldset>
</form>

</div>

</div>


<script>
// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
		$("#shipping").submit();
});
</script>
</body>
</html>
