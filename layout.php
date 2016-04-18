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

$selectedProducts = explode(",", $_GET['products']);
$_SESSION['selected-products'] = $selectedProducts;
//Variable for Posters and Flyers
$selectedProduct = $selectedProducts[0];

$product = $_SESSION['product'];
$style = $_SESSION['style'];
$projectName = $_SESSION['projectName'];

if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
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
<a href="layout.php" class="sideLink"><li class="active-step">Step 4: Layout</li></a>
</ul>
</div>
<?php

$sql = 'SELECT PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType
FROM PrintProduct
WHERE PrintProduct.printProdId = '.$product.'
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$printProductId = $row['printProdId'];
	$productName = $row['printProdName'];
	$productType = $row['printProdType'];
}
?>
<div id="rightContent">
<div id="page-description">
<h3><?echo $productName; ?></h3>
<hr/>
<h2>Edit Layout</h2>
<hr/>
<p>Here, you can choose/create a headline (if applicable) and set prices for products. </p>
<br/>
<?php 
if ($productType == "description-card") {
	echo '<p>Tip - You can save your work by clicking continue. Then "Edit" the project on your projects
page to continue editing your prices.</p>';
}
?>
</div>
<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
<br/><br/>
<div id="headline-list">
<?php
//---------------------------------------
if ($productType == "poster" | $productType == "flyer") {
	$sql = 'SELECT Product.prodId, Product.prodName, Product.prodCategory, ProductMedia.prodImgThmb
	FROM Product, ProductMedia
	WHERE Product.prodId = '.$selectedProduct.' AND Product.prodId = ProductMedia.prodId';
$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productId = $row['prodId'];
	$productName = $row['prodName'];
	$productImgThmb = $row['prodImgThmb'];
	$productCategory = $row['prodCategory'];
}
	echo '
	<div id="headlineSelect">
	<form id="poster-pricing" action="completion.php" method="POST">
	<h3>Select A Headline</h3>';
	// Echo the preset headlines for this print product (For now just poster headlines that apply to the other products as well)
	$sqlHeadline = "SELECT * FROM PrintProductHeadline WHERE PrintProductHeadline.styleId = ".$style."";
	$rowsHeadline = $conn->query($sqlHeadline);

	foreach ($rowsHeadline as $headline) {
		echo '<input type="radio" name="headline" value="'.$headline['headId'].'" />'.$headline['headText'].'<br/><br/>';
	}
	echo '
	<input id="custom" type="radio" name="headline" value="custom" />Or, Type A Custom Headline<br/><br/>
	<input id="customHeadText" type="text" name="headlineText" size="30" maxlength="30" /><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Characters Remaining</p>
	</div>
	<div id="product-pricing">
	<h3>'.$productName.'</h3>
	<img src="../../Pages/Products/'.$productCategory.'/'.$productImgThmb.'" alt="" /><br/><br/>
	<label for="posterPrice">Price: $</label>
	<input type="text" id="posterPrice" name="posterPrice" size="5" value="0.00"/>
	<p id="charRemaining">Enter only a decimal number. Ex: "54.95"</p>
	</div>
	</form>
	';
}
//---------------------------------------
// Layout the description card price entry form (Shown if user is creating a description card)
if ($productType == "description-card") {
	// Echo beginning of table and form for submitting description card pricing
echo '
<form id="desc-card-pricing" action="completion.php" method="POST">
<table>
<tr><td><h3>Product</h3></td><td colspan="3"><h3>Packages/Sizes & Prices<br/><span style="font-size:.8em; color:red;">Do not enter a "$" in your price. It is done automatically.</span></h3></td></tr>
';
// Get length of products array sent from previous page
$arrLength = count($selectedProducts);
// Loop through each product that was sent and query the database for it's information
	for ($i = 0; $i < $arrLength; $i++) {
		$sql = 'SELECT Product.prodId, Product.prodName, ProductMedia.prodImgThmb
	FROM Product, ProductMedia
	WHERE Product.prodId = '.$selectedProducts[$i].' AND Product.prodId = ProductMedia.prodId';

	$rows = $conn->query($sql);
// Display price entry form for each product returned
	foreach ($rows as $row) {
	echo '
	<tr><td>'.$row['prodName'].'</td>
	<td><input class="cardPrice" name="price1[]" type="text" size="5" />
	<select name="pack1[]">
	<option>Each</option>
	<option>Pack</option>
	<option>Bundle</option>
	<option>Dozen</option>
	<option>Bag</option>
	<option>Box</option>
	<option>Gross</option>
	</select>
	</td>
		<td><input class="cardPrice" name="price2[]" type="text" size="5" />
	<select name="pack2[]">
	<option>Each</option>
	<option>Pack</option>
	<option>Bundle</option>
	<option>Dozen</option>
	<option>Bag</option>
	<option>Box</option>
	<option>Gross</option>
	</select>
	</td>
		<td><input class="cardPrice" name="price3[]" type="text" size="5" />
	<select name="pack3[]">
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
	}
	echo '
	</table>
	</form>
	';
}
//---------------------------------------
//---------------------------------------
// If Editable Banner (Skys the Limit Banner for Example)
if ($productType == "editable-banner") {
	echo '
	<div id="headlineSelect">
	<form id="poster-pricing" action="completion.php" method="POST">
	<h3>Select A Headline</h3>';
	// Echo the preset headlines for this print product (For now just poster headlines that apply to the other products as well)
    $sqlHeadline = "SELECT * FROM PrintProductHeadline WHERE PrintProductHeadline.styleId = ".$style."";
	$rowsHeadline = $conn->query($sqlHeadline);
	
	foreach ($rowsHeadline as $headline) {
		echo '<input type="radio" name="headline" value="'.$headline['headId'].'" />'.$headline['headText'].'<br/><br/>';
	}
	echo '
	<input id="custom" type="radio" name="headline" value="custom" />Or, Type A Custom Headline<br/><br/>
	<input id="customHeadText" type="text" name="headlineCustom" size="30" maxlength="30" /><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Characters Remaining</p>
	<img src="Images/exHeadline.jpg" alt="headline-example" />
	<h3>Enter a Sub-headline</h3>
	<input type="text" id="posterPrice" name="subheadline" size="30" maxlength="30"/><br/>
	<p id="charRemaining"><span id="charLimit">30</span> Character Limit</p>
	<img src="Images/exSubheadline.jpg" alt="headline-example" />
	</form>
	</div>
	';
}
//---------------------------------------
//---------------------------------------
?>
</div>
</div>
</div>
<script>
// Set the card price to a default value of 0.00
$(".cardPrice").val("0.00");

// Check the first radio button in the group
$('input:radio[name=headline]:first').click();

// Submit the correct form the user filled in
$("#continueBtn").on("click", function() {
	$("#poster-pricing").submit();
	$("#desc-card-pricing").submit();

});
// Auto checks custom header radio button on input focus
$("#customHeadText").on("focus", function() {
	$("#custom").prop("checked", true);
});
// Sets the custom radio button's value to the value entered in the custom input box.
$("#customHeadText").on("input", function() {
	$("#custom").val($(this).val());
});

// Sets a character limit and updates the character limit paragraph FOR POSTERS
$("#customHeadText").on("input", function() {
	var charsEntered = $("#customHeadText").val().length;
	var charsLeft = 30 - charsEntered;
	$("#charLimit").text(charsLeft);
});

</script>

</body>
</html>
