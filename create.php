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
<a id="top"></a>
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
<a href="index.php" class="sideLink"><li class="active-step">Step 1: Create</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>Select Project Format</h2>
<hr/>
<p>To begin customizing this project, select one of the formats from the list below. 
Once you've selected the format scroll to the bottom of the window and click CONTINUE.</p>
<a id="continueBtn" href><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
</div>
<!-- The main display for all print products -->
<div id="print-products">
<?php
$sql = 'SELECT * FROM PrintProductMedia';
$rows = $conn->query($sql);
// Get all print products and show product info for the listing
foreach ($rows as $row) {
	$mediaId = $row['mediaId'];
	
echo '
<div class="print-product-media">
<p class="print-product-media-description">'.$row['mediaName'].'</p>
<hr/>
<img class="print-product-image" src="'.$row['mediaImage'].'" alt="print-'.$row['mediaType'].'-image" />
<div class="print-products">
';
// Show the sizes available for the product
$sql = 'SELECT * FROM PrintProductMedia, PrintProductMediaToPrintProduct, PrintProduct
WHERE PrintProductMedia.mediaId = '.$mediaId.' AND PrintProductMediaToPrintProduct.mediaId = '.$mediaId.' 
AND PrintProductMediaToPrintProduct.printProdId = PrintProduct.printProdId';
$rows = $conn->query($sql);
foreach ($rows as $row) {
	echo '
	<div class="print-product">
    <input class="radio-product" type="radio" name="radio-product" value="'.$row['printProdId'].'" />
    <img class="print-product-icon" src="'.$row['printProdImage'].'" alt="print-product-icon" />
    <p class="print-product-description">'.$row['printProdName'].'</p>
	';
	if ($row['printProdPreview']) {
		echo '<a target="_blank" href="product-preview.php?id='.$row['printProdId'].'">Preview</a> | ';
	}
	echo '
	<a target="_blank" href="product-pricing.php?id='.$row['printProdId'].'">Pricing</a>
</div>
	';
}
// echo out the last part of print product layout
echo '
</div>
</div>
';
}
?>
</div>
<a id="top-button" href="#top"><img src="Images/top-button.png" alt="back-to-top-button" />
</div>
</div>

<script>



$(document).ready(function() {
	
    $('input[type=radio][name=radio-product]').change(function() {
		$("#continueBtn").attr("href", "options.php?product=" + 
		$("input[name=radio-product]:checked").val())
    });
	
	$("#continueBtn").click(function() {
		if (!$("input[name='radio-product']:checked").val()) {
		alert("You have not selected a product.");
		}
	});
});
</script>
</body>
</html>
