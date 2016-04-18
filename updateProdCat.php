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

<?php
$sort = $_GET['sort'];
$category = $_GET['cat'];

// ---------------------------------------------------------------------------
// ---------------------------------------------------------------------------
// If Product is anything but POSTER or FLYER
if ($product != 1 && $product != 2) {

	if($category == "All") {
	// Use the sort and category variables to find products
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodIsSuppressed = 0
ORDER BY Product.".$sort."
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
else {
// Use the sort and category variables to find products
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodCategory='".$category."' AND Product.prodIsSuppressed = 0
ORDER BY Product.".$sort."
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
}
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------

// If Product is equal to POSTER or FLYER
if ($product == 1 || $product == 2) {
	if($category == "All") {
	// Use the sort and category variables to find products
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb,
ProductMedia.prodImgPrint
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodIsSuppressed = 0
ORDER BY Product.".$sort."
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
else {
// Use the sort and category variables to find products
$sql = "SELECT Product.prodId, Product.prodName, Product.prodCategory, Product.prodDescription, ProductMedia.prodImgThmb,
ProductMedia.prodImgPrint
FROM Product, ProductMedia 
WHERE Product.prodId = ProductMedia.prodId AND Product.prodCategory='".$category."' AND Product.prodIsSuppressed = 0
ORDER BY Product.".$sort."
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
}

?>
