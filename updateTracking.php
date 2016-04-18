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

if (!isset($_SESSION['username'])) {
	header("Location:loginPage.php");
}

if (!($acctType == "Admin" || $acctType == "Print")) {
	header("Location:loginPage.php");
}
$currDate = date("m-d-Y");
$orderNo = $_GET['orderNo'];

$newTrackingNo = $_POST['newTrackNo'];
$pickupTrackingNo = 999999;
$stmt = $conn->prepare('
UPDATE PrintShopCart
SET PrintShopCart.cartTrackingNo = :newTrackingNo, 
PrintShopCart.cartShippedDate = :cartShippedDate
WHERE PrintShopCart.cartId = :cartId
');
if (isset($_POST['pickedUp'])) {
	// Free pickup logic was added after. The num 999999 is used to tell the program it was 
	// a free pickup
	$stmt->bindParam(':newTrackingNo', $pickupTrackingNo);
} else {
	$stmt->bindParam(':newTrackingNo', $newTrackingNo);
}
$stmt->bindParam(':cartShippedDate', $currDate);
$stmt->bindParam(':cartId', $orderNo);
$stmt->execute();

header('Location: printerPortal.php');
?>
