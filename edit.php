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
	echo "<script>window.location = ('loginPage.php');</script>";
}



// Get the profile information previously entered
$newFirstName = $_POST['firstName'];
$newLastName = $_POST['lastName'];
$newEmail = $_POST['email'];
$newPhone = $_POST['phone'];
$newCompany = $_POST['company'];
$newStreetAdd = $_POST['streetAdd'];
$newCityAdd = $_POST['cityAdd'];
$newStateAdd = $_POST['stateAdd'];
$newZipAdd = $_POST['zipAdd'];
$newBio = $_POST['bio'];
$newPhoto = basename($_FILES["userPhoto"]["name"]);

// Try to upload the image provided
try {
$target_dir = "../../userPhotos/";
$target_file = $target_dir . basename($_FILES["userPhoto"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

if (!empty($_FILES['userPhoto']['name'])) {
$userPhoto = $_FILES['userPhoto']['name'];

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["userPhoto"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, a file with that name already exists. Please rename the image file.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["userPhoto"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["userPhoto"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}	
$sql = 'UPDATE User SET 
userFirstName="'.$newFirstName.'",
userLastName="'.$newLastName.'",
userEmail ="'.$newEmail.'",
userCompany ="'.$newCompany.'",
userPhone='.$newPhone.'
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();

$sql = 'UPDATE UserAddress SET 
addStreet="'.$newStreetAdd.'",
addCity="'.$newCityAdd.'",
addState="'.$newStateAdd.'",
addZip="'.$newZipAdd.'"
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();


$sql = 'UPDATE UserDetail SET 
userBio ="'.$newBio.'",
userPhotoUrl ="'.$newPhoto.'"
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();
}
else {
$sql = 'UPDATE User SET 
userFirstName="'.$newFirstName.'",
userLastName="'.$newLastName.'",
userEmail ="'.$newEmail.'",
userCompany ="'.$newCompany.'",
userPhone='.$newPhone.'
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();

$sql = 'UPDATE UserAddress SET 
addStreet="'.$newStreetAdd.'",
addCity="'.$newCityAdd.'",
addState="'.$newStateAdd.'",
addZip="'.$newZipAdd.'"
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();


$sql = 'UPDATE UserDetail SET 
userBio ="'.$newBio.'"
 WHERE userId="'.$userId.'"';
$stmt = $conn->prepare($sql);
$stmt->execute();	
}
$stmt = $conn->prepare($sql);
$stmt->execute();




}
catch (PDOException $e){
echo $e->getMessage();	
}

echo '<script>
window.location = "myProfile.php";
</script>';
?>
