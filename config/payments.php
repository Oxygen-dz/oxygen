<?php
/**
 * Payments.php
 *
 * Payment Getways Manual Configuration
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */


	$PaymentGetways = [

		"Modes" => [
			"STRIPE" 	=> 	false,
			"PAYSERA" 	=> 	false,
			"MOLLIE" 	=> 	false
		]

	];


	// DEFAULT PAYMENT MODE
	define("DEFAULT_OMNI_PAYMENT_GETWAY", "STRIPE");

	// DEFAULT PAYMENT MODE From $PaymentGetways[]
	define("AVAILABLE_PAYMENT_GETWAYS", $PaymentGetways["Modes"] );

	// Stripe Config
	define("STRIPE_SECRET_KEY","sk_test_51H02nDJEuRdzmlq2OP0oa0I3RiA1ID8Uu8jt3pmU4kgfYyfGQcS3ZlpVSh41IZsmzEbcWnj1S5ONpPu8CGNtPXvY006I3aePBv");

	// AuthorizeNet Config
	define("AUTHORIZE_AUTH_NAME", null);
	define("AUTHORIZE_TRANSACTION_KEY", null);
	define("AUTHORIZE_TEST_MODE", TRUE);

	// Paysera Config
	define("PAYSERA_PROJECT_ID" ,'188069'); // From https://bank.paysera.com/en/activities
	define("PAYSERA_PROJECT_PASSWORD" ,'037d0299e7730856496a2cdbdb768e14'); // From https://bank.paysera.com/en/activities

	// Mollie Config
	define("MOLLIE_API_KEY", 'test_EESmaebNAzjF8p3kwQpauzHpt7SRxs'); // From https://www.mollie.com/dashboard/developers/api-keys



    // Algerian Chargely Payment Getway APIs
	define("CHARGILY_PAY_API_KEY", 'api_xTju7dwdTcK1yN9t841yOd59bI3SBLPcccyPfIVyFUqKCY5brspoigl2nMucQNy7'); // Chargily api key
	define("CHARGILY_PAY_SECRET_KEY", 'secret_96bba5924790d7a6b0cf1c2410ab5882a637960ca720bd685222c4c2ef6b8650'); // Chargily secret key

    // Algerian Ubexpay Payment Getway APIs
    define("UBEXPAY_API_KEY", 'api_xTju7dwdTcK1yN9t841yOd59bI3SBLPcccyPfIVyFUqKCY5brspoigl2nMucQNy7'); // Chargily api key
    define("UBEXPAY_SECRET_KEY", 'secret_96bba5924790d7a6b0cf1c2410ab5882a637960ca720bd685222c4c2ef6b8650'); // Chargily secret key

?>