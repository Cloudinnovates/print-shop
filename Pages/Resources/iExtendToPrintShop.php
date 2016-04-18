<?php
session_start();
include '../../../../functions.php';

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
<meta name="robots" content="noindex">
  <title>Fireworks Over America - Print Shop</title>
  <meta name="description" content="Create and print high quality marketing materials for your business.">
  <meta name="author" content="Scott Knox">

  <link rel="stylesheet" href="../../Styles/layout.css">
    <script src="../../Scripts/jquery-1.11.3.min.js"></script>
  
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
<header>
<img class="headerLogo" src="../../Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop</h1>
<div id="userAccount">
<img class="userPhoto" src="../../../../userPhotos/<?php 
if (!($photoUrl)) {
echo 'defaultPhotoSmall.jpg';
}
else {
echo $photoUrl; 	
}
?>" alt="user-photo" />
<p class="usernameText">Hello, <br/><?php echo $firstName." ".$lastName ?><br/><br/>
<a href="../logout.php">Log Out</a>
</p>
</div>
</header>
<nav>
<ul id="navLinks">
<a class="navLink" href="../../index.php"><li>Home</li></a>
<a class="navLink" href="../../create.php"><li>Create</li></a>
<a class="navLink" href="../../projectsHome.php"><li>My Print Shop</li></a>
<a class="navLink" href="../../myAccount.php"><li>My Account</li></a>
<a class="navLink" href="../../resources.php"><li>Resources</li></a>
</ul>
</nav>
<div id="wrapper">
<div id="leftContent">
<ul id="sideLinks">
<a href="../../resources.php" class="sideLink"><li class="active-step">Resources</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>I Previously Used iExtend, What's New?</h2>
<p>Learn about the new features and functionality behind the new Print Shop system.</p>
<hr/>
<h3>Improved User Interface</h3>
<img src="Images/uichange.jpg" alt="ui-change" />
<p>Print Shop includes an updated user interface with less clutter, and more direct 
access to the features you need. When entering Print Shop, you are presented
with a home page that gives you the latest announcements, top questions, and a contact
form for instant contact with a Fireworks Over America tech representative.</p>
<p>Across the site, you will find many changes that can help you be more productive and get 
the products your business needs. We've also simplified the payment system significantly and made
it more secure and mobile friendly. iExtend was incompatible with mobile devices.p>

<h3>An Improved Home Page</h3>
<img src="Images/homeChange.jpg" alt="home-change" />
<p>Print Shop now features a home page that displays some of the our top questions,
an easy to use contact form, and the latest announcements.</p>

<h3>My Projects</h3>
<img src="Images/myProjects.jpg" alt="projects-change" />
<p>The updated My Projects page gives you access to the print projects you have created. Here, you
can place items in your shopping cart, manage your projects, and delete them. There is also an option to
self print a project. You can use a variety of online printing resources to print these projects for a
more affordable price than what you might find on Print Shop. Please note, Print Shop credit is not applicable 
to your purchases made on other websites. One possibility might be to print some items that are difficult to
print with online services with Print Shop, and then more common items (like posters or flyers) elsewhere. 
Uprinting.com is an example of an alternative printing service.</p>

<h3>My Account</h3>
<img src="Images/myAccount.jpg" alt="account-change" />
<p>On your new account page, you can view all of your past orders, the shipping status of those orders,
change your account information (like your address), and upload a user logo. This feature is not fully implemented at
this time. Once you upload a suitable image, you will be able to apply it to compatible print projects. This could allow 
you to show your business logo on marketing materials you print with us. Note again, this feature is not complete at this time.</p>

<h3>Improved Address Entry</h3>
<img src="Images/newAddress.jpg" alt="address-change" />
<p>Our improved address entry page allows you to fill out the entire form in one click! Just be sure your
address on file is filled out correctly and click the "use address on file" button.</p>

<h3>Calculate the Correct Payment Faster!</h3>
<img src="Images/payChange.jpg" alt="pay-change" />
<p>No more switching between various parts of a form to correctly enter the amounts to pay with Print Shop.
Unlike iExtend, you can now simply type the amount to pay with Print Shop and it will auto enter the correct
amount into the pay with credit section. Simply hit "continue" after this and your on your way. If you don't 
have any Print Shop credit, the site will simply display the full amount to pay with credit, and allow you to continue.</p>

<h3>Pay More Securely, On Mobile!</h3>
<img src="Images/payCardChange.jpg" alt="pay-card-change" />
<p>By integrating with an online web service, the information enter is more secure than ever. Your payment is processed through a very 
secure connection. It also makes it extremely easy to pay on a mobile device.</p>


<h3>Product Selection Is Easier Than Ever</h3>
<img src="Images/productSelect.jpg" alt="product-change" />
<p>Selecting products is easier than ever! You can now easily and quickly browse between various categories of product.
Simply check each product you would like to include on your print project. You can also use the new "type to find"
feature to instantly find a product you are searching for. If you need to choose all products in a list, simply hit "select all" or
"select none" to instantly choose all of them. Much less clicking!</p>

<a href="#">Back To Top</a>
</div>

</div>
</div>

<script>

</script>
</body>
</html>
