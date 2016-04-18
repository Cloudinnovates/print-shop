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
<img class="headerLogo" src="../../Images/FoaLogo.png" alt="logo" /><h1 class="headerTitle">Print Shop Reports</h1>
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
<a href="reports.php" class="sideLink"><li class="active-step">Reports Home</li></a>
<a href="orders.php" class="sideLink"><li>Orders</li></a>
<a href="product-performance.php" class="sideLink"><li>Product Performance</li></a>
</ul>
</div>
<?php
// ----------------------------------------
// ----------------------------------------
// Process total and % change on Projects
// ----------------------------------------

// Variables to store the total projects for each year
$total_proj_prev_year = 0;
$total_proj_curr_year = 0;
// Get the current year and calculate the previous year
$current_year = date('Y');
$last_year = $current_year - 1;

// Get total projects and their dates created
$sql = 'SELECT count(*)
FROM PrintProject';
$stmt = $conn->prepare($sql);
$stmt->execute();

// Get the total number of projects that have been created
$total_projects = $stmt->fetchColumn();

// Get all Print Project Dates
$sql = 'SELECT PrintProject.printProjDate
FROM PrintProject';
$rows = $conn->query($sql);

// Sort through all projects and process the dates and totals
foreach ($rows as $row) {
	// Get the date of the project
	$proj_year = strtotime($row['printProjDate']);
	$year = date("Y", $proj_year);

	// If project is current year, add to that. Otherwise, add to last years total
	if ($year == $current_year) {
		$total_proj_curr_year++;
	}
	elseif ($year == $last_year) {
		$total_proj_prev_year++;
	}
}

// Calculate % increase or decrease
$proj_increase = false;

if ($total_prev_year == 0) {
$proj_percent_change = number_format($total_proj_curr_year * 100, 2);	
} 
else {
$proj_percent_change = number_format(((($total_proj_curr_year - $total_proj_prev_year) / $total_proj_prev_year) * 100), 2);	
}
// If percent change is positive, increase is true. Not, is a decrease.
if ($proj_percent_change > 0) {
	$proj_increase = true;
	$proj_style = 'em-percent-inc';
}
else {
	$proj_increase = false;
	$proj_style = 'em-percent-dec';
}
// ----------------------------------------
// ----------------------------------------

// ----------------------------------------
// ----------------------------------------
// Process total and % change on Orders
// ----------------------------------------

// Variables to store the total Orders for each year
$total_ord_prev_year = 0;
$total_ord_curr_year = 0;
$total_spent_prev_year = 0.00;
$total_spent_curr_year = 0.00;
$all_time_total = 0.00;

// Get total Orders and their dates created
$sql = 'SELECT count(*)
FROM PrintShopCart
WHERE PrintShopCart.cartIsFinalized = 1';
$stmt = $conn->prepare($sql);
$stmt->execute();

// Get the total number of Orders that have been created
$total_orders = $stmt->fetchColumn();

// Get all Print Order Dates
$sql = 'SELECT PrintShopCart.cartDateFinalized, PrintShopCart.cartShipping,
PrintShopCart.cartTotalPrice
FROM PrintShopCart
WHERE PrintShopCart.cartIsFinalized = 1';
$rows = $conn->query($sql);

// Sort through all Orders and process the dates and totals
foreach ($rows as $row) {
	// Get the date of the project
	$ord_year = strtotime($row['cartDateFinalized']);
	$year = date("Y", $ord_year);
	// Get the cart total
	$shipping = $row['cartTotalShipping'];
	$totalPrice = $row['cartTotalPrice'];
	$cartTotal = $shipping + $totalPrice;
	// Calculate the all time total spending
	$all_time_total = $all_time_total + $cartTotal;
	
	// If order is current year, add to that. Otherwise, add to last year's total
	if ($year == $current_year) {
		$total_ord_curr_year++;
		$total_spent_curr_year = $total_spent_curr_year + $cartTotal;
	}
	elseif ($year == $last_year) {
		$total_ord_prev_year++;
		$total_spent_prev_year = $total_spent_prev_year + $cartTotal;
	}

}

// Calculate % increase or decrease for orders
$ord_increase = false;

if ($total_ord_prev_year == 0) {
$ord_percent_change = number_format($total_ord_curr_year * 100, 2);	
} 
else {
$ord_percent_change = number_format(((($total_ord_curr_year - $total_ord_prev_year) / $total_ord_prev_year) * 100), 2);	
}
// If percent change is positive, increase is true. Not, is a decrease.
if ($ord_percent_change > 0) {
	$ord_increase = true;
	$ord_style = 'em-percent-inc';
}
else {
	$ord_increase = false;
	$ord_style = 'em-percent-dec';
}

// Calculate % increase or decrease for total spending
$spent_increase = false;

if ($total_spent_prev_year == 0) {
$spent_percent_change = number_format($total_spent_curr_year * 100, 2);	
} 
else {
$spent_percent_change = number_format(((($total_spent_curr_year - $total_spent_prev_year) / $total_spent_prev_year) * 100), 2);	
}
// If percent change is positive, increase is true. Not, is a decrease.
if ($spent_percent_change > 0) {
	$spent_increase = true;
	$spent_style = 'em-percent-inc';
}
else {
	$spent_increase = false;
	$spent_style = 'em-percent-dec';
}
// ----------------------------------------
// ----------------------------------------

?>
<div id="rightContent">
<div id="page-description">
<h2>Overview</h2>
<hr/>
</div>
<h2 style="text-align:center;">Print Shop Overview</h2>
<hr/>
<p>All-Time Total Projects Created: <span class="em-number"><?php echo $total_projects; ?></span></p>
<p>All-Time Total Orders: <span class="em-number"><?php echo $total_orders; ?></span></p>
<p>All-Time Total Spent: <span class="em-number">$<?php echo $all_time_total; ?></span></p>
<p>----------------------------------------------</p>
<!-- Project yearly comparison -->
<p>Projects Created In <?php echo $current_year; ?>/<?php echo $last_year; ?>: 
<span class="em-number"><?php echo $total_proj_curr_year; ?></span> / 
<span class="em-number"><?php echo $total_proj_prev_year; ?></span> 
(<span class="<?php echo $proj_style; ?>"><?php echo $proj_percent_change; ?>%</span> 
<?php if ($proj_increase == true) {
	echo 'increase';
}
else {
echo 'decrease';
}
?>)</p>
<!-- Orders yearly comparison -->
<p>Orders Processed For <?php echo $current_year; ?>/<?php echo $last_year; ?>: 
<span class="em-number"><?php echo $total_ord_curr_year; ?></span> / 
<span class="em-number"><?php echo $total_ord_prev_year; ?></span> 
(<span class="<?php echo $ord_style; ?>"><?php echo $ord_percent_change; ?>%</span> 
<?php if ($ord_increase == true) {
	echo 'increase';
}
else {
echo 'decrease';
}
?>)</p>

<!-- Orders yearly comparison -->
<p>Total Spending For <?php echo $current_year; ?>/<?php echo $last_year; ?>: 
<span class="em-number">$<?php echo $total_spent_curr_year; ?></span> / 
<span class="em-number">$<?php echo $total_spent_prev_year; ?></span> 
(<span class="<?php echo $spent_style; ?>"><?php echo $spent_percent_change; ?>%</span> 
<?php if ($spent_increase == true) {
	echo 'increase';
}
else {
echo 'decrease';
}
?>)</p>

<hr/>

<h2 style="text-align:center;">Top Performing Products</h2>
<hr/>
<?php
// ------------------------------------
// Process top performing products
$sql = 'SELECT PrintShopCart.cartId, PrintShopCart.cartIsFinalized,
PrintShopCartItem.cartId, PrintShopCartItem.printProjId, PrintShopCartItem.qty, 
PrintShopCartItem.price, PrintProject.printProjId, PrintProject.printProdId, 
PrintProduct.printProdId, PrintProduct.printProdName,
SUM(PrintShopCartItem.price) AS totalPrice
FROM PrintShopCart, PrintShopCartItem, PrintProject, PrintProduct
WHERE PrintShopCart.cartIsFinalized = 1 AND PrintShopCart.cartId = PrintShopCartItem.cartId
AND PrintShopCartItem.printProjId = PrintProject.printProjId AND PrintProject.printProdId =
PrintProduct.printProdId
GROUP BY PrintProduct.printProdName
ORDER BY totalPrice DESC
LIMIT 5
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$product_name = $row['printProdName'];
	$product_total_price = $row['totalPrice'];
	
		echo '<p>'.$product_name.': Total Spent: <span class="em-number">$'.$product_total_price.'</span></p>';
}
//-----------------------------
//-----------------------------

?>
</div>
</div>

<script>

</script>
</body>
</html>
