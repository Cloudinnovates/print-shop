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
// Do not allow the browser to cache this page. This doesn't allow it to constantly regenerate the edited file for some
// reason
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Get the Print Project Id to search for the correct project that was created
$printProjId = $_GET['projId'];

// Find the print product type to choose which FPDF functions are called
$sql = 'SELECT PrintProject.printProjId, PrintProject.printProjName, 
PrintProject.printProdId, PrintProduct.printProdId, PrintProduct.printProdType, 
PrintProduct.printProdWidth, PrintProduct.printProdHeight, 
PrintProduct.printProdOrientation
FROM PrintProject, PrintProduct
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProdId = PrintProduct.printProdId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	// Get the print project name to be appended to the file name at output. This fits 
	// BASS' production flow as they need it in file name when processing many orders.
	$printProjName = $row['printProjName'];
	$printProdId = $row['printProdId'];
	$printProdType = $row['printProdType'];
	$printProdWidth = $row['printProdWidth'];
	$printProdHeight = $row['printProdHeight'];
	$printProdOrientation = $row['printProdOrientation'];
}

// Find the print project style that was chosen
if ($printProdType == "banner") {
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProductStyle.printProjId,
PrintProjectToPrintProductStyle.styleId, PrintProductStyle.styleId, PrintProductStyle.styleBackground
FROM PrintProject, PrintProjectToPrintProductStyle, PrintProductStyle
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProjId = 
PrintProjectToPrintProductStyle.printProjId AND PrintProjectToPrintProductStyle.styleId =
PrintProductStyle.styleId';
}
else {
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProductStyle.printProjId,
PrintProjectToPrintProductStyle.styleId, PrintProductStyle.styleId, PrintProductStyle.styleBackground,
PrintProductStyleFont.styleFont, PrintProductStyleFont.styleFontHeadFontSize, 
PrintProductStyleFont.styleFontHeadR, PrintProductStyleFont.styleFontHeadG, 
PrintProductStyleFont.styleFontHeadB, PrintProductStyleFont.styleFontPriceFontSize,
PrintProductStyleFont.styleFontPriceR, PrintProductStyleFont.styleFontPriceG,
PrintProductStyleFont.styleFontPriceB, PrintProductStyleFont.styleFontStrokeR,
PrintProductStyleFont.styleFontStrokeG, PrintProductStyleFont.styleFontStrokeB
FROM PrintProject, PrintProjectToPrintProductStyle, PrintProductStyle, PrintProductStyleFont
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProjId = 
PrintProjectToPrintProductStyle.printProjId AND PrintProjectToPrintProductStyle.styleId =
PrintProductStyle.styleId AND PrintProductStyle.styleId = PrintProductStyleFont.styleId';
}
$rows = $conn->query($sql);

foreach ($rows as $row) {
	$styleId = $row['styleId'];
	$styleBackground = $row['styleBackground'];
	$styleFont = $row['styleFont'];
	$styleFontHeadFontSize = $row['styleFontHeadFontSize'];
	$styleFontHeadR = $row['styleFontHeadR'];
	$styleFontHeadG = $row['styleFontHeadG'];
	$styleFontHeadB = $row['styleFontHeadB'];
	$styleFontPriceFontSize = $row['styleFontPriceFontSize'];
	$styleFontPriceR = $row['styleFontPriceR'];
	$styleFontPriceG = $row['styleFontPriceG'];
	$styleFontPriceB = $row['styleFontPriceB'];
	$styleFontStrokeR = $row['styleFontStrokeR'];
	$styleFontStrokeG = $row['styleFontStrokeG'];
	$styleFontStrokeB = $row['styleFontStrokeB'];
}

// Find the headline text the user selected for their project if custom
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProjectHeadline.printProjId,
PrintProjectToPrintProjectHeadline.headId, PrintProjectHeadline.headId, PrintProjectHeadline.headText
FROM PrintProject, PrintProjectToPrintProjectHeadline, PrintProjectHeadline
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProjId = 
PrintProjectToPrintProjectHeadline.printProjId AND PrintProjectToPrintProjectHeadline.headId =
PrintProjectHeadline.headId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$headLength = strlen($row['headText']);
	$text = $row['headText'];
}

// Find the headline information if pre-selected option
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProductHeadline.printProjId,
PrintProjectToPrintProductHeadline.headId, PrintProductHeadline.headId, PrintProductHeadline.headImgX,
PrintProductHeadline.headImgY, PrintProductHeadline.headImgWidth, PrintProductHeadline.headImgFile
FROM PrintProject, PrintProjectToPrintProductHeadline, PrintProductHeadline
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProjId = 
PrintProjectToPrintProductHeadline.printProjId AND  PrintProjectToPrintProductHeadline.headId =
PrintProductHeadline.headId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$headImgX = $row['headImgX'];
	$headImgY = $row['headImgY'];
	$headImgWidth = $row['headImgWidth'];
	$headImgFile = $row['headImgFile'];
	
	$headIsImage = strlen($headImgFile);
}

// Find the SUB headline text the user selected for their project IF Editable Banner
if ($printProdType == "editable-banner") {
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProjectSubheadline.printProjId,
PrintProjectToPrintProjectSubheadline.subheadId, PrintProjectSubheadline.subheadId, PrintProjectSubheadline.subheadText
FROM PrintProject, PrintProjectToPrintProjectSubheadline, PrintProjectSubheadline
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProjectToPrintProjectSubheadline.subheadId =
PrintProjectSubheadline.subheadId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$subText = $row['subheadText'];
}	
}

// Find product media that will be displayed
$sql = 'SELECT PrintProject.printProjId, PrintProjectToProduct.printProjId,
PrintProjectToProduct.prodId, Product.prodId, Product.prodName, Product.prodCategory, 
Product.prodDescription, ProductMedia.prodId, ProductMedia.prodImgPrint
FROM PrintProject, PrintProjectToProduct, Product, ProductMedia
WHERE PrintProject.printProjId = '.$printProjId.' AND 
PrintProject.printProjId = PrintProjectToProduct.printProjId AND
PrintProjectToProduct.prodId = Product.prodId AND
Product.prodId = ProductMedia.prodId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productName = $row['prodName'];
	$productCategory = $row['prodCategory'];
	$productDescription = $row['prodDescription'];
	$productImage = $row['prodImgPrint'];
}
// Find the price the user entered
$sql = 'SELECT PrintProject.printProjId, PrintProjectToPrintProjectUserPrice.printProjId,
PrintProjectToPrintProjectUserPrice.userPriceId, PrintProjectUserPrice.userPriceId, PrintProjectUserPrice.userPriceThePrice
FROM PrintProject, PrintProjectToPrintProjectUserPrice, PrintProjectUserPrice
WHERE PrintProject.printProjId = '.$printProjId.' AND 
PrintProject.printProjId = PrintProjectToPrintProjectUserPrice.printProjId AND PrintProjectToPrintProjectUserPrice.userPriceId
= PrintProjectUserPrice.userPriceId';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$userPrice = $row['userPriceThePrice'];
}	


// Include the FPDF Library
require('fpdf/fpdf.php');
require('fpdf/makefont/makefont.php');
// Code to Generate PDF file that was designed

// Generate a random integer to add to file name
$ranNum = mt_rand(1000, 9999);
	
// Generate Poster PDF File
if($printProdType == "poster") {
$pdf = new FPDF(''.$printProdOrientation.'', 'in', array($printProdWidth + .25, $printProdHeight + .25));
// Get Image Dimensions 
list($width, $height) = getimagesize('../../Pages/Products/'.$productCategory.'/PrintSize/'.$productImage);
$width = ($width / 110);
$height = ($height / 110);

$pdf->AddFont($styleFont,'',$styleFont.'.php');
$pdf->AddPage();
$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);

// If headline is pre-select option, display transparent headline image
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
// Else if custom headline, display the custom text
else {
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(1.83,4.16);
$pdf->SetTextColor($styleFontStrokeR,$styleFontStrokeG,$styleFontStrokeB);
$pdf->MultiCell(23.7,3, $text, 0, "C");
$pdf->SetTextColor($styleFontHeadR,$styleFontHeadG,$styleHeadPriceB);
$pdf->SetXY(2,4);
$pdf->MultiCell(23.7,3, $text, 0, "C");
}
$pdf->Image('Images/Optional/foa-logo.png', 2, 33);
$pdf->Image('../../Pages/Products/'.$productCategory.'/PrintSize/'.$productImage, 4.3, 14, $width, $height);

if ($userPrice == 0) {
	// Do nothing
}
else {
$pdf->SetXY(8.8,28.2);
$pdf->SetTextColor($styleFontStrokeR,$styleFontStrokeG,$styleFontStrokeB);
$pdf->SetFont($styleFont,'', $styleFontPriceFontSize);
$pdf->MultiCell(float,2, '$'.$userPrice, 0, "C");
$pdf->SetXY(9,28);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->MultiCell(float,2, '$'.$userPrice, 0, "C");	
}
// Output poster file
$pdf->Output('Output/poster_'.$printProjName.'_'.$ranNum.'.pdf', F);
// Update the project entity in the database
$sqlUpdateName = $conn->prepare('
UPDATE PrintProject
SET printProjFile = "poster_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = 1
WHERE PrintProject.printProjId = '.$printProjId.'
');
$sqlUpdateName->execute();
header("Location:projectsHome.php");
}

// Generate Flyer PDF File
if($printProdType == "flyer") {
$pdf = new FPDF(''.$printProdOrientation.'', 'in', array($printProdWidth + .25, $printProdHeight + .25));

// Set Font Size for the number of characters entered
if ($headLength >= 25) {
	$styleFontHeadFontSize = $styleFontHeadFontSize * .80;
}
// Get Image Dimensions 
list($width, $height) = getimagesize('../../Pages/Products/'.$productCategory.'/PrintSize/'.$productImage);
$width = ($width / 300);
$height = ($height / 300);

$pdf->AddFont($styleFont,'',$styleFont.'.php');
$pdf->AddPage();
// Create top Headline "stroke"
$pdf->SetFont($styleFont,"",$styleFontHeadFontSize);
$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);

// If headline is pre-select option, display transparent headline image
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
else {
$pdf->SetTextColor($styleFontStrokeR,$styleFontStrokeG,$styleFontStrokeB);
$pdf->SetXY(0.97,1.25);
$pdf->MultiCell(8.45,1.4, $text, 0, "C");
// Create Top Headline "actual" Text
$pdf->SetFont($styleFont,"",$styleFontHeadFontSize);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->SetXY(1,1.2);
$pdf->MultiCell(8.45,1.4, $text, 0, "C");	
}

// Place FOA or User Logo
$pdf->Image('Images/Optional/foa-logo.png', .5, 9.8, 2, 1.2);
// Place Product Image
$pdf->Image('../../Pages/Products/'.$productCategory.'/PrintSize/'.$productImage, 1.8, 4.4, $width, $height);

if ($userPrice == 0) {
	// Do nothing
}
else {
// Create User Price "stroke"
$pdf->SetTextColor($styleFontStrokeR,$styleFontStrokeG,$styleFontStrokeB);
$pdf->SetFont($styleFont,"",$styleFontPriceFontSize);
$pdf->SetXY(3.4,7.95);
$pdf->MultiCell(float,2, '$'.$userPrice, 0, "C");
// Create User Price "actual" Text 
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->SetXY(3.45,8);
$pdf->MultiCell(float,2, '$'.$userPrice, 0, "C");	
}
// Output PDF
$pdf->Output('Output/flyer_'.$printProjName.'_'.$ranNum.'.pdf', F);
// Update the project entity in the database
$sqlUpdateName = $conn->prepare('
UPDATE PrintProject
SET printProjFile = "flyer_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = 1
WHERE PrintProject.printProjId = '.$printProjId.'
');
$sqlUpdateName->execute();
header("Location:projectsHome.php");
}

// Generate Description Cards
if ($printProdType == "description-card") {
$pdf = new FPDF(''.$printProdOrientation.'', 'in', array($printProdWidth + .25, $printProdHeight + .25));
$pdf->AddFont('Impact','','Impact.php');	
$pdf->AddFont('ariblk','','ariblk.php');
$pdf->SetMargins(0, 0, 0);
if ($printProdId)
$pdf->SetAutoPageBreak(false);
// This query gets the product information to display on the card.
$sql = '
SELECT PrintProject.printProjId, PrintProjectToProduct.printProjId,
PrintProjectToProduct.prodId, Product.prodId, Product.prodName, Product.prodCategory, 
Product.prodDescription
FROM PrintProject, PrintProjectToProduct, Product
WHERE PrintProject.printProjId = '.$printProjId.' AND 
PrintProject.printProjId = PrintProjectToProduct.printProjId AND
PrintProjectToProduct.prodId = Product.prodId
';
$rows = $conn->query($sql);
$numberOfCards = $rows->rowCount();

foreach ($rows as $row) {
	// Get the current product ID to be used in later queries
	$productId = $row['prodId'];
	$productName = iconv('UTF-8', 'windows-1252', $row['prodName']);
	$productDescription = iconv('UTF-8', 'windows-1252', $row['prodDescription']);
	// Switch statement reformats each product category to be correctly displayed on the card.
	switch($row['prodCategory']) {
		case "500Gram" :
		$productCategory = "500 Gram";
		break;
		case "MultiEffect" :
		$productCategory = "Multi-Effect";
		break;
		case "ReloadablesAndTubes" :
		$productCategory = "Reloadables and Tubes";
		break;
		case "RomanCandles" :
		$productCategory = "Roman Candles";
		break;
		case "Parachutes" :
		$productCategory = "Parachutes";
		break;
		case "Missiles" :
		$productCategory = "Missiles";
		break;
		case "Assortments" :
		$productCategory = "Assortments";
		break;
		case "Fountains" :
		$productCategory = "Fountains";
		break;
		case "Firecrackers" :
		$productCategory = "Firecrackers";
		break;
		case "Rockets" :
		$productCategory = "Rockets";
		break;
		case "NoveltiesAndSparklers" :
		$productCategory = "Novelties and Sparklers";
		break;
		case "Spinners" :
		$productCategory = "Spinners";
		break;
	}
	// Adds the product information to the current card
	$pdf->AddPage();
	$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);
	if (!empty($userCompLogo)) {
		$pdf->Image('Images/UserLogos/'.$userCompLogo.'', .125, .125, 4, 2);
	}
	$pdf->SetFont("Impact","",19);
	$pdf->SetTextColor($styleFontHeadR,$styleFontHeadG,$styleFontHeadB);
	$pdf->SetXY(.25,0.25);
	$pdf->MultiCell(4.6,0.35, $productName, 0, "L");
	$pdf->SetFont("ariblk","",10);
	$pdf->SetXY(.25,0.51);
	$pdf->MultiCell(4.6,0.2, $productCategory, 0, "L");
	// Logic to resize the text if the description is insanely long.
	if (strlen($productDescription) > 500) {
		$pdf->SetFont("ariblk","",7);
	} elseif (strlen($productDescription) > 330) {
		$pdf->SetFont("ariblk","",9);		
	} else {
		$pdf->SetFont("ariblk","",12);
	}

	$pdf->SetXY(.25,.78);
	$pdf->MultiCell(4.7,0.2, $productDescription, 0, "L");
	$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
	// This query collects the user supplied pricing to display on the card for the current product
	$sqlProductPrices = '
	SELECT PrintProject.printProjId, PrintProjectToPrintProjectDCardUserPrice.printProjId,
	PrintProjectToPrintProjectDCardUserPrice.dCardUserPriceId,
	PrintProjectDCardUserPrice.dCardUserPriceProdId, PrintProjectDCardUserPrice.dCardUserPricePrice1,
	PrintProjectDCardUserPrice.dCardUserPricePack1, PrintProjectDCardUserPrice.dCardUserPricePrice2,
	PrintProjectDCardUserPrice.dCardUserPricePack2, PrintProjectDCardUserPrice.dCardUserPricePrice3,
	PrintProjectDCardUserPrice.dCardUserPricePack3
	FROM PrintProject, PrintProjectToPrintProjectDCardUserPrice, 
	PrintProjectDCardUserPrice
	WHERE PrintProject.printProjId = '.$printProjId.' AND 
	PrintProjectToPrintProjectDCardUserPrice.printProjId = '.$printProjId.'
	AND PrintProjectToPrintProjectDCardUserPrice.dCardUserPriceId = PrintProjectDCardUserPrice.dCardUserPriceId 
	AND PrintProjectDCardUserPrice.dCardUserPriceProdId = '.$productId.'
	';
	$rowsProductPrices = $conn->query($sqlProductPrices);
	// Loops through each price and displays it on the card
	foreach ($rowsProductPrices as $rowProductPrices){
		// Price is split into dollars and cents for separate display, so they can be sized differently.
		$priceSplit1 = explode(".", $rowProductPrices['dCardUserPricePrice1']);
		$priceDollar1 = $priceSplit1[0];
		$priceCents1 = $priceSplit1[1];
		$priceSplit2 = explode(".", $rowProductPrices['dCardUserPricePrice2']);
		$priceDollar2 = $priceSplit2[0];
		$priceCents2 = $priceSplit2[1];
		$priceSplit3 = explode(".", $rowProductPrices['dCardUserPricePrice3']);
		$priceDollar3 = $priceSplit3[0];
		$priceCents3 = $priceSplit3[1];
		// Get the packing description the user supplied
		$pack1 = $rowProductPrices['dCardUserPricePack1'];
		$pack2 = $rowProductPrices['dCardUserPricePack2'];
		$pack3 = $rowProductPrices['dCardUserPricePack3'];
		// Each of these display code blocks checks to see if the price entered was zero.
		// If it was, do not display either the price or the packing description
		if ($priceDollar1 == "0" && $priceCents1 == "0") {
		}
		else {
		$pdf->SetFont("Impact","",36);
		$pdf->SetXY(.25,2.55);
		$pdf->Write(float, '$'.$priceDollar1);
		$pdf->SetFont("Impact","",22);
		$pdf->Write(float, $priceCents1);	
		}

		if ($priceDollar2 == "0" && $priceCents2 == "0") {
		}
		else {
		$pdf->SetFont("Impact","",36);
		$pdf->SetXY(1.8,2.55);
		$pdf->Write(float, '$'.$priceDollar2);
		$pdf->SetFont("Impact","",22);
		$pdf->Write(float, $priceCents2);
		}
		
		if ($priceDollar3 == "0" && $priceCents3 == "0") {
		}
		else {
		$pdf->SetFont("Impact","",36);
		$pdf->SetXY(3.47,2.55);
		$pdf->Write(float, '$'.$priceDollar3);
		$pdf->SetFont("Impact","",22);
		$pdf->Write(float, $priceCents3);	
		}

		if ($priceDollar1 == "0" && $priceCents1 == "0") {
		}
		else {
		$pdf->SetFont("ariblk","",14);
		$pdf->SetXY(.30,-.5);
		$pdf->MultiCell(1, .3, $pack1, 0, "C");	
		}

		if ($priceDollar2 == "0" && $priceCents2 == "0") {
		}
		else {
		$pdf->SetFont("ariblk","",14);
		$pdf->SetXY(2.1,-.5);
		$pdf->MultiCell(1, .3, $pack2, 0, "C");	
		}
		
		if ($priceDollar3 == "0" && $priceCents3 == "0") {
		}
		else {
		$pdf->SetFont("ariblk","",14);
		$pdf->SetXY(4,-.5);
		$pdf->MultiCell(1, .3, $pack3, 0, "C");	
		}
	}
}
// Output PDF
$pdf->Output('Output/desccard_'.$printProjName.'_'.$ranNum.'.pdf', F);
	// Update the project entity in the database, numberOfCards is used for calculating total in cart.
	$sqlUpdateName = $conn->prepare('
	UPDATE PrintProject
	SET printProjFile = "desccard_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = '.$numberOfCards.'
	WHERE PrintProject.printProjId = '.$printProjId.'
	');
	$sqlUpdateName->execute();
header("Location:projectsHome.php");
}

// Generate BLANK description card PDF File
if($printProdType == "description-card-blank") {
$pdf = new FPDF($printProdOrientation, "in", array($printProdWidth + .25, $printProdHeight + .25));
$pdf->AddPage();
$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);
	if (!empty($userCompLogo)) {
		list($width, $height) = getimagesize('Images/UserLogos/'.$userCompLogo.'');
		if ($width > 600) {
			$percentChange = ((($width - 600) / $width));
			$width = $width * $percentChange;
			$height = $height * $percentChange;
		}
		$width = 1.252;
		$height = .7;
		$pdf->Image('Images/UserLogos/'.$userCompLogo.'', .2, 2.42, $width, $height);	
	}
$pdf->Output('Output/desc-blank_'.$printProjName.'_'.$ranNum.'.pdf', F);
// Update the project entity in the database
$sqlUpdateName = $conn->prepare('
UPDATE PrintProject
SET printProjFile = "desc-blank_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = 1
WHERE PrintProject.printProjId = '.$printProjId.'
');
$sqlUpdateName->execute();
header("Location:projectsHome.php");
}

// Generate Banner PDF File
if($printProdType == "banner") {
	// --------------------- !
	// This logic is added per BASS Printing's Request.
	// Find out if banner passed is actually just an uneditable poster or flyer
	// Like the Duck Commander series. This changes the file name to fit those products
	// To give BASS a better understanding during production of what the file is.
	// Add logic for any new uneditable banners here to assist with file naming.
	if ($printProdId == 22) {
		$bannerType = 'duck-poster';
	}
	elseif ($printProdId == 23) {
		$bannerType = 'duck-flyer';
	}
	else {
		$bannerType = 'banner';
	}
$pdf = new FPDF($printProdOrientation, "in", array($printProdWidth + .25, $printProdHeight + .25));
$pdf->AddPage();
$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);
$pdf->Output('Output/'.$bannerType.'_'.$printProjName.'_'.$ranNum.'.pdf', F);
// Update the project entity in the database
$sqlUpdateName = $conn->prepare('
UPDATE PrintProject
SET printProjFile = "'.$bannerType.'_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = 1
WHERE PrintProject.printProjId = '.$printProjId.'
');
$sqlUpdateName->execute();
header("Location:projectsHome.php");
}

// Generate Editable Banner PDF File
if($printProdType == "editable-banner") {
$pdf = new FPDF($printProdOrientation, "in", array($printProdWidth + .25, $printProdHeight + .25));
$pdf->AddPage();
$pdf->AddFont($styleFont,'',$styleFont.'.php');
$pdf->Image($styleBackground, 0, 0, $pdf->w, $pdf->h);

// This is a shitty way of doing it, but for now set the positions of the text by print product ID. This allows
// compensation for the larger size of each banner size. Font size is still set from database.
if ($printProdId == 5) {
// Display headline STROKE
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0.15,13.15);
$pdf->SetTextColor(190,30,35);
$pdf->MultiCell($pdf->w,4, $text, 0, "C");
// If headline is pre-select option, display transparent headline image
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
else {
// Display headline
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0,13);
$pdf->SetTextColor($styleFontHeadR,$styleFontHeadR,$styleFontHeadR);
$pdf->MultiCell($pdf->w,4, $text, 0, "C");	
}
// Display subheadline
$pdf->SetFont($styleFont,'', $styleFontPriceFontSize);
$pdf->SetXY(2.5,30);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->MultiCell(45,4, $subText, 0, "L");
}

if ($printProdId == 6) {
// Display headline STROKE
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0.15,17.15);
$pdf->SetTextColor(190,30,35);
$pdf->MultiCell($pdf->w,6, $text, 0, "C");
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
else {
// Display headline
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0,17);
$pdf->SetTextColor($styleFontHeadR,$styleFontHeadR,$styleFontHeadR);
$pdf->MultiCell($pdf->w,6, $text, 0, "C");	
}

// Display subheadline
$pdf->SetFont($styleFont,'', $styleFontPriceFontSize);
$pdf->SetXY(2.5,42);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->MultiCell(70,3, $subText, 0, "L");
}

if ($printProdId == 7) {
// Display headline STROKE
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0.15,20.15);
$pdf->SetTextColor(190,30,35);
$pdf->MultiCell($pdf->w,7, $text, 0, "C");
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
else {
// Display headline
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0,20);
$pdf->SetTextColor($styleFontHeadR,$styleFontHeadR,$styleFontHeadR);
$pdf->MultiCell($pdf->w,7, $text, 0, "C");	
}

// Display subheadline
$pdf->SetFont($styleFont,'', $styleFontPriceFontSize);
$pdf->SetXY(2.5,52);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->MultiCell(100,3, $subText, 0, "L");
}

if ($printProdId == 8) {
// Display headline STROKE
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0.15,26.15);
$pdf->SetTextColor(190,30,35);
$pdf->MultiCell($pdf->w - .5,8, $text, 0, "C");
if ($headIsImage > 1) {
	$pdf->Image($headImgFile, $headImgX, $headImgY, $headImgWidth, float);
}
else {
// Display headline
$pdf->SetFont($styleFont,'', $styleFontHeadFontSize);
$pdf->SetXY(0,26);
$pdf->SetTextColor($styleFontHeadR,$styleFontHeadR,$styleFontHeadR);
$pdf->MultiCell($pdf->w - .5,8, $text, 0, "C");
}

// Display subheadline
$pdf->SetFont($styleFont,'', $styleFontPriceFontSize);
$pdf->SetXY(2.5,63);
$pdf->SetTextColor($styleFontPriceR,$styleFontPriceG,$styleFontPriceB);
$pdf->MultiCell(120,3, $subText, 0, "L");
}

// Output editable Banner
$pdf->Output('Output/edit-banner_'.$printProjName.'_'.$ranNum.'.pdf', F);
// Update the project entity in the database
$sqlUpdateName = $conn->prepare('
UPDATE PrintProject
SET printProjFile = "edit-banner_'.$printProjName.'_'.$ranNum.'.pdf", printProjQty = 1
WHERE PrintProject.printProjId = '.$printProjId.'
');
$sqlUpdateName->execute();
header("Location:projectsHome.php");
}
?>