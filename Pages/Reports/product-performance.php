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
<img class="headerLogo" src="../../Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop Metrix</h1>
<div id="userAccount">
<img class="userPhoto" src="../../../../../userPhotos/<?php 
if (!($photoUrl)) {
echo 'defaultPhotoSmall.jpg';
}
else {
echo $photoUrl; 	
}
?>" alt="user-photo" />
<p class="usernameText">Hello, <br/><?php echo $firstName." ".$lastName ?><br/><br/>
<a href="../../logout.php">Log Out</a>
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
<a href="reports.php" class="sideLink"><li>Reports Home</li></a>
<a href="orders.php" class="sideLink"><li>Orders</li></a>
<a href="product-performance.php" class="sideLink"><li class="active-step">Product Performance</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>Top 10 Products and Styles</h2>
<hr/>
</div>

<h1>Top Products</h1>
<table>
<tr><td>Product Name</td><td>Qty Ordered</td><td>Total Orders</td><td>Total Spent</td></tr>
<?php
// ------------------------------------
// Process top performing products
$sql_top_products = 'SELECT PrintShopCart.cartId, PrintShopCart.cartIsFinalized,
PrintShopCartItem.cartId, PrintShopCartItem.printProjId, PrintShopCartItem.qty, 
PrintShopCartItem.price, PrintProject.printProjId, PrintProject.printProdId, 
PrintProduct.printProdId, PrintProduct.printProdName, SUM(PrintShopCartItem.qty) AS totalQty,
SUM(PrintShopCartItem.price) AS totalPrice, COUNT(PrintShopCart.cartId) AS totalOrders
FROM PrintShopCart, PrintShopCartItem, PrintProject, PrintProduct
WHERE PrintShopCart.cartIsFinalized = 1 AND PrintShopCart.cartId = PrintShopCartItem.cartId
AND PrintShopCartItem.printProjId = PrintProject.printProjId AND PrintProject.printProdId =
PrintProduct.printProdId
GROUP BY PrintProduct.printProdName
ORDER BY totalPrice DESC
LIMIT 10
';

$product_details = $conn->query($sql_top_products);

foreach ($product_details as $product_detail) {
	$product_name = $product_detail['printProdName'];
	$product_total_qty = $product_detail['totalQty'];
	$product_total_spent = $product_detail['totalPrice'];
	$total_orders = $product_detail['totalOrders'];
	
	echo '<tr><td>'.$product_name.'</td><td>'.$product_total_qty.'</td>
	<td>'.$total_orders.'</td><td>'.$product_total_spent.'</td></tr>';
}
?>
</table>

</div>
</div>

<script>

</script>
</body>
</html>
