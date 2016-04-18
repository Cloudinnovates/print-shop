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

<div id="rightContent">
<div id="page-description">
<h2>My Account</h2>
<hr/>
<h3>Order History</h3>
<p>Below is your current order history.</p>
</div>

<table cellpadding="5">
<tr><td>Confirmation Number</td><td>Date Placed</td><td>View</td></tr>
<?php
// Show the customer order list
$sqlGetCarts = '
SELECT UserToPrintShopCart.userId, UserToPrintShopCart.cartId, 
PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized,
PrintShopCart.cartDateFinalized, PrintShopCart.cartIsDeleted
FROM UserToPrintShopCart, PrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND UserToPrintShopCart.cartId
= PrintShopCart.cartId AND PrintShopCart.cartIsFinalized = 1 AND PrintShopCart.cartIsDeleted = 0
';
$rows = $conn->query($sqlGetCarts);

foreach ($rows as $row) {
	echo '<tr><td>'.$row['cartConfNo'].'</td><td>'.$row['cartDateFinalized'].'</td>
	<td><a href="viewOrder.php?orderNo='.$row['cartId'].'">View Order</a></td></tr>';
}
?>
</table>
</div>
</div>

<script>

</script>
</body>
</html>
