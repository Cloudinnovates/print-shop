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

$selectedProducts = array();
$_SESSION['selected_products'] = $selectedProducts;

$_SESSION['style'] = $_GET['style'];
$_SESSION['projectName'] = $_POST['project-name'];

$product = $_SESSION['product'];
$style = $_SESSION['style'];
$projectName = $_SESSION['projectName'];

if (!isset($_SESSION['username'])) {
	echo "<script>window.location = ('loginPage.php');</script>";
}
// This redirects the user if they creating a project that does not need products
$sql = 'SELECT PrintProduct.printProdId, PrintProduct.printProdName, PrintProduct.printProdType, PrintProduct.printProdMaxProds
FROM PrintProduct
WHERE PrintProduct.printProdId = '.$product.'
';

$rows = $conn->query($sql);

foreach ($rows as $row) {
	$productId = $row['printProdId'];
	$productName = $row['printProdName'];
	$productType = $row['printProdType'];
	$productMaxProds = $row['printProdMaxProds'];
}

// Redirect to layout, as some customization is still needed
if ($productType == "editable-banner") {
	header('Location: layout.php');
} elseif($productMaxProds == 0) {
	header('Location: completion.php');	
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
<a href="index.php" class="sideLink"><li>Step 1: Create</li></a>
<a href="options.php?product=<?php echo $product ?>" class="sideLink"><li>Step 2: Options</li></a>
<a href="products.php" class="sideLink"><li class="active-step">Step 3: Products</li></a>
</ul>
</div>

<div id="rightContent">
<div id="page-description">
<h3><?echo $productName; ?></h3>
<hr/>
<h2>Select Products (Up To <span class="maxProds"><?php echo $productMaxProds ?>)</span></h2>
<hr/>
<p>To select a product, click the checkbox next to its image. To select from other categories, use the drop down menu below. Once you've finished making your selections, click CONTINUE.
<?php 
if ($productType == "description-card") {
	echo '<p style="color:red;">Note* It is cheaper to have all of your description cards printed in
	one project, rather than creating multiple projects for all of your cards.</p>';
}
?>
</p>
</div>
<a id="continueBtn"><img src="Images/arrow.png" alt="arrow-icon" /> Continue</a>
<br/><br/>
<div class="data-tool">
<p>Type To Find</p>
<input type="text" id="product-finder" size="35"/>
</div>

<div class="data-tool">
<p>Or, Select Category</p>
<select id="category" name="category">
<option value="Choose">Choose Category...</option>
<option value="All">Show All Products</option>
<option value="500Gram">500 Gram</option>
<option value="MultiEffect">Multi-Effect</option>
<option value="ReloadablesAndTubes">Reloadables and Tubes</option>
<option value="RomanCandles">Roman Candles</option>
<option value="Parachutes">Parachutes</option>
<option value="Missiles">Missiles</option>
<option value="Assortments">Assortments</option>
<option value="Fountains">Fountains</option>
<option value="Firecrackers">Firecrackers</option>
<option value="Rockets">Rockets</option>
<option value="NoveltiesAndSparklers">Novelties and Sparklers</option>
<option value="Spinners">Spinners</option>
</select>
</div>

<div class="data-tool">
<p>Sort By</p>
<select id="sort" name="sort">
<option value="prodName">Alphabetical</option>
<option value="prodFoaNo">FOA #</option>
</select>
</div>

<div id="product-list">
<a class="selectBtn" id="select-all">Select All</a> | <a class="selectBtn" id="select-none">Select None</a>

<div id="selected-products">
<p>Selected Products</p><hr/>
<p class="selected-product"></p>
</div>
</div>

</div>
</div>

<script>
// Define variables used across each function
var sortBy = $("#sort option:selected").val();
var totalSelects = 0;
var maxProds = parseInt($(".maxProds").text());

// when sort changes, set the variable to the selected value. Calls update method
$("#sort").change(function() {
	sortBy = $("#sort option:selected").val();
	$("#category").change();
});

// When product check is clicked, compare total selected to print product limit
$("#product-list").on("change", "input[type='checkbox']", (function() {
	var product = $(this).val();
	var productId = $(this).attr("id");

	if (this.checked) {
			if (totalSelects <= maxProds) {
			$(".selected-product").append("<p class='product-selected' id='"+productId+"'>"+product+"</p>");
			totalSelects += 1;
		}
		if (totalSelects > maxProds) {
			$(this).prop("checked", false);
			alert("You have reached the maximum product count for this item. Please deselect a product and try again.");
		}
	}
	if (!(this.checked)) {
		totalSelects -= 1;
		$("p[id="+productId+"]").remove();
	}
}));

// Process validation before user can continue 
$("#continueBtn").on("click", function(){
	var products = [];
	
	$(".product-selected").each(function() {
		var product = $(this).attr("id");
		products.push(product);
	});
		if (totalSelects != 0) {
	window.location.href = "layout.php?products="+products;
	}
	else {
		alert("You must select a product");
	}

	
});
	
// When category is changed, return products matching category
$("#category").change(function update() {
	var category = $("#category option:selected").val();
	
		// Check if string is empty
	 if (category == "") {
        return;
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
				$(".product").remove();
                $("#product-list").append(xmlhttp.responseText);
				selectedCheck();
            }
        }
// Open update product category php script and return values
        xmlhttp.open("GET","updateProdCat.php?cat="+category+"&sort="+sortBy,true);
        xmlhttp.send();
    }
});

// "Type to find" feature uses this to return products as each letter is input by the user
$("#product-finder").on("input", function() {
	var string = $("#product-finder").val();
	
		// Check if string is empty
	 if (string == "") {
        return;
    } 
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
// Clear the product list and append each item returned
				$(".product").remove();
                $("#product-list").append(xmlhttp.responseText);
				selectedCheck();
            }
        }
// Open update type find file and return products
        xmlhttp.open("GET","updateTypeFind.php?s="+string,true);
        xmlhttp.send();
    }
});

// Checks which products are currently selected and keeps their checkboxes checked between pages
function selectedCheck() {
	var products = [];
	
	$(".product-selected").each(function() {
		var product = $(this).attr("id");
		products.push(product);
	});
	for (i=0; i < products.length; i++) {
		$("#product-list #"+products[i]+"").prop("checked", "checked");
	}
}

// Selects all products
$("#select-all").on("click", function() {
	$(".product-check").prop("checked", true).change();
	
});
// Deselects all products
$("#select-none").on("click", function() {
	$(".product-check").prop("checked", false).change();
});

</script>
</body>
</html>
