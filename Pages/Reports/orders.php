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
<a href="orders.php" class="sideLink"><li class="active-step">Orders</li></a>
<a href="product-performance.php" class="sideLink"><li>Product Performance</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h2>Order Information</h2>
<hr/>
</div>
<form name="search-tools" action="orders.php" method="POST">
<label for="date1">Date Between:</label>
<input type="text" name="date1" /> and <input type="text" name="date2" /><br/>
<p>Use format (xx/xx/xxxx)</p>
<label for="first-name">First Name: </label><input type="text" name="first-name" />
<label for="last-name">Last Name: </label><input type="text" name="last-name" /><br/><br/>
<label for="order-num">Order #: </label>
<input type="text" name="order-num" size="25" /><br/><br/>
<label for="group-by">Group By: </label>
<select name="group-by">
<option value="">None</option>
<option value="Customer">Customer</option>
<option value="Admin">Admin</option>
<option value="Showroom">Showroom</option>
</select><br/><br/>

<input type="submit" value="Submit" name="submit" />
</form>
<hr/>

<table cellpadding="5">
<tr><td>Conf #</td><td>First Name</td><td>Last Name</td><td>Date Placed</td><td>View</td></tr>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];
	$first_name = $_POST['first-name'];
	$last_name = $_POST['last-name'];
	$order_num = $_POST['order-num'];
	$group_by = $_POST['group-by'];
	
	$sql = "
	SELECT User.userUsername, User.userFirstName, User.userLastName,
	User.userType, UserToPrintShopCart.userId, UserToPrintShopCart.cartId, 
PrintShopCart.cartId, PrintShopCart.cartConfNo, PrintShopCart.cartIsFinalized,
PrintShopCart.cartDateFinalized
FROM User, UserToPrintShopCart, PrintShopCart
WHERE User.userId = UserToPrintShopCart.userId AND UserToPrintShopCart.cartId = PrintShopCart.cartId 
AND PrintShopCart.cartIsFinalized = 1
	";
	if (!empty($date1) && !empty($date2)) {
		$sql .= " AND PrintShopCart.cartDateFinalized BETWEEN '".$date1."' AND '".$date2."'";
	}
	elseif (!empty($date1)) {
		$sql .= " AND PrintShopCart.cartDateFinalized = '".$date1."'";
	}
	elseif (!empty($date2)) {
		$sql .= " AND PrintShopCart.cartDateFinalized = '".$date2."'";
	}
	if (!empty($order_num)) {
		$sql.= " AND PrintShopCart.cartConfNo = ".$order_num."";
	}
	if (!empty($first_name) && !empty($last_name)) {
		$sql .= " AND User.userFirstName = '".$first_name."' AND User.userLastName = '".$last_name."'";
	}
	elseif (!empty($first_name)) {
		$sql .= " AND User.userFirstName = '".$first_name."'";
	}
	elseif (!empty($last_name)) {
		$sql .= " AND User.userLastName = '".$last_name."'";
	}
	if (!empty($group_by)) {
		$sql .= " AND User.userType = '".$group_by."'";
	}
	$rows = $conn->query($sql);
	
	foreach ($rows as $row) {
	echo '<tr><td>'.$row['cartConfNo'].'</td><td>'.$row['userFirstName'].'</td>
	<td>'.$row['userLastName'].'</td><td>'.$row['cartDateFinalized'].'</td>
	<td><a href="../../viewOrderAdmin.php?orderNo='.$row['cartId'].'">View Order</a></td></tr>';
}

}

?>
</table>
</div>
</div>

<script>

</script>
</body>
</html>
