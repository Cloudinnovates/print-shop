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
<h2>Enter Shipping Info</h2>
<hr/>
<p>Enter your shipping info below. You may choose to use your address on file 
with Fireworks Over America, or enter a different address.</p>

</div>
<!-- Customer shopping carts are inserted here -->
<div id="user-cart-title"><img src="Images/cart-icon.png" alt="cart-icon" /> Your Cart</div>
<div id="user-cart-list">
<div id="start-order"></div>
<?php
// Checks for the main cart information
$sqlCheckCart = '
SELECT PrintShopCart.cartId,PrintShopCart.cartTotalPrice,
UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$cartTotal = $row['cartTotalPrice'];
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
<p id="cart-total-line"><strong>Subtotal</strong>: $<span id="subtotal"><?php echo $cartTotal ?></span>
</p>
</div>
<br/><br/>

<p style="color:red; font-weight:bold;"></p>

<form id="shipping" action="shippingMethod.php" method="POST">
<fieldset>
<legend><b>Enter Your Address</b></legend>
<input type="checkbox" id="useCurrentAddress" name="useCurrentAddress" value="1">Use Address On File?<br/>
<p id="addressListing">
<span id="companyName"><?php echo $company; ?></span><br/><br/>
<span id="fullName"><?php echo $firstName." ".$lastName; ?></span>
<br/><br/>
<span id="streetAdd">
<?php echo $streetAdd."</span><br/><span id='cityAdd'>".$cityAdd."</span>, <span id='stateAdd'>".$stateAdd."</span> <span id='zipAdd'>".$zipAdd."</span><br/><br/><span id='phoneAdd'>".$phoneNumber."</span>" ?>
</p>
<hr/>
<h3>Enter The Ship-To Address:</h3>
<label for="shipCompany">Company Name:</label><br/>
<input type="text" id="shipCompany" name="shipCompany" size="40"/><br/><br/>
<label for="shipName">Full Name:</label><br/>
<input type="text" id="shipName" name="shipName" size="40"/><br/><br/>
<label for="shipStreet">Street Address:</label><br/>
<input type="text" id="shipStreet" name="shipStreet" size="40"/><br/><br/>
<label for="shipCity">City:</label><br/>
<input type="text" id="shipCity" name="shipCity" size="40"/><br/><br/>
<label for="shipState">State:</label><br/>
<select id="shipState" name="shipState">
	<option value="AL">Alabama</option>
	<option value="AZ">Arizona</option>
	<option value="AR">Arkansas</option>
	<option value="CA">California</option>
	<option value="CO">Colorado</option>
	<option value="CT">Connecticut</option>
	<option value="DE">Delaware</option>
	<option value="DC">District Of Columbia</option>
	<option value="FL">Florida</option>
	<option value="GA">Georgia</option>
	<option value="ID">Idaho</option>
	<option value="IL">Illinois</option>
	<option value="IN">Indiana</option>
	<option value="IA">Iowa</option>
	<option value="KS">Kansas</option>
	<option value="KY">Kentucky</option>
	<option value="LA">Louisiana</option>
	<option value="ME">Maine</option>
	<option value="MD">Maryland</option>
	<option value="MA">Massachusetts</option>
	<option value="MI">Michigan</option>
	<option value="MN">Minnesota</option>
	<option value="MS">Mississippi</option>
	<option value="MO">Missouri</option>
	<option value="MT">Montana</option>
	<option value="NE">Nebraska</option>
	<option value="NV">Nevada</option>
	<option value="NH">New Hampshire</option>
	<option value="NJ">New Jersey</option>
	<option value="NM">New Mexico</option>
	<option value="NY">New York</option>
	<option value="NC">North Carolina</option>
	<option value="ND">North Dakota</option>
	<option value="OH">Ohio</option>
	<option value="OK">Oklahoma</option>
	<option value="OR">Oregon</option>
	<option value="PA">Pennsylvania</option>
	<option value="RI">Rhode Island</option>
	<option value="SC">South Carolina</option>
	<option value="SD">South Dakota</option>
	<option value="TN">Tennessee</option>
	<option value="TX">Texas</option>
	<option value="UT">Utah</option>
	<option value="VT">Vermont</option>
	<option value="VA">Virginia</option>
	<option value="WA">Washington</option>
	<option value="WV">West Virginia</option>
	<option value="WI">Wisconsin</option>
	<option value="WY">Wyoming</option>
</select>	<br/><br/>		
<label for="shipZipCode">Zip Code:</label><br/>
<input type="text" id="shipZipCode" name="shipZipCode" size="10"/><br/><br/>
<label for="shipPhone">Phone Number:</label><br/>
<input type="text" id="shipPhone" name="shipPhone" size="15"/><br/><br/>

<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
</fieldset>
</form>

</div>

</div>


<script>
$('#useCurrentAddress').on('click', function() {
	if ($("#useCurrentAddress").is(":checked")) {
	var shipCompany = $("#companyName").text();
	var shipName = $("#fullName").text();
	var shipStreetAdd = $("#streetAdd").text();
	var shipCityAdd = $("#cityAdd").text();
	var shipStateAdd = $("#stateAdd").text();
	var shipZipCodeAdd = $("#zipAdd").text();
	var shipPhoneAdd = $("#phoneAdd").text();
	
	$("#shipCompany").val(shipCompany);	
	$("#shipName").val(shipName);	
	$("#shipStreet").val(shipStreetAdd);
	$("#shipCity").val(shipCityAdd);
	$("#shipState").val(shipStateAdd).change();
	$("#shipZipCode").val(shipZipCodeAdd);
	$("#shipPhone").val(shipPhoneAdd);
	}
	else {
		$("#shipping").trigger("reset");
	}

});

// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
	var empty = "";
	var company = $("#shipCompany").val();
	var name = $("#shipName").val();
	var street = $("#shipStreet").val();
	var city = $("#shipCity").val();
	var zip = $("#shipZipCode").val();
	var phone = $("#shipPhone").val();
	
	if (empty != company && empty != name && empty != street
	&& empty != city && empty != phone) {
		$("#shipping").submit();
	} else {
		alert("You must fill in all fields for your shipping address.");
	}

});
</script>
</body>
</html>
