var originalTotal = parseFloat($("#cart-total").text());
var psBalance = parseFloat($("#psBalance").text());
// These variables are set when the user has correctly entered their information.
var cardCorrect = false;
var cvcCorrect = false;
var expirDateCorrect = false;
// Hide the "info entered correctly span element" Show later when information is all entered correctly.
$("#infoCorrect").hide();

// Set the credit card amount to the amount due.
$("#credit-card-amount").val(originalTotal);

// If PS Balance is 0, disable the form fields for value entry.
if (psBalance == 0.00) {
	$("#printShopCost").prop("disabled", true);
	$("#credit-card-amount").val(originalTotal);
}
// Code for processing the current balance.
// Clear the print shop cost field when focused on.
$("#printShopCost").on("focus", function() {
	$("#printShopCost").val("");
});
$("#printShopCost").on("blur", function() {
	if (!$(this).val()) {
		$("#printShopCost").val("0.00");
		$("#cart-total").text(originalTotal);
		$("#credit-card-amount").val(originalTotal);
	}
});
// Change order total as you type
$("#printShopCost").on("input", function() {
	if ($("#printShopCost").val() > psBalance) {
		$("#printShopCost").val(psBalance);
	}
	if ($("#printShopCost").val() > originalTotal) {
		$("#printShopCost").val(originalTotal);
	}
		var currentTotal = originalTotal;
		var printShopAmount = parseFloat($("#printShopCost").val());
		var newTotal = currentTotal - printShopAmount;
		newTotal = newTotal.toFixed(2);
		$("#credit-card-amount").val(newTotal);
});

// Validate Cardholder Name Entered
$("#cardName").on("blur", function(){
	var cardName = $("#cardName").val();
	if (cardName.length > 3) {
	$("#name-status").attr("src", "Images/green-check.png");
	}
	else {
	$("#name-status").attr("src", "Images/red-x.png");	
	}
});

// Validate Card Number Entered
$("#cardNo").on("blur", function(){
	var cardNumber = $("#cardNo").val();
	if (Stripe.card.validateCardNumber(cardNumber)) {
	$("#card-status").attr("src", "Images/green-check.png");
	cardCorrect = true;
	formCheck();
	}
	else {
	$("#card-status").attr("src", "Images/red-x.png");	
	cardCorrect = false;
	}
});


// Validate CVC Number Entered
$("#cvcNo").on("blur", function(){
	var cvcNumber = $("#cvcNo").val();
	if (Stripe.card.validateCVC(cvcNumber)) {
	$("#cvc-status").attr("src", "Images/green-check.png");
	cvcCorrect = true;
	formCheck();
	}
	else {
	$("#cvc-status").attr("src", "Images/red-x.png");
	cvcCorrect = false;	
	}
});

// Validate CVC Number Entered
$("#expirYear").on("blur", function(){
	var expirMonth = $("#expirMonth").val();
	var expirYear = $("#expirYear").val();
	if (Stripe.card.validateExpiry(expirMonth, expirYear)) {
	$("#expir-status").attr("src", "Images/green-check.png");
	expirDateCorrect = true;
	formCheck();
	}
	else {
	$("#expir-status").attr("src", "Images/red-x.png");
	expirDateCorrect = false;	
	}
});

// Function to check if form is filled out correctly
function formCheck() {
	if (cardCorrect && cvcCorrect && expirDateCorrect) {
		$("#submit").prop("disabled", false);	
		$("#infoCorrect").show();
	}
	else {
		$("#submit").prop("disabled", true);	
		$("#infoCorrect").hide();
	}
}

 var stripeResponseHandler = function(status, response) {
      var $form = $('#payment-form');
      if (response.error) {
        // Show the errors on the form
        $form.find('.payment-errors').text(response.error.message);
        $form.find('button').prop('disabled', false);
      } else {
        // token contains id, last4, and card type
        var token = response.id;
        // Insert the token into the form so it gets submitted to the server
        $form.append($('<input id="strTok" type="text" name="stripeToken" />').val(token));
		alert($("#strTok").val());
        // and re-submit
        $form.get(0).submit();
      }
    };
	
$(document).ready(function() {
                $("#payment-form").submit(function(event) {
                    // disable the submit button to prevent repeated clicks
                    $('#submit').attr("disabled", "disabled");
                    // createToken returns immediately - the supplied callback submits the form if there are no errors
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);

                });
            });
 