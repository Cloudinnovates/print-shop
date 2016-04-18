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
$newPhoto = basename($_FILES["newLogo"]["name"]);
$deactivate = $_POST['deactivateLogo'];

// Try to upload the image provided
try {
$target_dir = "Images/UserLogos/";
$target_file = $target_dir . basename($_FILES["newLogo"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

if (!empty($_FILES['newLogo']['name'])) {
$userPhoto = $_FILES['newLogo']['name'];

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["newLogo"]["tmp_name"]);
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
    if (move_uploaded_file($_FILES["newLogo"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["newLogo"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$sql = 'UPDATE UserDetail SET 
userCompLogo="'.$newPhoto.'"
 WHERE userId='.$userId.'';
$stmt = $conn->prepare($sql);
$stmt->execute();

}
}
catch (PDOException $e){
echo $e->getMessage();	
}

if ($deactivate == "yes") {
$sql = 'UPDATE UserDetail SET 
userCompLogo=""
 WHERE userId='.$userId.'';
$stmt = $conn->prepare($sql);
$stmt->execute();
}	

echo '<script>
window.location = "userImages.php";
</script>';
?>
