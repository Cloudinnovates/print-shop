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

$_SESSION['prices1'] = $_POST['price1'];
$_SESSION['prices2'] = $_POST['price2'];
$_SESSION['prices3'] = $_POST['price3'];
$_SESSION['packs1'] = $_POST['pack1'];
$_SESSION['packs2'] = $_POST['pack2'];
$_SESSION['packs3'] = $_POST['pack3'];
$_SESSION['headlineValue'] = $_POST['headline'];
$headlineLength = strlen($_SESSION['headlineValue']);

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
$headlineText = $_SESSION['headlineText'];
$posterPrice = $_SESSION['posterPrice'];

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
<a href="index.php" class="sideLink"><li>Step 1: Create</li></a>
<a href="options.php?product=<?php echo $product ?>" class="sideLink"><li>Step 2: Options</li></a>
<a href="products.php" class="sideLink"><li>Step 3: Products</li></a>
<a href="layout.php" class="sideLink"><li>Step 4: Layout</li></a>
<a href="completion.php" class="sideLink"><li class="active-step">Step 5: Completion</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<hr/>
<h2>Your Project Has Been Created</h2>
<hr/>
<p>Please click continue to proceed to your account. You may then add the project to your cart or 
create another print project.</p>
</div>
<img id="loading-icon" src="Images/loading-icon-blank.png" alt="loading-icon" />
<br/><br/>
<?php
$sql = 'SELECT PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType
FROM PrintProduct
WHERE PrintProduct.printProdId = '.$product.'
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productId = $row['printProdId'];
	$productName = $row['printProdName'];
	$productType = $row['printProdType'];
}
$date = date("m/d/Y");

// Create the print object if poster or flyer
if ($productType == "poster" | $productType == "flyer") {
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProject (printProdId, printProjName, printProjDate, printProjFile) 
	VALUES ('.$product.', "'.$projectName.'", "'.$date.'", "test")');
	$stmtPoster->execute();
	$printProjId = $conn->lastInsertId();
	
	// If the headline length is greater than 3 characters, it is a custom headline. It will be inserted into the 
	// Print PROJECT Headline table and junction table. If it is less, it is a pre-set. It will be inserted into the Print 
	// PRODUCT Headline table and junction.
	if ($headlineLength > 3) {
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectHeadline (headText) 
	VALUES ("'.$_SESSION['headlineValue'].'")');
	$stmtPoster->execute();
	$headlineId = $conn->lastInsertId();
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectHeadline (printProjId, headId) 
	VALUES ('.$printProjId.', '.$headlineId.')');
	$stmtPoster->execute();
	}
	else {
	// Convert headlineValue to int
	$headlineValue = intval($headlineValue);
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductHeadline (printProjId, headId) 
	VALUES ('.$printProjId.', '.$_SESSION['headlineValue'].')');
	$stmtPoster->execute();
	}
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductStyle (printProjId, styleId) 
	VALUES ('.$printProjId.', '.$style.')');
	$stmtPoster->execute();
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectUserPrice (userPriceProdId, userPriceThePrice) 
	VALUES ('.$selectedProducts[0].', '.$posterPrice.')');
	$stmtPoster->execute();
	$userPriceId = $conn->lastInsertId();
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectUserPrice (printProjId, userPriceId) 
	VALUES ('.$printProjId.', '.$userPriceId.')');
	$stmtPoster->execute();
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToProduct (printProjId, prodId) 
	VALUES ('.$printProjId.', '.$selectedProducts[0].')');
	$stmtPoster->execute();
}
// Create the print object if description card
if ($productType == "description-card") {

	$stmtDescCard = $conn->prepare('INSERT INTO 
	PrintProject (printProdId, printProjName, printProjDate, printProjFile) 
	VALUES ('.$product.', "'.$projectName.'", "'.$date.'", "test")');
	$stmtDescCard->execute();
	$printProjId = $conn->lastInsertId();

	$stmtDescCard = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductStyle (printProjId, styleId) 
	VALUES ('.$printProjId.', '.$style.')');
	$stmtDescCard->execute();
// Get the count of the prices1 array, loop over the length and insert all values to UserPrice
	$count = count($prices1);
	for ($i = 0; $i < $count; $i++) {
	if (empty($prices1[$i])) {
		$prices1[$i] = 0.00;
	}
	if (empty($prices2[$i])) {
		$prices2[$i] = 0.00;
	}
	if (empty($prices2[$i])) {
		$prices2[$i] = 0.00;
	}
	$stmtDescCard = $conn->prepare('INSERT INTO 
	PrintProjectDCardUserPrice (dCardUserPriceProdId, dCardUserPricePrice1, 
	dCardUserPricePack1, dCardUserPricePrice2, dCardUserPricePack2, 
	dCardUserPricePrice3, dCardUserPricePack3) 
	VALUES ('.$selectedProducts[$i].', '.$prices1[$i].', "'.$packs1[$i].'", 
	'.$prices2[$i].', "'.$packs2[$i].'", '.$prices3[$i].', "'.$packs3[$i].'")');
	$stmtDescCard->execute();
	$userPriceId = $conn->lastInsertId();
	
	$stmtDescCard = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectDCardUserPrice (printProjId, dCardUserPriceId) 
	VALUES ('.$printProjId.', '.$userPriceId.')');
	$stmtDescCard->execute();
// Add the link between the card and the product
	$stmtDescCard = $conn->prepare('INSERT INTO 
	PrintProjectToProduct (printProjId, prodId) 
	VALUES ('.$printProjId.', '.$selectedProducts[$i].')');
	$stmtDescCard->execute();
	}
}

// Create the print object if BLANK description card
if ($productType == "description-card-blank") {
	$stmtDescCardBlank = $conn->prepare('INSERT INTO 
	PrintProject (printProdId, printProjName, printProjDate, printProjFile) 
	VALUES ('.$product.', "'.$projectName.'", "'.$date.'", "test")');
	$stmtDescCardBlank->execute();	
	$printProjId = $conn->lastInsertId();
	
	$stmtDescCardBlank = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductStyle (printProjId, styleId) 
	VALUES ('.$printProjId.', '.$style.')');
	$stmtDescCardBlank->execute();
}

// Create the print object if banner
if ($productType == "banner") {
	$stmtBanner = $conn->prepare('INSERT INTO 
	PrintProject (printProdId, printProjName, printProjDate, printProjFile) 
	VALUES ('.$product.', "'.$projectName.'", "'.$date.'", "test")');
	$stmtBanner->execute();	
	$printProjId = $conn->lastInsertId();
	
	$stmtBanner = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductStyle (printProjId, styleId) 
	VALUES ('.$printProjId.', '.$style.')');
	$stmtBanner->execute();
}

// Create the print object if banner
if ($productType == "editable-banner") {
	$headline = $_POST['headline'];
	$subheadline = $_POST['subheadline'];

	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProject (printProdId, printProjName, printProjDate, printProjFile) 
	VALUES ('.$product.', "'.$projectName.'", "'.$date.'", "test")');
	$stmtEditableBanner->execute();	
	$printProjId = $conn->lastInsertId();
	
	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductStyle (printProjId, styleId) 
	VALUES ('.$printProjId.', '.$style.')');
	$stmtEditableBanner->execute();
	
	// If the headline length is greater than 3 characters, it is a custom headline. It will be inserted into the 
	// Print PROJECT Headline table and junction table. If it is less, it is a pre-set. It will be inserted into the Print 
	// PRODUCT Headline table and junction.
	if ($headlineLength > 3) {
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectHeadline (headText) 
	VALUES ("'.$headline.'")');
	$stmtPoster->execute();
	$headlineId = $conn->lastInsertId();
	
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectHeadline (printProjId, headId) 
	VALUES ('.$printProjId.', '.$headlineId.')');
	$stmtPoster->execute();
	
	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProjectSubheadline (subheadText) 
	VALUES ("'.$subheadline.'")');
	$stmtEditableBanner->execute();
	$subheadlineId = $conn->lastInsertId();
	
	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectSubheadline (printProjId, subheadId) 
	VALUES ('.$printProjId.', '.$subheadlineId.')');
	$stmtEditableBanner->execute();
	}
	else {
	// Convert headlineValue to int
	$headlineValue = intval($headlineValue);
	$stmtPoster = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProductHeadline (printProjId, headId) 
	VALUES ('.$printProjId.', '.$_SESSION['headlineValue'].')');
	$stmtPoster->execute();
	
	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProjectSubheadline (subheadText) 
	VALUES ("'.$subheadline.'")');
	$stmtEditableBanner->execute();
	$subheadlineId = $conn->lastInsertId();
	
	$stmtEditableBanner = $conn->prepare('INSERT INTO 
	PrintProjectToPrintProjectSubheadline (printProjId, subheadId) 
	VALUES ('.$printProjId.', '.$subheadlineId.')');
	$stmtEditableBanner->execute();
}
}
	// Add to UserToPrintProject to relate the project to the user creating it
	$stmtRelateUser = $conn->prepare('INSERT INTO
	UserToPrintProject (printProjId, userId)
	VALUES('.$printProjId.', '.$userId.')');
	$stmtRelateUser->execute();
?>
<a id="continueBtn" href="output.php?projId=<?php echo $printProjId; ?>"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
</div>
</div>

<script>
// Display a loading gif while the project is actually created
$("#continueBtn").on("click", function() {
		$("#loading-icon").attr("src", "Images/loading-icon.gif");
		$("<p style='color:red;'>Please wait while your project finalizes.</p>").insertAfter("#loading-icon");
		$("#continueBtn").remove();
});

$("a").on('click', function() {
	return "Project is not fully generated. Are you sure you want to leave?";
});
</script>

</body>
</html>
