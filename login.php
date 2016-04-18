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

if (isset($_POST['submit'])) {

	$username = htmlentities($_POST['username']);
	$password = htmlentities($_POST['password']);
	
$sql = 'SELECT * 
FROM User, UserAddress, UserDetail
WHERE userUsername = "'.$username.'" AND userPassword = "'.$password.'" AND User.userId = UserAddress.userId AND UserAddress.userId = UserDetail.userId';
	$rows = $conn->query($sql);
	$rowCount = $rows->rowCount();
	
	if ($rowCount > 0) {
		foreach ($rows as $row) {
		if ($row['userUsername'] === $username && $row['userPassword'] === $password) {
			session_start();
			$_SESSION['userId'] = $row['userId'];
			$_SESSION['username'] = $row['userUsername'];
			$_SESSION['password'] = $row['userPassword'];
			
			if ($row['userType'] == "Print") {
				echo "<script>window.location = 'printerPortal.php';</script>";
			}
			else {
					echo "<script>window.location = 'index.php';</script>";	
			}
		}
	}
	}
		else {
				echo "<script>window.location = 'loginIncorrectPage.php';</script>";
			}
}

?>
