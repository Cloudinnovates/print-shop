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

// Get price of project being removed
$sqlGetPrice = '
SELECT PrintShopCartItem.price, PrintShopCartItem.weight
FROM PrintShopCartItem
WHERE PrintShopCartItem.cartId = '.$_SESSION['cartId'].' AND printProjId = '.$printProjId.'
';

$rows = $conn->query($sqlGetPrice);

foreach ($rows as $row) {
	$price = $row['price'];
	$weight = $row['weight'];
}

// Get current cart total and create new total with chosen product price removed
$sqlGetCartTotal = '
SELECT PrintShopCart.cartTotalPrice, PrintShopCart.cartTotalWeight
FROM PrintShopCart
WHERE PrintShopCart.cartId = '.$_SESSION['cartId'].'
';

$rows = $conn->query($sqlGetCartTotal);

foreach ($rows as $row) {
	$cartTotal = $row['cartTotalPrice'];
	$cartTotalWeight = $row['cartTotalWeight'];
}

$newTotal = $cartTotal - $price;
$newWeight = $cartTotalWeight - $weight;
// Delete chosen cart item
$sqlDeleteCartItem = '
DELETE FROM PrintShopCartItem
WHERE printProjId = '.$printProjId.'
';

$conn->exec($sqlDeleteCartItem);

// Update Cart Total
$sqlUpdateTotal = '
UPDATE PrintShopCart
SET cartTotalPrice = '.$newTotal.', cartTotalWeight = '.$newWeight.'
WHERE cartId = '.$_SESSION['cartId'].'
';

$conn->exec($sqlUpdateTotal);
?>
