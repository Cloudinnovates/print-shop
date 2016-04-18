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
<h2>How To Pay For A Purchase</h2>
<p>How does the payment process work?</p>
<hr/>
<img src="Images/checkout-1.jpg" alt="checkout-pic-1" />
<p>The first step is adding projects you wish to purchase to your cart.
Make sure you enter the correct quantities of each project you wish to order.
Then, just hit "Checkout."</p>
<img src="Images/checkout-2.jpg" alt="checkout-pic-2" />
<p>You can then enter the shipping address for your company or location. If you click
the "Use Address On File" button, it will auto-populate your information. Make sure
this information is entered correctly as you would ship any other item to your location.</p>
<img src="Images/checkout-3.jpg" alt="checkout-pic-3" />
<p>Choose a shipping option and hit "Continue." FedEx Ground is currently
our only shipping option.</p>

<img src="Images/checkout-4.jpg" alt="checkout-pic-4" />
<p>The payment options page lets you choose an amount to pay out of your Print Shop
balance. Just type in the amount to use and the amount to pay from your credit card
will automatically fill in. Click "Continue."</p>

<img src="Images/checkout-5.jpg" alt="checkout-pic-5" />
<p>Once you agree with the terms and conditions, you can also enter any special 
instructions you might have for your order. If your Print Shop balance 
didn't cover the full amount owed, click "Pay with Card" to pay for the 
remainder. This button will only show up if you have agreed to the Terms and Conditions.</p>

<img src="Images/checkout-6.jpg" alt="checkout-pic-6" />
<p>You are now presented with a secure checkout form. Simply enter your payment details
and hit "Pay." Once you hit "Pay" the payment will be processed immidiately.</p>

<img src="Images/checkout-7.jpg" alt="checkout-pic-7" />
<p>Success! Your order is complete. You can now go to your account and view details on your 
order.</p>
<a href="#">Back To Top</a>
</div>

</div>
</div>

<script>

</script>
</body>
</html>
