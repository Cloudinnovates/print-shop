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
<img class="userPhoto" src="../../userPhotos/<?php 
if (empty($photoUrl)) {
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
<a href="myAccount.php" class="sideLink"><li>My Account</li></a>
<a href="myProfile.php" class="sideLink"><li class="active-step">My Profile</li></a>
<a href="userImages.php" class="sideLink"><li>User Image</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>Edit Your Profile</h2>
<p>Edit your profile information here. Scroll to the bottom and hit "Submit" to update your information.</p>
<hr/>
</div>
<h2>Profile</h2>

<form action="edit.php" method="POST" enctype="multipart/form-data">
<table cellpadding="5">
<tr><td>
<?php
if (empty($photoUrl)) {
echo '<img class="userPhoto" src="../../userPhotos/defaultPhotoSmall.jpg" alt="userPhoto" />'; 

}
else {
echo '<img class="userPhoto" src="../../userPhotos/'.$photoUrl.'" alt="userPhoto" />'; 
	
}
?><br/>
<label for="userPhoto">Upload New Photo</label><br/><br/>
<input type="file" name="userphoto" value="upload" />
</td>
<td><h3>User Details</h3>
<label for="firstName">First Name:</label>
<input type="text" name="firstName" value="<?php echo $firstName ?>" /><br/><br/>
<label for="lastName">Last Name:</label>
<input type="text" name="lastName" value="<?php echo $lastName ?>" /><br/><br/>
<label for="email">Email:</label>
<input type="text" name="email" size="30" value="<?php echo $email ?>" /><br/><br/>
<label for="phone">Phone:</label>
<input type="text" name="phone" value="<?php echo $phoneNumber ?>" /><br/><br/>
<label for="company">Company Name:</label>
<input type="text" name="company" value="<?php echo $company ?>" /><br/><br/>
</td></tr>
<tr><td></td><td>
<h3>Address</h3>
<label for="streetAdd">Street Address:</label>
<input type="text" name="streetAdd" value="<?php echo $streetAdd ?>" /><br/><br/>
<label for="cityAdd">City:</label>
<input type="text" name="cityAdd" value="<?php echo $cityAdd ?>" /><br/><br/>
<label for="stateAdd">State:</label>
<input type="text" name="stateAdd" value="<?php echo $stateAdd ?>" /><br/><br/>
<label for="zipAdd">Zip Code:</label>
<input type="text" name="zipAdd" value="<?php echo $zipAdd ?>" /><br/><br/>
</td></tr>
<tr><td></td><td>
<label for="bio">User Bio:</label><br/>
<textarea name="bio" rows="10" cols="40">
<?php 
if (isset($bio)) {
	echo $bio;
}
else {
	echo "You can enter details about you or your company here.";
}
?>
</textarea><br/>
<input style="width:100px; height:50px; font-weight:bold;" type="submit" name="submit" value="Submit" />
</td></tr>
</table>
</form>

</div>
</div>

<script>

</script>
</body>
</html>
