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

$product = $_SESSION['product'];
$style = $_SESSION['style'];
$projectName = $_SESSION['projectName'];

?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

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

<?php
$search = $_GET['s'];
// uses user's search query to find products

// ---------------------------------------------------------------------------
// ---------------------------------------------------------------------------
// If Product is anything but POSTER or FLYER
if ($product != 1 && $product != 2) {
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodName LIKE '%".$search."%' AND Product.prodIsSuppressed = 0
ORDER BY Product.prodName
";
$rows = $conn->query($sql);
// echo products
foreach ($rows as $row) {
	$prodThumb = $row['prodImgThmb'];
	
	if ($prodThumb == "" || $prodThumb == null) {
		$prodThumb = "defaultProduct_Thumb.png";
	}
echo '
<div class="product">
<input type="checkbox" id="'.$row['prodId'].'" class="product-check" value="'.$row['prodName'].'"/>
<img class="product-image" src="../../Pages/Products/'.$row['prodCategory'].'/'.$prodThumb.'" />
<p class="product-text">'.$row['prodName'].'<br/><br/>'.$row['prodDescription'].'</p>
</div>
';	
}
}
// ---------------------------------------------------------------------------
// ---------------------------------------------------------------------------

// If Product is equal to POSTER or FLYER
if ($product == 1 || $product == 2) {
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb,
ProductMedia.prodImgPrint
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodName LIKE '%".$search."%' AND Product.prodIsSuppressed = 0
ORDER BY Product.prodName
";
$rows = $conn->query($sql);
// echo products
foreach ($rows as $row) {
	$prodThumb = $row['prodImgThmb'];
	
	if ($prodThumb == "" || $prodThumb == null) {
		$prodThumb = "defaultProduct_Thumb.png";
	}
	if (!(empty($row['prodImgPrint']))) {
echo '
<div class="product">
<input type="checkbox" id="'.$row['prodId'].'" class="product-check" value="'.$row['prodName'].'"/>
<img class="product-image" src="../../Pages/Products/'.$row['prodCategory'].'/'.$prodThumb.'" />
<p class="product-text">'.$row['prodName'].'<br/><br/>'.$row['prodDescription'].'</p>
</div>
';	
	}
}
}
?>
