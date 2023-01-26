<?php


/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * oauth.class.php
 *
 * OAuth (Facebook,Google,Github,Twitter,ect..) class  and functions
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


/**
 * Oauth class 
 */


class OAuth{	

	private function ServiceProvider($ProviderName){

		switch ($ProviderName) {

			case 'Google':
				$provider = new League\OAuth2\Client\Provider\Google([
				    'clientId'     => $_ENV['GOOGLE_CLIENT_ID'],
				    'clientSecret' => $_ENV['GOOGLE_SECRET'],
				    'redirectUri'  => $_ENV['APP_URL'] . '/auth/google/callback'				    //'hostedDomain' => 'localhost', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
				]);
				break;


			case 'Facebook':
		        $provider = new League\OAuth2\Client\Provider\Facebook([
				    'clientId'          => $_ENV['FACEBOOK_CLIENT_ID'],
				    'clientSecret'      => $_ENV['FACEBOOK_SECRET'],
				    'redirectUri'       => $_ENV['APP_URL'] . '/auth/facebook/callback',
				    'graphApiVersion'   => 'v2.10'
		        ]);
				break;


			case 'Instagram':
				$provider = new League\OAuth2\Client\Provider\Instagram([
				    'clientId'          => $_ENV['INSTAGRAM_CLIENT_ID'],
				    'clientSecret'      => $_ENV['INSTAGRAM_SECRET'],
				    'redirectUri'       => $_ENV['APP_URL'] . '/auth/instagram/callback',
				    'host'              => 'https://api.instagram.com',  // Optional, defaults to https://api.instagram.com
				    'graphHost'         => 'https://graph.instagram.com' // Optional, defaults to https://graph.instagram.com
				]);
				break;

			case 'Github':
		        $provider = new League\OAuth2\Client\Provider\Github([
		            'clientId'          => $_ENV['GITHUB_CLIENT_ID'],
		            'clientSecret'      => $_ENV['GITHUB_SECRET'],
		            'redirectUri'       => $_ENV['APP_URL'] . '/auth/github/callback'
		        ]);
				break;

			default:
				// code...
				break;
		}


        return $provider;

	}




	//  Google OAuth Function -- Provider is in ServiceProvider function
	public function Google($IsCallback = false){

    	$provider = $this->ServiceProvider("Google");

        if (! isset ( $_GET['code'] ) ) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        }elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }else{
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
    	}

    	if ($IsCallback == true) {
	            // We got an access token, let's now get the user's details
	        try {

	        	// Optional: Now you have a token you can look up a users profile data
	            $user = $provider->getResourceOwner($token);

	            // Use these details to create a new profile
    			$UserInfo 	=	[
    				'firstname' 	=> $user->getFirstName(),
    				'lastname' 		=> $user->getLastName(),
    				'email' 		=> $user->getEmail()
    			];

				//  First Check if user already exsit By ::EMAIL:: in the db
				$CheckUsereExist = $this->CheckUserExist( $UserInfo['email'] );

				if ($CheckUsereExist) {
					// if user is alraedy exsit then login the user
					$this->Login( $UserInfo['email'] , "Google" );
				}else{
					$Fullname 	=	$UserInfo['firstname'] . ' ' . $UserInfo['lastname'];
					// if user not exist in db then registre it with the email and name
					$this->Signup($Fullname, $UserInfo['email'], "Google");
				}

	        } catch (Exception $e) {

	            // Failed to get user details
	            exit('Oh dear...');
	        }

    	}

	}





	//  Facebook OAuth Function -- Provider is in ServiceProvider function
	public function Facebook($IsCallback = false){

    	$provider = $this->ServiceProvider("Facebook");

        if (! isset ( $_GET['code'] ) ) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        }elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }else{
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
    	}

    	if ($IsCallback == true) {
	            // We got an access token, let's now get the user's details
	        try {
	        	// Optional: Now you have a token you can look up a users profile data
	            $user = $provider->getResourceOwner($token);
    			$UserInfo 	=	[
    				'firstname' 	=> $user->getFirstName(),
    				'lastname' 	=> $user->getLastName()
    			];
    			echo $UserInfo['firstname'] . ' ' . $UserInfo['lastname']; 


	            // Use these details to create a new profile

	        } catch (Exception $e) {

	            // Failed to get user details
	            exit('Oh dear...');
	        }

    	}

	}





	//  Instagram OAuth Function -- Provider is in ServiceProvider function
	public function Instagram($IsCallback = false){

    	$provider = $this->ServiceProvider("Instagram");

        if (! isset ( $_GET['code'] ) ) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        }elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }else{
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
    	}

    	if ($IsCallback == true) {
	            // We got an access token, let's now get the user's details
	        try {

	        	// Optional: Now you have a token you can look up a users profile data
	            $user = $provider->getResourceOwner($token);
    			$UserInfo 	=	[
    				'Nickname' 	=> $user->getNickname()
    			];
    			echo $UserInfo['Nickname']; 


	            // Use these details to create a new profile

	        } catch (Exception $e) {

	            // Failed to get user details
	            exit('Oh dear...');
	        }

    	}

	}





	//  Github OAuth Function -- Provider is in ServiceProvider function
	public function Github($IsCallback = false){

    	$provider = $this->ServiceProvider("Github");

        if (! isset ( $_GET['code'] ) ) {
            // If we don't have an authorization code then get one
			$options = [
			    'scope' => ['user','user:email'] // array or string
			];

            $authUrl = $provider->getAuthorizationUrl($options);
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        }elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }else{
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
    	}

    	if ($IsCallback == true) {
	            // We got an access token, let's now get the user's details
	        try {

	        	// Optional: Now you have a token you can look up a users profile data
	            // Use these details to create a new profile
	            $user = $provider->getResourceOwner($token);
    			$UserInfo 	=	[
    				'nickname' 	=> $user->getNickname(),
    			];
				//  First Check if user already exsit By ::EMAIL:: in the db
				echo $UserInfo['nickname'];
				// $CheckUsereExist = $this->CheckUserExist($UserInfo['email']);

				// if ($CheckUsereExist) {
				// 	echo "User is already exsit in our records";
				// 	// if user is alraedy exsit then login the user
				// 	$this->Login( $UserInfo['email'] , "Github" );
				// }else{
				// 	echo $UserInfo['firstname'] . "User Succesfully registred !";
				// }

	        } catch (Exception $e) {

	            // Failed to get user details
	            exit('Oh dear...');
	        }

    	}

	}



    // Signup user via OAtuth
    private function Signup($name, $email, $signup_type){
        global $database;
            $FirstLoginSession		=   OX_RandomChars();
            $InsertedID				=   $database->query('INSERT INTO users', [ // here can be omitted question mark
                'name' => $name,
                'email' => $email,
                'password' => random_int(987654321,999999999999999).'RND-'. OX_RandomChars(),
                'session_firstlogin' => $FirstLoginSession,
                'session_welcome_step' => 0,
                'signup_type' => $signup_type,
                'is_verified' => 1
            ]);
            $this->CreateSession("FirstLogin", $FirstLoginSession);
            $this->Redirect($_ENV['APP_URL'] . "/welcome");

    }





    // Login user via OAuth
    private function Login($email, $signup_type){
        global $database;
        // Get Signup type
        $SignupType     =   $database->fetchField("SELECT signup_type FROM users WHERE email = ?", $email);
        // Check signup type
        if ( !empty( $SignupType ) AND $SignupType == $signup_type) {
        	
	        // Check if account setup is compeleted by checking First login session token in database
        	$FirstLogin     =   $database->fetchField("SELECT session_firstlogin FROM users WHERE email = ?", $email);
	        $UserID 		=   $database->fetchField("SELECT id FROM users WHERE email = ?", $email);
	            if ( ! empty( $FirstLogin ) ) {
	                // Start the same session and continue the steps
	                $this->CreateSession("FirstLogin", $FirstLogin);
	                // This will Redirect user to the right step to continue and complete the signup process
	                $this->Login_ContinueWelcomeStep($email);
	            }else {
	                $this->CreateSession("Login", $UserID);
	                $this->Redirect($_ENV['APP_URL'] . "/browse");            
	            }

        }else{

        	echo " -- Please use  " . $SignupType . " to login";

        }



    }



    private function CheckUserExist($email){
        global $database;
        $result         =   $database->query('SELECT * FROM users WHERE email = ?',$email);
        $CheckExsits    =   $result->getRowCount(); // returns the number of rows if is known

        if ($CheckExsits != 0) {
            $response = true;
        }else{
            $response = false;
        }

        return $response;
    }



    private function Login_ContinueWelcomeStep($email){
        global $database;        
        $Agreement      =   $database->fetchField("SELECT is_agree FROM users WHERE email = ?", $email);
        $CurrentStep    =   $database->fetchField("SELECT session_welcome_step FROM users WHERE email = ?", $email);
        // Check the current step & agreement 
        if ($CurrentStep != 0 AND $Agreement == 1)  {
            // Redirect the user to the right step to continue the signup process
            $step   =   $CurrentStep;
            $this->CreateSession("UserAgree", $Agreement);
            $this->Redirect($_ENV['APP_URL'] . "/welcome/step/".$step);
        }else{
            $step   =   NULL;
            $this->Redirect($_ENV['APP_URL'] . "/welcome");
        }

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