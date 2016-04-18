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

$_SESSION['prices1'] = $_POST['price1'];
$_SESSION['prices2'] = $_POST['price2'];
$_SESSION['prices3'] = $_POST['price3'];
$_SESSION['packs1'] = $_POST['pack1'];
$_SESSION['packs2'] = $_POST['pack2'];
$_SESSION['packs3'] = $_POST['pack3'];
$_SESSION['headlineText'] = $_POST['headlineCustom'];
$_SESSION['posterPrice'] = $_POST['posterPrice'];

$product = $_SESSION['product'];
$style = $_SESSION['style'];
$projectName = $_SESSION['projectName'];
$selectedProducts = $_SESSION['selected-products'];
// variables to store description card values, if entered
$prices1 = $_SESSION['prices1'];
$prices2 = $_SESSION['prices2'];
$prices3 = $_SESSION['prices3'];
$packs1 = $_SESSION['packs1'];
$packs2 = $_SESSION['packs2'];
$packs3 = $_SESSION['packs3'];
// variables to store poster values, if entered
$headlineValue = $_POST['headline-custom'];

$headlineText = $_POST['headlineCustom'];
$headlineLength = strlen($headlineText);
$posterPrice = $_SESSION['posterPrice'];


if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('../Pages/Registration/loginPage.php');</script>";
}


$printProjId = $_SESSION['printProjId'];
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

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
<a href="logout.php">Log Out</a>
</p>
</div>
</header>
<nav>
<ul id="navLinks">
<a class="navLink" href="../../index.php"><li>Home</li></a>
<a class="navLink" href="../../index.php"><li>Create</li></a>
<a class="navLink" href="../../projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="../../marketingPlatform.php"><li>My Account</li></a>
<a class="navLink" href="../../marketingPlatform.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="editLayout.php?project=<?php echo $_SESSION['printProjId'] ?>" class="sideLink"><li>Step 4: Layout</li></a>
<a href="editCompletion.php" class="sideLink"><li class="active-step">Step 5: Completion</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Your Project Has Been Updated</h2>
<hr/>
<p>Please click continue to return to your account.</p>
</div>
<a id="continueBtn" href="editOutput.php?projId=<?php echo $printProjId; ?>"><img src="../../Images/arrow.png" alt="arrow-icon" /> Continue</a>
<img id="loading-icon" src="../../Images/loading-icon-blank.png" alt="loading-icon" />
<br/><br/>
<?php

if (isset($_GET['newStyle'])) {
	$newStyle = $_GET['newStyle'];
}

$sql = 'SELECT PrintProject.printProjId, PrintProject.printProdId, PrintProject.printProjName,
PrintProduct.printProdName, PrintProduct.printProdType
FROM PrintProject, PrintProduct
WHERE PrintProject.printProdId = PrintProduct.printProdId AND PrintProject.printProjId = '.$printProjId.'
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$projectId = $row['printProjId'];
	$productId = $row['printProdId'];
	$projectName = $row['printProjName'];
	$productName = $row['printProdName'];
	$productType = $row['printProdType'];
}

// Create the print object if poster or flyer
if ($productType == "poster" || $productType == "flyer") {

if (isset($newStyle)) {
	$stmtEditableBanner = $conn->prepare('UPDATE
	PrintProjectToPrintProductStyle
	SET styleId = '.$newStyle.'
	WHERE printProjId = '.$printProjId.'');
	$stmtEditableBanner->execute();	
}
	
	$stmtPoster = $conn->prepare('UPDATE PrintProjectToPrintProjectHeadline, PrintProjectHeadline
	SET headText="'.$headlineText.'"
	WHERE PrintProjectToPrintProjectHeadline.printProjId = '.$printProjId.'
	AND PrintProjectToPrintProjectHeadline.headId = PrintProjectHeadline.headId
	');
	$stmtPoster->execute();
	
	$stmtPoster = $conn->prepare('UPDATE PrintProjectToPrintProjectUserPrice, PrintProjectUserPrice
	SET userPriceThePrice = '.$posterPrice.'
	WHERE PrintProjectToPrintProjectUserPrice.printProjId = '.$printProjId.' AND 
	PrintProjectToPrintProjectUserPrice.userPriceId = PrintProjectUserPrice.userPriceId');
	$stmtPoster->execute();

}

// Create the print object if description card
if ($productType == "description-card") {
// Get the count of the prices1 array, loop over the length and insert all values to UserPrice
$count = count($prices1);

if (isset ($newStyle)) {
	$stmtEditableBanner = $conn->prepare('UPDATE
	PrintProjectToPrintProductStyle
	SET styleId = '.$newStyle.'
	WHERE printProjId = '.$printProjId.'');
	$stmtEditableBanner->execute();	
}
	
$sql = 'SELECT PrintProjectToPrintProjectDCardUserPrice.printProjId, 
PrintProjectToPrintProjectDCardUserPrice. dCardUserPriceId,
PrintProjectDCardUserPrice.dCardUserPriceId
FROM PrintProjectToPrintProjectDCardUserPrice, PrintProjectDCardUserPrice
WHERE PrintProjectToPrintProjectDCardUserPrice.printProjId = '.$printProjId.'
AND PrintProjectToPrintProjectDCardUserPrice.dCardUserPriceId = 
PrintProjectDCardUserPrice.dCardUserPriceId';

$rows = $conn->query($sql);
$i = 0;
foreach ($rows as $row) {
	if (empty($prices1[$i])) {
		$prices1[$i] = 0.00;
	}
	if (empty($prices2[$i])) {
		$prices2[$i] = 0.00;
	}
	if (empty($prices2[$i])) {
		$prices2[$i] = 0.00;
	}
	$stmtDescCard = $conn->prepare('UPDATE PrintProjectDCardUserPrice
	SET
	dCardUserPricePrice1 = '.$prices1[$i].', dCardUserPricePack1 = "'.$packs1[$i].'", 
	dCardUserPricePrice2 = '.$prices2[$i].', dCardUserPricePack2 = "'.$packs2[$i].'", 
	dCardUserPricePrice3 = '.$prices3[$i].', dCardUserPricePack3 = "'.$packs3[$i].'"
	WHERE PrintProjectDCardUserPrice.dCardUserPriceId = '.$row['dCardUserPriceId'].'
	');
	$stmtDescCard->execute();
	
	$i = $i + 1;
}
	}

// Create the print object if editable banner
if ($productType == "editable-banner") {
	$headline = $_POST['headline'];
	$subheadline = $_POST['subheadline'];
	
if (isset ($newStyle)) {
	$stmtEditableBanner = $conn->prepare('UPDATE
	PrintProjectToPrintProductStyle
	SET styleId = '.$newStyle.'
	WHERE printProjId = '.$printProjId.'');
	$stmtEditableBanner->execute();	
}
	
	// If the headline length is greater than 3 characters, it is a custom headline. It will be inserted into the 
	// Print PROJECT Headline table and junction table. If it is less, it is a pre-set. It will be inserted into the Print 
	// PRODUCT Headline table and junction.
	if ($headlineLength > 3) {
	$stmtGetBanner = '
	SELECT PrintProjectToPrintProjectHeadline.printProjId, 
	PrintProjectToPrintProjectHeadline.headId, PrintProjectHeadline.headId
	FROM PrintProjectToPrintProjectHeadline, PrintProjectHeadline
	WHERE PrintProjectToPrintProjectHeadline.printProjId = '.$printProjId.' 
	AND PrintProjectToPrintProjectHeadline.headId = PrintProjectHeadline.headId';
	$rows = $conn->query($stmtGetBanner);
	
	foreach ($rows as $row) {
		$headId = $row['headId'];
	}
		
	$stmtPoster = $conn->prepare('UPDATE 
	PrintProjectHeadline
	SET headText="'.$headlineText.'"
	WHERE headId = '.$headId.'');
	$stmtPoster->execute();
	
	$stmtGetBanner = '
	SELECT PrintProjectToPrintProjectSubheadline.printProjId, 
	PrintProjectToPrintProjectSubheadline.subheadId, PrintProjectSubheadline.subheadId
	FROM PrintProjectToPrintProjectSubheadline, PrintProjectSubheadline
	WHERE PrintProjectToPrintProjectSubheadline.printProjId = '.$printProjId.' 
	AND PrintProjectToPrintProjectSubheadline.subheadId = PrintProjectSubheadline.subheadId';
	$rows = $conn->query($stmtGetBanner);
	
	foreach ($rows as $row) {
		$subheadId = $row['subheadId'];
	}
	
	$stmtEditableBanner = $conn->prepare('UPDATE 
	PrintProjectSubheadline 
	SET subheadText="'.$subheadline.'"
	WHERE subheadId = '.$subheadId.'');
	$stmtEditableBanner->execute();
	}
	else {
	// Convert headlineValue to int
	$headlineValue = intval($headlineValue);
	$stmtPoster = $conn->prepare('UPDATE
	PrintProjectToPrintProductHeadline
	SET headId= '.$headlineValue.'
	WHERE printProjId='.$printProjId.'');
	$stmtPoster->execute();
	
	$stmtGetBanner = '
	SELECT PrintProjectToPrintProjectSubheadline.printProjId, 
	PrintProjectToPrintProjectSubheadline.subheadId, PrintProjectSubheadline.subheadId
	FROM PrintProjectToPrintProjectSubheadline, PrintProjectSubheadline
	WHERE PrintProjectToPrintProjectSubheadline.printProjId = '.$printProjId.' AND PrintProjectToPrintProjectSubheadline.subheadId = PrintProjectSubheadline.subheadId';

	$rows = $conn->query($stmtGetBanner);
	
	foreach ($rows as $row) {
		$subheadId = $row['subheadId'];
	}
	
	$stmtEditableBanner = $conn->prepare('UPDATE 
	PrintProjectSubheadline 
	SET subheadText="'.$subheadline.'"
	WHERE subheadId = '.$subheadId.'');
	$stmtEditableBanner->execute();
}
}

?>
</div>
</div>
<script>
// Display a loading gif while the project is actually created
$("#continueBtn").on("click", function() {
		$("#loading-icon").attr("src", "../../Images/loading-icon.gif");
		$("<p style='color:red;'>Please wait while your project finalizes.</p>").insertAfter("#loading-icon");
		$("#continueBtn").remove();
});
</script>
</body>
</html>