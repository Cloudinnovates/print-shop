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
<?php 
if ($acctType == 'Admin') {
	echo '<a href="Pages/Reports/reports.php" class="sideLink"><li class="active-step">Report System</li></a>';
}
?>
<a href="../../index.php" class="sideLink"><li class="active-step">Back to Website</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h1>Welcome To Print Shop</h1>
<p>Here, you can view recent announcements, get the latest updates on Print Shop, and find many
other helpful resources to assist you in creating marketing materials for your company. </p>
<hr/>
</div>
<div id="announcements">
<h3>Announcements</h3>
<p>12/14/2015 - Print Shop has entered the beta testing phase.</p>
</div>

<div id="left-column">
<div id="common-questions">
<h2 style="text-align:center;">Common Questions</h2>
<hr/>
<a href="Pages/Resources/iExtendToPrintShop.php" class="question-link">I Previously Used iExtend, What's New?</a><br/>
<a href="Pages/Resources/earn-print-shop-credit.php" class="question-link">How Do I Earn Print Shop Credit?</a><br/>
<a href="Pages/Resources/how-to-pay.php" class="question-link">How To Pay For A Purchase</a><br/>
<a href="Pages/Resources/self-printing.php" class="question-link">How To Self Print Projects</a><br/>
<a href="Pages/Resources/how-to-track.php" class="question-link">How To Track Your Order</a><br/>
</div>
</div>

<div id="right-column">
<div id="contact-form">
<form action="contactSend.php" method="POST">
<h2 class="contact-title">Contact Us</h2>
<label for="subject">Subject</label><br/>
<input type="text" name="subject" size="40" /><br/><br/>
<label for="message">Message</label><br/>
<textarea name="message" rows="11" cols="45">

</textarea>

<input type="image" src="Images/send-button.jpg" id="home-submit" name="submit" alt="submit" />
</form>

</div>

</div>

</div>
</div>

<script>

</script>
</body>
</html>
