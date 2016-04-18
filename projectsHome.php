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
	$userPsBalance = $user_detail['userPsBalance'];
}

if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}



// Check user's active shopping cart
$sqlCheckCart = '
SELECT PrintShopCart.cartId, PrintShopCart.cartIsFinalized, UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);
// Set to the current shopping cart user has created
foreach ($rows as $row) {
	if ($row['cartIsFinalized'] != 1) {
			$_SESSION['cartId'] = $row['cartId'];
	}
	else {
		$_SESSION['cartId'] = 0;
	}
}

// If user does not have shopping cart set, create a cart
if ($_SESSION['cartId'] == null || $_SESSION['cartId'] == 0) {
$sqlCreateCart = '
INSERT INTO PrintShopCart (cartIsFinalized, cartTotalPrice) 
VALUES (0, 0);
';
$conn->exec($sqlCreateCart);
// Get the cart Id that was just created
$cartId = $conn->lastInsertId();
// Creat the cart to user relation
$sqlCreateCart = '
INSERT INTO UserToPrintShopCart (userId, cartId) 
VALUES ('.$userId.', '.$cartId.');
';
$conn->exec($sqlCreateCart);
// Set a cart Id to be used for this session
$_SESSION['cartId'] = $cartId;
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
<img class="userPhoto" src="../../../userPhotos/<?php 
if (!($photoUrl)) {
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
<a href="index.php" class="sideLink"><li class="active-step">Your Projects</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h3><? echo $productName; ?></h3>
<hr/>
<h2>Manage Items or Start an Order</h2>
<hr/>
<p>Click the project name or "Self-Print" button to view the created product. You may also click
"Add to Cart" to create an order. When you are satisfied with your order, click "Check Out" to continue.</p>
<p><strong>Always remember to preview your product before ordering.</strong></p>
</div>
<br/>
<img src="Images/psCardSmall.png" alt="ps-card-logo" />Your Print Shop Credit: <b>$<?php echo $userPsBalance; ?></b>
<br/><br/><br/>
<!-- Customer shopping carts are inserted here -->
<div id="user-cart-title"><img src="Images/cart-icon.png" alt="cart-icon" /> Your Cart</div>
<div id="user-cart-list">
<div id="start-order"></div>
<?php
// Checks for the main cart information
$sqlCheckCart = '
SELECT PrintShopCart.cartId,PrintShopCart.cartTotalPrice,
UserToPrintShopCart.userId, UserToPrintShopCart.cartId 
FROM PrintShopCart, UserToPrintShopCart
WHERE UserToPrintShopCart.userId = '.$userId.' AND 
UserToPrintShopCart.cartId = PrintShopCart.cartId
';
$rows = $conn->query($sqlCheckCart);

foreach ($rows as $row) {
	$cartTotal = $row['cartTotalPrice'];
}

// Get Print projects that are currently in the users cart as well as the quantities and other information 
// about the project.
$sqlGetCartItems = '
SELECT PrintShopCartItem.cartId, PrintShopCartItem.printProjId, PrintShopCartItem.skuId, PrintShopCartItem.qty,
PrintShopCartItem.price,
PrintProject.printProjId, PrintProject.printProdId, PrintProject.printProjName, 
PrintProduct.printProdId, PrintProduct.printProdName, PrintProductSku.skuId, PrintProductSku.skuPrice
FROM PrintShopCartItem, PrintProject, PrintProduct, PrintProductSku
WHERE PrintShopCartItem.cartId = '.$_SESSION['cartId'].' AND PrintShopCartItem.printProjId = 
PrintProject.printProjId AND PrintProject.printProdId = PrintProduct.printProdId AND 
PrintProductSku.skuId = PrintShopCartItem.skuId
';

$rows = $conn->query($sqlGetCartItems);
// Echo through each print project in the cart
foreach ($rows as $row) {
	$qty = $row['qty'];
	$printProjId = $row['printProjId'];
	$printProjName = $row['printProjName'];
	$printProdName = $row['printProdName'];
	$productPrice = $row['price'];
	
	echo '
<div id="cart'.$printProjId.'"class="user-cart">
<div class="user-cart-desc">
<p>'.$printProjName.' - '.$printProdName.'</p>
<strong>Quantity</strong>: '.$qty.'</p>
</div>
<div class="user-cart-options">
<p><strong>Price</strong>: $<span class="price">'.$productPrice.'</span><br/>
<a id="'.$printProjId.'" class="removeButton" href="">(Remove)</a></p>
</div>
</div>
	';

}
?>
<hr/>
<p id="cart-total-line"><strong>Subtotal</strong>: $<span id="subtotal"><?php echo $cartTotal ?></span>
<a href="shipping.php"><img id="checkoutBtn" src="Images/checkoutBtn.jpg" alt="checkout-button" /></a> 
</p>
</div>

<br/><br/>

<!-- List of User Projects go here -->
<div id="project-list-title"><img src="Images/paper-icon.png" alt="project-icon" /> Your Projects</div>
<div id="project-list">
<?php
// Get Print Project Info
$sql = 'SELECT PrintProject.printProjId, PrintProject.printProjName, 
PrintProject.printProjDate, PrintProject.printProjFile, PrintProject.printProjQty, 
PrintProject.printProjIsDeleted, UserToPrintProject.printProjId, UserToPrintProject.userId
FROM PrintProject, UserToPrintProject
WHERE PrintProject.printProjIsDeleted = 0 AND 
PrintProject.printProjId = UserToPrintProject.printProjId AND UserToPrintProject.userId = 
'.$userId.'';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$printProjId = $row['printProjId'];
	$printProdId = $row['printProdId'];
	$printProjName = $row['printProjName'];
	$printProjDate = $row['printProjDate'];
	$printProjFile = $row['printProjFile'];
	$printProjQty = $row['printProjQty'];
	$printProdName = $row['printProdName'];
	
	// Get Print Product Info
	$sqlPrintProduct = 'SELECT PrintProject.printProjId, PrintProject.PrintProdId, 
	PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType
	FROM PrintProject, PrintProduct
	WHERE PrintProject.printProjId = '.$printProjId.' AND PrintProject.printProdId
	= PrintProduct.printProdId';

	$rowsPrintProduct = $conn->query($sqlPrintProduct);
	
	foreach ($rowsPrintProduct as $rowPrintProduct) {
	$printProdId = $rowPrintProduct['printProdId'];
	$printProdName = $rowPrintProduct['printProdName'];
	$printProdType = $rowPrintProduct['printProdType'];
	}
	// The quantity processing below is shit, thanks to our printer changing everything at the last second.
	// The owner is a brain-dead sloth.
	echo '
	<div class="print-project">
	<div class="print-project-desc">
	<p><a class="project-link" target="_blank" href="Output/'.$printProjFile.'">'.$printProjName.'</a></p>
	<p class="project-product">Includes: ('.$printProjQty.') '.$printProdName.'<br/>Created: '.$printProjDate.'</p>
	</div>
	<div class="print-project-options">
	<p>Quantity: 
	';
	// If poster, or duck commander poster. Again, sorry about the newb code quality
	if ($printProdType == 'poster' || $printProdId == 22) {
		echo '
		<select id="qty'.$printProjId.'">
		<option value="2">2</option>
		<option value="4">4</option>
		<option value="8">8</option>
		<option value="16">16</option>
		<option value="20">20</option>
		<option value="24">24</option>
		<option value="28">28</option>
		<option value="32">32</option>
		</select>
		';
	}
	// Same for the flyer. 
	else if ($printProdType == 'flyer' || $printProdId == 23) {
		echo '
		<select id="qty'.$printProjId.'">
		<option value="50">50</option>
		<option value="100">100</option>
		<option value="250">250</option>
		<option value="500">500</option>
		<option value="750">750</option>
		<option value="1000">1000</option>
		<option value="1500">1500</option>
		<option value="2000">2000</option>
		</select>
		';
	}
	else {
		echo '<input type="text" id="qty'.$printProjId.'" size="1" value="1"/> ';
	}
	echo '
	<a id="'.$printProjId.'" class="cartAdd" href="">Add To Cart</a> | 
	<a href="Pages/Edit/editLayout.php?project='.$printProjId.'">Edit</a> | 
	<a onclick="return confirm(\'Are you sure?\')" href="Pages/Edit/editDelete.php?project='.$printProjId.'">Delete</a> |
	<a target="_blank" href="self-print.php?projId='.$printProjId.'">Self Print</a>
	';
	if ($printProdType == "description-card" || $printProdType == "description-card-blank") {
		echo '<br/><input type="checkbox" id="laminate-'.$printProjId.'" name="laminate">Laminate? + $0.10 per card.';
	}
	echo '</p>
	</div>
	</div>
	';
}
?>
</div>


</div>

</div>


<script>

// If they click add to cart button
$(".cartAdd").on("click", function (event) {
	event.preventDefault();
	var projId = $(this).attr("id");
	var qty = $("#qty"+projId).val();
	var lam = "false";
	// Gets the laminate value and appends true or false to the URL called
	if ($("#laminate-"+projId+"").prop("checked") == true) {
		lam = "true";
	} else {
		lam = "false";
	}
		// Check if string is empty
	 if ($("#cart"+projId).length != 0) {
        alert("You have already added this project to your cart. Please remove "+
		"it before adding it again.");
    } 
	// then create xmlHTTP object for AJAX and PHP processing. 
	else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// Remove each product currently listed and append the products returned by category change
            $("#start-order").after(xmlhttp.responseText);
			location.reload();
            }
        }
// Open update product category php script and return values
        xmlhttp.open("GET","cartAdd.php?projId="+projId+"&qty="+qty+"&lam="+lam,true);
        xmlhttp.send();
    }
});

// If they click add to cart button
$(".removeButton").on("click", function (event) {
	event.preventDefault();
	var projId = $(this).attr("id");

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				location.reload();
            }
        }
// Open update product category php script and return values
        xmlhttp.open("GET","cartRemove.php?projId="+projId,true);
        xmlhttp.send();

});
</script>
</body>
</html>
