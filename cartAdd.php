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

?>

<?php
$printProjId = $_GET['projId'];
$qty = $_GET['qty'];

// Check Qty and insert proper sku into a new cart item
$sqlGetSku = '
SELECT PrintProject.printProjId, PrintProject.printProdId, PrintProject.printProjQty,
PrintProduct.printProdId, PrintProduct.printProdType, PrintProductToPrintProductSku.printProdId, 
PrintProductToPrintProductSku.skuId, PrintProductSku.skuId, 
PrintProductSku.skuLimit, PrintProductSku.skuPrice, PrintProductSku.skuWeight
FROM PrintProject, PrintProduct, PrintProductToPrintProductSku, PrintProductSku
WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProdId =
PrintProduct.printProdId AND PrintProduct.printProdId = PrintProductToPrintProductSku.printProdId
AND PrintProductToPrintProductSku.skuId = PrintProductSku.skuId
';

$rows = $conn->query($sqlGetSku);

foreach ($rows as $row) {
// This is the qty of items in the print project, times the qty of print projects to add
	$printProdId = $row['printProdId'];
	$qtyProject = $qty * $row['printProjQty'];	
	
	$laminate = $_GET['lam'];
	$laminated = 0;
	
	$printProdType = $row['printProdType'];
	// look through SKUs to find the one under the limit chosen by the customer
	if ($qtyProject <= $row['skuLimit']) {
		$skuPrice = $row['skuPrice'];
		$skuWeight = $row['skuWeight'];
		
		if ($printProdType == 'description-card' || $printProdType == 'description-card-blank'
			|| $printProdType == 'editable-banner' || $printProdType == 'banner') {
				// The logic is getting shitty quick. This is for if a person has chosen
				// To laminate their cards. 
				if ($laminate == "true" && $printProdType == "description-card" ||
				$printProdType == "description-card-blank") {
					$laminated = 1;
					$laminationCharge = $qtyProject * .10;
					$productPrice = number_format(($skuPrice * $qtyProject) + $laminationCharge, 2);
				} else {
						$productPrice = number_format($skuPrice * $qtyProject, 2);
				}
				// Quick fix: Duck commnader posters/flyers are considered banners right now,
				// Which causes them to throw errors because the QTY of "2" is multipled by 
				// QTYProj of 1. Which makes the poster price 138. Wrong. I'm so sorry....
				if ($printProdId == 22 || $printProdId == 23) {
					$productPrice = number_format($skuPrice, 2);
				}
		}
		else {
			$productPrice = number_format($skuPrice, 2);
		}
		// Banners, description cards, and blank cards
		// can have manual input for their qty, rather than selectbox options.
		// This means a user can order multiple of that particular SKU. 
		// This logic multiplies the SKU wieght * qty entered for these products
		// The print prod IDs are for the duck posters / flyers
		if ($printProdType == "banner" && !($printProdId == 22) 
				&& !($printProdId == 23)) {
			$skuWeight = $skuWeight * $qty;
		}
		
		$productPrice = str_replace( ',', '', $productPrice );
		
		$sqlInsertItem = '
		INSERT INTO PrintShopCartItem (cartId, printProjId, skuId, qty, price, weight, laminated)
		VALUES ('.$_SESSION['cartId'].', '.$printProjId.', '.$row['skuId'].',
		 '.$qty.', '.$productPrice.', '.$skuWeight.', '.$laminated.');
		';

		$conn->exec($sqlInsertItem);
		break;
	}
}

// Get current Cart total and add product price to the total
$sqlGetCurrentTotal = '
SELECT PrintShopCart.cartId, PrintShopCart.cartTotalPrice, PrintShopCart.cartTotalWeight
FROM PrintShopCart
WHERE PrintShopCart.cartId = '.$_SESSION['cartId'].'
';

$rows = $conn->query($sqlGetCurrentTotal);

foreach($rows as $row) {
	$currentCartTotal = $row['cartTotalPrice'];
	$currentCartWeight = $row['cartTotalWeight'];
}

$newTotal = $currentCartTotal + $productPrice;
$cartTotalWeight = $currentCartWeight + $skuWeight;
// Update the cart total
$sqlUpdateTotal = '
UPDATE PrintShopCart
SET cartTotalPrice = '.$newTotal.', cartTotalWeight = '.$cartTotalWeight.'
WHERE cartId = '.$_SESSION['cartId'].'
';

$conn->exec($sqlUpdateTotal);
?>
