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
	echo "<script>window.location = ('../../loginPage.php');</script>";
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
</ul>
</div>
<?php
$projectId = $_GET['projId'];

$sql = 'SELECT PrintProject.printProjId, PrintProject.printProdId,
PrintProduct.printProdId, PrintProduct.printProdName
FROM PrintProject, PrintProduct
WHERE PrintProject.printProjId = '.$projectId.' AND PrintProject.printProdId = PrintProduct.printProdId
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productId = $row['printProdId'];
	$productName = $row['printProdName'];
}
?>
<div id="rightContent">
<div id="page-description">
<h3><?echo $productName; ?></h3>
<hr/>
<h2>Select Options</h2>
<hr/>
<p>Use the following selections to modify the content of your description card or to add extra features. 
After you've made your selections, click CONTINUE.</p>
<a id="continueBtn"><img src="../../Images/arrow.png" alt="arrow-icon" /> Continue</a>
</div>
<!-- The main display for all print products -->
<div id="print-products">
<form id="project-info" method="POST" action="editLayout.php">
</form>
<p>Ex: Duck Commander Description Cards</p><br/>
<p><strong>Select a design</strong></p>
<?php
$sql = 'SELECT PrintProduct.printProdId, PrintProduct.printProdName, PrintProductStyle.styleId, PrintProductStyle.styleThumb
FROM PrintProduct, PrintProductStyle
WHERE PrintProduct.printProdId = '.$productId.' AND PrintProduct.printProdId = PrintProductStyle.printProdId
';

$rows = $conn->query($sql);
$count = $rows->rowCount();

if ($count > 0) {
	foreach ($rows as $row) {
		echo '<img id="'.$row['styleId'].'" class="style-thumb" src="../../'.$row['styleThumb'].'" alt="'.$row['styleName'].'-thumb" />';
}
}
else {
	echo "<strong><p>This product has no styles to choose from.</p></strong>";
}

?>
</div>

</div>
</div>

<script>

// Auto select the first style design available
// and set the form action to include current style to pass
$("#project-info").attr("action", "editLayout.php?project=<?php echo $projectId; ?>&style=" + $(".style-thumb:first").attr("id"));
$(".style-thumb:first").toggleClass("style-thumb-active");

// When style is clicked, remove the active class from whatever it is attached to and toggle the active class for
// the style that was clicked. 
$(".style-thumb").on( "click", function(event) {
	if ($(".style-thumb").hasClass("style-thumb-active")) {	
		$(".style-thumb").removeClass("style-thumb-active");
	}
	  $(this).toggleClass( "style-thumb-active" );

// Set the form action to include current style id to be passed
	  $("#project-info").attr("action", "editLayout.php?project=<?php echo $projectId; ?>&style=" + event.target.id);
});

// When the continue button is clicked verify a name is entered, and an item is selected.
$("#continueBtn").click(function() {
	$("#project-info").submit();
});
</script>
</body>
</html>
