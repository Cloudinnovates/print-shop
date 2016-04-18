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

if (!($acctType == "Admin" || $acctType == "Print")) {
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
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="printerPortal.php" class="sideLink"><li class="active-step">Shipping Management</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>Printer Shipping Management</h2>
<hr/>
<h2>Not Shipped</h2>
<p>Below are the orders that have not had tracking numbers entered. These orders are marked as 
"in processing" to the customer and will be marked as "shipped" once a tracking number has been entered.</p>
</div>

<table cellpadding="5">
<tr><td>Order #</td><td>Order Date</td><td>First Name</td><td>Last Name</td><td>Options</td></tr>
<?php
// Show the customer order list
$sqlGetCarts = '
SELECT User.userId, User.userFirstName, User.userLastName, 
UserToPrintShopCart.userId, UserToPrintShopCart.cartId,
PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized,
PrintShopCart.cartDateFinalized, PrintShopCart.cartShipping, PrintShopCart.cartTrackingNo, 
PrintShopCart.cartShippedDate, PrintShopCart.cartIsDeleted
FROM User, UserToPrintShopCart, PrintShopCart
WHERE User.userId = UserToPrintShopCart.userId AND UserToPrintShopCart.cartId = PrintShopCart.cartId AND
PrintShopCart.cartIsFinalized = 1 AND PrintShopCart.cartTrackingNo = 0 AND PrintShopCart.cartIsDeleted = 0
';
$rows = $conn->query($sqlGetCarts);

foreach ($rows as $row) {
	echo '<tr><td>'.$row['cartConfNo'].'</td><td>'.$row['cartDateFinalized'].'</td>
	<td>'.$row['userFirstName'].'</td><td>'.$row['userLastName'].'</td>
	<td><a href="viewOrderPrinter.php?orderNo='.$row['cartId'].'" target="_blank">View Order</a> | 
	';
	if ($row['cartShipping'] == 0.00) {
		echo '<a href="editTrackNo.php?orderNo='.$row['cartId'].'&pickup=true" target="_blank">Verify Customer Pick Up</a></td></tr>';	
	} else {
		echo '<a href="editTrackNo.php?orderNo='.$row['cartId'].'" target="_blank">Enter Tracking No</a></td></tr>';	
	}
}
?>
</table>

<h2>Shipped</h2>
<p>Below are the orders that have been marked as shipped. These have had tracking numbers entered
and are marked as "shipped" to the customer.</p>
<table cellpadding="5">
<tr><td>Order #</td><td>Order Date</td><td>First Name</td><td>Last Name</td><td>Options</td></tr>
<?php
// Show the customer order list
$sqlGetCarts = '
SELECT User.userId, User.userFirstName, User.userLastName, 
UserToPrintShopCart.userId, UserToPrintShopCart.cartId,
PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized,
PrintShopCart.cartDateFinalized, PrintShopCart.cartTrackingNo, PrintShopCart.cartShippedDate,
PrintShopCart.cartIsDeleted
FROM User, UserToPrintShopCart, PrintShopCart
WHERE User.userId = UserToPrintShopCart.userId AND UserToPrintShopCart.cartId = PrintShopCart.cartId AND
PrintShopCart.cartIsFinalized = 1 AND PrintShopCart.cartTrackingNo <> 0 AND PrintShopCart.cartIsDeleted = 0
';
$rows = $conn->query($sqlGetCarts);

foreach ($rows as $row) {
	echo '<tr><td>'.$row['cartConfNo'].'</td><td>'.$row['cartDateFinalized'].'</td>
	<td>'.$row['userFirstName'].'</td><td>'.$row['userLastName'].'</td>
	<td><a href="viewOrderPrinter.php?orderNo='.$row['cartId'].'" target="_blank">View Order</a> | 
	';
	if ($row['cartTrackingNo'] == 999999) {
		echo 'Picked Up</td></tr>';

	} else {
		 echo '<a href="editTrackNo.php?orderNo='.$row['cartId'].'" target="_blank">Edit Tracking No</a></td></tr>';
	}
}
?>
</table>
</div>

</div>


<script>

</script>
</body>
</html>
