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

<body style="text-align:left; padding:10px;">

<?php
$id = $_GET['id'];

$sql = 'SELECT * FROM PrintProduct, PrintProductToPrintProductPrice, PrintProductPrice 
WHERE PrintProduct.printProdId = '.$id.' AND PrintProductToPrintProductPrice.printProdId = '.$id.' AND
PrintProductToPrintProductPrice.priceId = PrintProductPrice.priceId';
$rows = $conn->query($sql);

// Get the name of the selected product
foreach ($rows as $row) {
	$productName = $row['printProdName'];
	$productType = $row['printProdType'];
}
// Echo the page title
echo '<h1>'.$productName.'</h1>';

// Get all print products and show product info for the listing
$rows = $conn->query($sql);
foreach ($rows as $row) {
echo '
<p class="product-pricing">'.$row['priceName'].' - <b>$'.$row['priceThePrice'].'</b></p>
';
}
if ($productType == "description-card") {
	echo '
	<p>Optional fees:<br/>
Lamination additional fees per piece:<br/>
5000-10000		$0.08/each<br/>
2500-4999		$0.08/each<br/>
1000-2499		$0.08/each<br/>
500-999		$0.08/each<br/>
250-499		$0.08/each<br/>
100-249		$0.09/each<br/>
50-99		$0.12/each<br/>
26-49		$0.13/each<br/>
1-25		$0.16/each<br/>
</p>
	';
}
?>

</body>
</html>