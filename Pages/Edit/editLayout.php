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

$product = $_SESSION['product'];
$style = $_SESSION['style'];
$projectName = $_SESSION['projectName'];


if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('../Pages/Registration/loginPage.php');</script>";
}


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
<a class="navLink" href="../../editProduct.php?page=1"><li>Create</li></a>
<a class="navLink" href="../../projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="marketingPlatform.php"><li>My Account</li></a>
<a class="navLink" href="marketingPlatform.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="../../projectsHome.php" class="sideLink"><li class="active-step">Go Back</li></a>
</ul>
</div>
<?php
$printProjId = $_GET['project'];
$newStyle = $_GET['style'];

$_SESSION['printProjId'] = $printProjId;

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

// Get the current style of the project
// If the user has specified a new style
if (isset($newStyle)) {
	$sqlGetStyle = 'SELECT PrintProductStyle.styleId, PrintProductStyle.styleThumb
FROM PrintProductStyle
WHERE PrintProductStyle.styleId = '.$newStyle.'';
} else {
	$sqlGetStyle = 'SELECT PrintProject.printProjId, PrintProjectToPrintProductStyle.printProjId,
PrintProjectToPrintProductStyle.styleId, PrintProductStyle.styleId, PrintProductStyle.styleThumb
FROM PrintProject, PrintProjectToPrintProductStyle, PrintProductStyle
WHERE PrintProject.printProjId = '.$projectId.' AND PrintProject.printProjId = PrintProjectToPrintProductStyle.printProjId
AND PrintProjectToPrintProductStyle.styleId = PrintProductStyle.styleId';
}

$rows = $conn->query($sqlGetStyle);

foreach ($rows as $row) {
	$styleThumb = $row['styleThumb'];
}

?>
<div id="rightContent">
<div id="page-description">
<h3><?echo $productName; ?></h3>
<hr/>
<h2>Edit Layout</h2>
<hr/>
<p>Here, you can edit your headline (if applicable) and change prices for products. </p>
</div>
<a id="continueBtn"><img src="../../Images/arrow.png" alt="arrow-icon" /> Continue</a>
<br/><Br/>
<p>Current Style: <img src="../../<?php echo $styleThumb; ?>" alt="product-style" /></p>
<a href="editStyle.php?projId=<?php echo $projectId; ?>">Click here to edit style</a>
<br/><br/>
<div id="headline-list">
<?php
if ($productType == "poster" || $productType == "flyer") {
	echo '
	<div id="headlineSelect">
	<form id="poster-pricing" action="editCompletion.php" method="POST">
	<h3>Edit Headline</h3>';
	// Echo the preset headlines for this print product (For now just poster headlines that apply to the other products as well)
	$sqlHeadline = "SELECT * FROM PrintProductHeadline WHERE PrintProductHeadline.styleId = ".$style."";
	$rowsHeadline = $conn->query($sqlHeadline);
	foreach ($rowsHeadline as $headline) {
		echo '<input type="radio" name="headline" value="'.$headline['headId'].'" />'.$headline['headText'].'<br/><br/>';
	}
	
	$sqlHeadline = "SELECT PrintProjectToPrintProjectHeadline.printProjId, PrintProjectToPrintProjectHeadline.headId,
	PrintProjectHeadline.headId, PrintProjectHeadline.headText
	FROM PrintProjectToPrintProjectHeadline, PrintProjectHeadline 
	WHERE PrintProjectToPrintProjectHeadline.printProjId = ".$printProjId." AND PrintProjectToPrintProjectHeadline.headId
	= PrintProjectHeadline.headId";
	$rowsHeadline = $conn->query($sqlHeadline);
	
	foreach ($rowsHeadline as $headline) {
		$headText = $headline['headText'];
	}

	// Get product that was chosen for the project
	$sql = 'SELECT PrintProjectToProduct.printProjId, PrintProjectToProduct.prodId, Product.prodId, Product.prodName,
	Product.prodCategory, ProductMedia.prodImgThmb
	FROM PrintProjectToProduct, Product, ProductMedia
	WHERE PrintProjectToProduct.printProjId = '.$printProjId.' AND PrintProjectToProduct.prodId = 
	Product.prodId AND Product.prodId = ProductMedia.prodId';
$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productId = $row['prodId'];
	$productName = $row['prodName'];
	$productImgThmb = $row['prodImgThmb'];
	$productCategory = $row['prodCategory'];
}

	// Get user price that was entered for the project
	$sql = 'SELECT PrintProjectToPrintProjectUserPrice.printProjId, PrintProjectToPrintProjectUserPrice.userPriceId,
	PrintProjectUserPrice.userPriceId, PrintProjectUserPrice.userPriceThePrice
	FROM PrintProjectToPrintProjectUserPrice, PrintProjectUserPrice
	WHERE PrintProjectToPrintProjectUserPrice.printProjId = '.$printProjId.' AND PrintProjectToPrintProjectUserPrice.userPriceId = 
	PrintProjectToPrintProjectUserPrice.userPriceId';
$rows = $conn->query($sql);

foreach ($rows as $row) {
	$userPrice = $row['userPriceThePrice'];
}
	echo '
	<input id="customHeadText" type="text" name="headlineCustom" size="30" maxlength="30" value="'.$headText.'"/><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Characters Remaining</p>
	</div>
	<div id="product-pricing">
	<h3>'.$productName.'</h3>
	<img src="../../../../Pages/Products/'.$productCategory.'/'.$productImgThmb.'" alt="" /><br/><br/>
	<label for="posterPrice">Price: $</label>
	<input type="text" id="posterPrice" name="posterPrice" size="5" value="'.$userPrice.'"/>
	<p id="charRemaining">Enter only a decimal number. Ex: "54.95"</p>
	</div>
	</form>
	
	<script>
	// Submit the Poster editing form
	$("#continueBtn").on("click", function() {
		$("#poster-pricing").submit();
	});
	</script>
	';
}
// Layout the description card price entry form (Shown if user is creating a description card)
if ($productType == "description-card") {
	// Echo beginning of table and form for submitting description card pricing
	if (isset($newStyle)) {
		echo '<form id="desc-card-pricing" action="editCompletion.php?newStyle='.$newStyle.'" method="POST">';
	} else {
		echo '<form id="desc-card-pricing" action="editCompletion.php" method="POST">';
	}
echo '
<table>
<tr><td><h3>Product</h3></td><td colspan="3"><h3>Packages/Sizes & Prices</h3></td></tr>
';

	$sql = 'SELECT PrintProjectToPrintProjectDCardUserPrice.printProjId, 
	PrintProjectToPrintProjectDCardUserPrice.dCardUserPriceId, 
	PrintProjectDCardUserPrice.dCardUserPriceId, PrintProjectDCardUserPrice.dCardUserPriceProdId,
	PrintProjectDCardUserPrice.dCardUserPricePrice1, PrintProjectDCardUserPrice.dCardUserPricePack1,
	PrintProjectDCardUserPrice.dCardUserPricePrice2, PrintProjectDCardUserPrice.dCardUserPricePack2,
	PrintProjectDCardUserPrice.dCardUserPricePrice3, PrintProjectDCardUserPrice.dCardUserPricePack3,
	Product.prodId, Product.prodName
	FROM PrintProjectToPrintProjectDCardUserPrice, PrintProjectDCardUserPrice, Product
	WHERE PrintProjectToPrintProjectDCardUserPrice.printProjId = '.$printProjId.' AND 
	PrintProjectToPrintProjectDCardUserPrice.dCardUserPriceId = 
	PrintProjectDCardUserPrice.dCardUserPriceId AND PrintProjectDCardUserPrice.dCardUserPriceProdId =
	Product.prodId
	';

	$rows = $conn->query($sql);
// Display price entry form for each product returned
	foreach ($rows as $row) {
	
	echo '
	<tr><td>'.$row['prodName'].'</td>
	<td><input class="cardPrice" name="price1[]" type="text" size="5" value="'.$row['dCardUserPricePrice1'].'"/>
	<select name="pack1[]">
	<option>'.$row['dCardUserPricePack1'].'</option>
	<option>Each</option>
	<option>Pack</option>
	<option>Bundle</option>
	<option>Dozen</option>
	<option>Bag</option>
	<option>Box</option>
	<option>Gross</option>
	</select>
	</td>
		<td><input class="cardPrice" name="price2[]" type="text" size="5" value="'.$row['dCardUserPricePrice2'].'"/>
	<select name="pack2[]">
	<option>'.$row['dCardUserPricePack2'].'</option>
	<option>Each</option>
	<option>Pack</option>
	<option>Bundle</option>
	<option>Dozen</option>
	<option>Bag</option>
	<option>Box</option>
	<option>Gross</option>
	</select>
	</td>
		<td><input class="cardPrice" name="price3[]" type="text" size="5" value="'.$row['dCardUserPricePrice3'].'"/>
	<select name="pack3[]">
	<option>'.$row['dCardUserPricePack3'].'</option>
	<option>Each</option>
	<option>Pack</option>
	<option>Bundle</option>
	<option>Dozen</option>
	<option>Bag</option>
	<option>Box</option>
	<option>Gross</option>
	</select>
	</td>
	</tr>
	';
	}

	echo '
	</table>
	</form>
	<script>
	// Handle submit of description card edit form
	$("#continueBtn").on("click", function() {
	$("#desc-card-pricing").submit();
	});
	</script>
	';
}
// If Editable Banner (Skys the Limit Banner for Example)
if ($productType == "editable-banner") {
	$sql = 'SELECT PrintProjectToPrintProjectHeadline.printProjId, 
	PrintProjectToPrintProjectHeadline.headId, PrintProjectHeadline.headId,
    PrintProjectHeadline.headText,
	PrintProjectToPrintProjectSubheadline.printProjId, 
	PrintProjectToPrintProjectSubheadline.subheadId, PrintProjectSubheadline.subheadId,
    PrintProjectSubheadline.subheadText
	FROM PrintProjectToPrintProjectHeadline, PrintProjectHeadline,
	PrintProjectToPrintProjectSubheadline, PrintProjectSubheadline
	WHERE PrintProjectToPrintProjectHeadline.printProjId = '.$printProjId.' AND
	PrintProjectToPrintProjectHeadline.headId = PrintProjectHeadline.headId AND
    PrintProjectToPrintProjectSubheadline.subheadId = PrintProjectSubheadline.subheadId
	';
	$rows = $conn->query($sql);
	
	foreach ($rows as $row) {
		$headText = $row['headText'];
		$subheadText = $row['subheadText'];
	}
	
	echo '
	<div id="headlineSelect">
	<form id="edit-banner-form" action="editCompletion.php" method="POST">
	<h3>Select A Headline</h3>';
	// Echo the preset headlines for this print product (For now just poster headlines that apply to the other products as well)
    $sqlHeadline = "SELECT * FROM PrintProductHeadline WHERE PrintProductHeadline.styleId = ".$style."";
	$rowsHeadline = $conn->query($sqlHeadline);
	
	foreach ($rowsHeadline as $headline) {
		echo '<input type="radio" name="headline-custom" value="'.$headline['headId'].'" />'.$headline['headText'].'<br/><br/>';
	}
	echo '
	<input id="custom" type="radio" name="headline" value="custom" />Edit Headline<br/><br/>
	<input id="customHeadText" type="text" name="headlineCustom" size="30" maxlength="30" value="'.$headText.'"/><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Characters Remaining</p>
	<img src="../../Images/exHeadline.jpg" alt="headline-example" />
	<h3>Enter a Sub-headline</h3>
	<input type="text" id="posterPrice" name="subheadline" size="30" maxlength="30" value="'.$subheadText.'"/><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Character Limit</p>
	<img src="../../Images/exSubheadline.jpg" alt="headline-example" />
	</form>
	</div>
	
	<script>
	// Auto checks custom header radio button on input focus
	$("#customHeadText").on("focus", function() {
	$("#custom").prop("checked", true);
	});
	// Sets the custom radio button\'s value to the value entered in the custom input box.
	$("#customHeadText").on("input", function() {
	$("#custom").val($(this).val());
	});

	// Sets a character limit and updates the character limit paragraph FOR POSTERS
	$("#customHeadText").on("input", function() {
	var charsEntered = $("#customHeadText").val().length;
	var charsLeft = 30 - charsEntered;
	$("#charLimit").text(charsLeft);
	});
	
	// Handle submit of editable banner edit form
	$("#continueBtn").on("click", function() {
	$("#edit-banner-form").submit();
	});
	</script>
	';
}

if ($productType != "poster" && $productType != "flyer" && $productType != "description-card" && 
$productType != "editable-banner") {
	echo '<span style="color:red;">This product is not editable. Please create the project again with the changes you desire.</span>
		<script>
		// Submit the Poster editing form
		$("#continueBtn").on("click", function() {
		window.location = ("../../projectsHome.php");
		});
	</script>
	';
}
?>
</div>
</div>
</div>
<script>


</script>

</body>
</html>