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
<a href="myAccount.php" class="sideLink"><li class="active-step">My Account</li></a>
<a href="myProfile.php" class="sideLink"><li>My Profile</li></a>
<a href="userImages.php" class="sideLink"><li>User Image</li></a>
</ul>
</div>
<?php
// Get order information

// Get Cart Information
$sqlGetCart = '
SELECT UserToPrintShopCart.userId, UserToPrintShopCart.cartId, 
PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized,
PrintShopCart.cartDateFinalized, PrintShopCart.cartShipping, PrintShopCart.cartTotalPrice,
PrintShopCart.cartPayWithPS, PrintShopCart.cartPayWithCard, PrintShopCart.cartTrackingNo,
PrintShopCart.cartShippedDate
FROM UserToPrintShopCart, PrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND UserToPrintShopCart.cartId
= '.$_GET['orderNo'].' AND UserToPrintShopCart.cartId = PrintShopCart.cartId 
AND PrintShopCart.cartIsFinalized = 1
';
$rows = $conn->query($sqlGetCart);

foreach ($rows as $row) {
	$cartId = $row['cartId'];
	$cartConfNo = $row['cartConfNo'];
	$cartIsFinalized = $row['cartIsFinalized'];
	$cartDateFinalized = $row['cartDateFinalized'];
	$cartShipping = $row['cartShipping'];
	$cartTotalPrice = $row['cartTotalPrice'];
	$cartPayWithPS = $row['cartPayWithPS'];
	$cartPayWithCard = $row['cartPayWithCard'];
	$cartTrackingNo = $row['cartTrackingNo'];
	$cartShippedDate = $row['cartShippedDate'];
}
// Get User destination address and print shop balance at time of order
$sqlGetUserInfo = '
SELECT UserToPrintShopCart.userId, UserToPrintShopCart.cartId, 
User.userId, User.userEmail, User.userFirstName, User.userLastName,
User.userPhone, User.userPsBalance, UserAddress.addStreet, UserAddress.addCity,
UserAddress.addState, UserAddress.addZip
FROM UserToPrintShopCart, User, UserAddress
WHERE UserToPrintShopCart.userId = '.$userId.' AND UserToPrintShopCart.cartId
= '.$_GET['orderNo'].' AND UserToPrintShopCart.userId = User.userId AND User.userId =
UserAddress.userId
';

$rows = $conn->query($sqlGetUserInfo);

foreach ($rows as $row) {
	$userEmail = $row['userEmail'];
	$userFirstName = $row['userFirstName'];
	$userLastName = $row['userLastName'];
	$userPhone = $row['userPhone'];
	$userPsBalance = $row['userPsBalance'];
	$userStreet = $row['addStreet'];
	$userCity = $row['addCity'];
	$userState = $row['addState'];
	$userZip = $row['addZip'];
}

?>
<div id="rightContent">
<div id="page-description">
<h2>Order # <?php echo $cartConfNo ?></h2>
<h2>Order Date: <?php echo $cartDateFinalized ?></h2>
<a href="myAccount.php">Back To Your Account</a>
<hr/>
</div>
<h2>Order Summary</h2>
<table cellpadding="5">
<tr><td><strong>Delivery Address</strong></td><td><strong>Payment Information</strong></td><td></td></tr>
<tr><td><?php echo $userFirstName.' '.$userLastName ?>
<br/><?php echo $userStreet ?><br/>
<?php echo $userCity.', '.$userState.' '.$userZip ?></td>
<td>Paid From PrintShop Balance: $<?php echo $cartPayWithPS ?><br/>
<br/>
Paid With Credit Card $<?php echo $cartPayWithCard ?>
</td><td></td></tr>
<tr><td colspan="3"><strong>Projects Purchased</strong></td></tr>
<tr><td>Project Name</td><td>Quantity</td><td>Price</td></tr>
<?php
// Get the projects that are in the current cart
$sqlGetProjectDetails = '
SELECT UserToPrintShopCart.userId, UserToPrintShopCart.cartId, 
PrintShopCartItem.cartId, PrintShopCartItem.printProjId, 
PrintShopCartItem.price, PrintShopCartItem.laminated,
PrintProject.printProjId, PrintProject.printProdId,
PrintProject.printProjName, PrintProject.printProjFile,
PrintProject.printProjQty, PrintProduct.printProdId, PrintProduct.printProdName
FROM UserToPrintShopCart, PrintShopCartItem, PrintProject, PrintProduct
WHERE UserToPrintShopCart.userId = '.$userId.' AND UserToPrintShopCart.cartId
= '.$_GET['orderNo'].' AND UserToPrintShopCart.cartId = PrintShopCartItem.cartId
AND PrintShopCartItem.printProjId = PrintProject.printProjId AND PrintProject.printProdId 
= PrintProduct.printProdId
';

$rows = $conn->query($sqlGetProjectDetails);

foreach ($rows as $row) {
	echo '<tr><td><a target="_blank" href="Output/'.$row['printProjFile'].'">'.$row['printProjName'].'</a><br/>
'.$row['printProdName'].'<br/>
Laminated? ';

if ($row['laminated'] == 1) {
	echo 'Yes';
} else {
	echo 'No';
}

echo '</td>
<td>'.$row['printProjQty'].'</td><td>$'.$row['price'].'</td></tr>';
}
?>
<tr><td colspan="2"><strong>Shipping Information</strong></td><td></td></tr>
<tr><td>Status: <?php 
if (empty($cartShippedDate)) {
	echo '<strong>Processing Order</strong>';
}
else {
	if ($row['cartTrackingNo'] == 999999) {
		echo '<strong>Picked Up</strong>';
	} else {
		echo '<strong>Shipped</strong>';	
	}
}
?><br/>
Shipped Date: <?php echo $cartShippedDate ?></td>
<td>Shipment Cost: $<?php echo $cartShipping ?><br/>
Service: <?php if ($cartShipping === "0.00") {
	echo 'Free Pickup';
}else {
	echo 'FedEx Ground';
}?><br/>
Tracking Number: <a target="_blank" href="https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=<?php echo $cartTrackingNo; ?>&cntry_code=us"
><?php 
if ($cartTrackingNo == 0 || $cartTrackingNo == 999999) {
	echo '';
}
else {
	echo $cartTrackingNo; 
}
?></a></td><td></td></tr>
</table>
</div>
</div>

<script>

</script>
</body>
</html>
