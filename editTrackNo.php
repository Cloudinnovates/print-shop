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

  <title>Fireworks Over America - Print Shop</title>
  <meta name="description" content="Create and print high quality marketing materials for your business.">
  <meta name="author" content="Scott Knox">
<meta name="robots" content="noindex">
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
<?php
$pickup = $_GET['pickup'];

if ($pickup == "true") {
	echo '<h2>Verify Customer Pickup</h2>
<p>Check the box below and hit "submit" to mark that customer picked up the order.</p>';
} else {
	echo '<h2>Edit a Tracking Number</h2>
<p>Enter the tracking number for the order below.</p>';
}
?>

<hr/>
</div>
<?php
$orderNo = $_GET['orderNo'];


$sql = '
SELECT PrintShopCart.cartId, PrintShopCart.cartConfNo, 
PrintShopCart.cartTrackingNo, PrintShopCart.cartShippedDate
FROM PrintShopCart
WHERE PrintShopCart.cartId = '.$orderNo.'
';

$rows = $conn->query($sql);
foreach ($rows as $row) {
	$cartConfNo = $row['cartConfNo'];
	$cartTrackingNo = $row['cartTrackingNo'];
	$cartShippedDate = $row['cartShippedDate'];
}

if ($pickup == "true") {
	echo '
	<form action="updateTracking.php?orderNo='.$orderNo.'" method="POST">
	<h2>Order #: '.$cartConfNo.'</h2>
	<input type="checkbox" name="pickedUp" />Picked Up?<Br/><br/>
	<input type="submit" name="submit" value="Submit" />
	</form>
	';
} else {
echo '
<form action="updateTracking.php?orderNo='.$orderNo.'" method="POST">
<h2>Order #: '.$cartConfNo.'</h2>
<label for="newTrackNo">Tracking Number:</label>
<input type="text" name="newTrackNo" value="'.$cartTrackingNo.'" />
<input type="submit" name="submit" value="Submit" />
</form>
';
}

?>
</div>

</div>


<script>

</script>
</body>
</html>
