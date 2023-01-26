<?php


/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * payments.class.php
 *
 * Payments class and functions
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

/* =============================================================================================================================*/
/* =============================================================================================================================*/





class Payments {

	public function Stripe($CardNumber, $ExpiryMonth, $ExpiryYear, $Cvv, $Amount, $Currency = 'USD' ,$IsCallback = null){

		$gateway = Omnipay\Omnipay::create('Stripe');
		$gateway->setApiKey( STRIPE_SECRET_KEY );

		$formData = array(
			'number' => $CardNumber,
			'expiryMonth' => $ExpiryMonth,
			'expiryYear' => $ExpiryYear,
			'cvv' => $Cvv
		);
		$response = $gateway->purchase(array('amount' => $Amount, 'currency' => $Currency, 'card' => $formData))->send();

		if ($response->isRedirect()) {
		    // redirect to offsite payment gateway
		    $response->redirect();
		} elseif ($response->isSuccessful()) {
		    // payment was successful: update database
		    //print_r($response);
		    $this->Redirect("/browse");
		} else {
		    // payment failed: display message to customer
		    echo $response->getMessage();
		}


	}

	public function Paypal($IsCallback = null){

	}

	public function Eddahabia($IsCallback = null){
		$chargily = new Chargily\ePay\Chargily([
			//credentials
			'api_key' => CHARGILY_PAY_API_KEY,
			'api_secret' => CHARGILY_PAY_SECRET_KEY,
			//urls
			'urls' => [
				'back_url' => "http://localhost/oxygenframework/payment/algpay/success", // this is where client redirected after payment processing
				'webhook_url' => "http://localhost/oxygenframework/payment/algpay/add", // this is where you receive payment informations
			],
			//mode
			'mode' => 'EDAHABIA', //OR CIB
			//payment details
			'payment' => [
				'number' => '465468796897', // Payment or order number
				'client_name' => 'client name', // Client name
				'client_email' => 'client_email@mail.com', // This is where client receive payment receipt after confirmation
				'amount' => 75, //this the amount must be greater than or equal 75 
				'discount' => 0, //this is discount percentage between 0 and 99
				'description' => 'payment-description', // this is the payment description
		
			]
		]);
		// get redirect url
		$redirectUrl = $chargily->getRedirectUrl();
		//like : https://epay.chargily.com.dz/checkout/random_token_here
		//
		if($redirectUrl){
			//redirect
			header('Location: '.$redirectUrl);
		}else{
			echo "We cant redirect to your payment now";
		}


		if ($IsCallback == true) {
			if ($chargily->checkResponse()) {
				$response = $chargily->getResponseDetails();
				//@ToDo: Validate order status by $response['invoice']['invoice_number']. If it is not already processed, process it
				//@ToDo: Check payment status if "paid" confirm order if "failed" or "canceled" Cancel order
			}

		}else{}


	}


	public function CIB($IsCallback = null){

	}



    private function UserID(){
        global $database;

        if (isset( $_SESSION['FirstLogin'] ) ) {
        	$LoginSession	=	$_SESSION['FirstLogin'];
			$UserID 		= 	$database->fetchField('SELECT id FROM users WHERE session_firstlogin = ?', $LoginSession);
        }
        return $UserID;
    }


    private function CreateSession($key, $value){
        return $_SESSION[$key]     =   $value;
    }



    private function Redirect($to){
        header("Location: $to");
        exit();
    }





}



?> 