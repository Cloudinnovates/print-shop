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



require_once('./Stripe/init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey("sk_test_fYKCmuYE7J97JC7UwjD83e6J");

// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
echo "Dog".$token;
echo "Dog2".$_POST['name'];
var_dump($_POST);
$creditAmount = number_format($_POST['credit-card-amount'],2)*100;
// Create the charge on Stripe's servers - this will charge the user's card
try {
$charge = \Stripe\Charge::create(array(
  "amount" => $creditAmount, // amount in cents, again
  "currency" => "usd",
  "source" => $token,
  "description" => "Example charge")
);
} catch(\Stripe\Error\Card $e) {
  // The card has been declined
  echo "Your card has been declined. Please click below to return to the billing entry page.";
  echo "<button onclick='window.location.href = 'billing.php';'></button>";
}	
}
else {
	echo "This page was accessed incorrectly.";
}



?>

