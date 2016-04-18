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

require_once('./Stripe/init.php');


if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['message'] != "") {
	$subject = htmlentities($_POST['subject']);
	$customerMessage = htmlentities($_POST['message']);
	
	$to = 'scott@fireworksoveramerica.com';

	$subject = 'PS Inquiry - '.$subject.'';

	$headers = "From: printshipcustomerservice@fireworksoveramerica.com\r\n";
	$headers .= "Reply-To: scott@fireworksoveramerica.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$message = '<h1>Print Shop Customer Inquiry</h1><br/></br>
	<p>Customer Name: '.$firstName.', '.$lastName.'</p>
	<p>Customer Email: '.$email.'</p>
	<p>Customer Phone: '.$phoneNumber.'</p>
	<p>'.$customerMessage.'</p>
	';

	
	$msg = wordwrap($message,70);
	// Mail to Scott Knox
	mail($to, $subject, $msg, $headers, '-fscott@fireworksoveramerica.com');
} else {
	echo "You have submitted a blank message. Please go back and enter a message to send one.";
}
// ----------------------------------
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
<a class="navLink" href="index.php"><li>Create</li></a>
<a class="navLink" href="projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="myAccount.php"><li>My Account</li></a>
<a class="navLink" href="resources.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Email Sent</h2>
<hr/>
<p class="success-text">You Message Has Been Sent</p>
</div>

<p>Your message has been sent and the Print Shop team will be in contact with you soon. 
Thank you for choosing Fireworks Over America!
</p>

<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Return Home</a>

</div>

</div>

<script>
// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
	window.location.replace("index.php");	
});
</script>

</body>
</html>