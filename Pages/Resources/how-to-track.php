<?php
session_start();
include '../../../../functions.php';

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

  <link rel="stylesheet" href="../../Styles/layout.css">
    <script src="../../Scripts/jquery-1.11.3.min.js"></script>
  
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
<header>
<img class="headerLogo" src="../../Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop</h1>
<div id="userAccount">
<img class="userPhoto" src="../../../../userPhotos/<?php 
if (!($photoUrl)) {
echo 'defaultPhotoSmall.jpg';
}
else {
echo $photoUrl; 	
}
?>" alt="user-photo" />
<p class="usernameText">Hello, <br/><?php echo $firstName." ".$lastName ?><br/><br/>
<a href="../../logout.php">Log Out</a>
</p>
</div>
</header>
<nav>
<ul id="navLinks">
<a class="navLink" href="../../index.php"><li>Home</li></a>
<a class="navLink" href="../../create.php"><li>Create</li></a>
<a class="navLink" href="../../projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="../../myAccount.php"><li>My Account</li></a>
<a class="navLink" href="../../resources.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="../../resources.php" class="sideLink"><li class="active-step">Resources</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>How To Track Your Order</h2>
<p>See the status of your order, and track the delivery</p>
<hr/>
<img src="Images/track-1.jpg" alt="track-pic-1" />
<p>First, go to "My Account."</p>
<img src="Images/track-2.jpg" alt="track-pic-2" />
<p>Here, you can see all of the orders you have placed with Print Shop. Click on the
"View Order" button to see more details and status on an order.</p>
<img src="Images/track-3.jpg" alt="track-pic-3" />
<p>You can see the full details of your order on the view order page. 
Here, you can click the project name to see the project that was ordered
and also the tracking number to track the deliver through Fed Ex. By clicking 
the tracking number, you will be taken to Fed Ex's website to see delivery 
status for the order.</p>

<a href="#">Back To Top</a>
</div>

</div>
</div>

<script>

</script>
</body>
</html>
