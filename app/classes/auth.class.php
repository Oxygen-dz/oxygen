<?php


/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * auth.class.php
 *
 * Auth class and functions
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

class Users
{


    public function Signup($name, $email, $password){
        global $database;
        $checkUser  =   $this->SignupCheckUser($email);
        if ( $checkUser ) {
            $response = $this->Error(1);
            $this->Redirect("?error=$response");
        }else{
            $FirstLoginSession   =   OX_RandomChars();
            $InsertedID     =   $database->query('INSERT INTO users', [ // here can be omitted question mark
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'session_firstlogin' => $FirstLoginSession,
                'session_welcome_step' => 0,
                'signup_type' => "normal"
            ]);
            $this->CreateSession("FirstLogin", $FirstLoginSession);
            $this->Redirect($_ENV['APP_URL'] . "/welcome");
            $response = $InsertedID;

        }

        return $response;
    }


    public function Login($email, $password){
        global $database;

        // check the signup type if normal or OAuth
        $SignupType     =   $this->Login_SignupType($email);
        if ($SignupType != "normal") {
            $response = $this->Error(2, "Log In with " . $SignupType);
            $this->Redirect("?error=$response");
        }else{ }
        //  Check Account exsits
        $CheckAccount   =   $this->Login_CheckAccount($email, $password);
        if ($CheckAccount == false) {
            $response = $this->Error(3);
            $this->Redirect("?error=$response");
        }else{

            // Check if account setup is compeleted by checking First login session token in database
            $FirstLogin     =   $database->fetchField("SELECT session_firstlogin FROM users WHERE email = ?", $email);
            $UserID         =   $database->fetchField("SELECT id FROM users WHERE email = ?", $email);
                if ( ! empty( $FirstLogin ) ) {
                    // Start the same session and continue the steps
                    $this->CreateSession("FirstLogin", $FirstLogin);
                    // This will Redirect user to the right step to continue and complete the signup process
                    $this->Login_ContinueWelcomeStep($email);
                }else {
                    $this->CreateSession("Login", $UserID);
                    $this->Redirect($_ENV['APP_URL'] . "/browse");            
                }




        }

    }





    protected function Is_NormalSignup($email){
        global $database;
        $Signup_type    =   $database->fetchField('SELECT signup_type FROM users WHERE email = ?',$email);

        if ( empty( $Signup_type ) ) {
            $response = true;
        }else{
            $response = false;
        }

        return $response;

    }


    // Check the user exsitence in the db by email / username / phone number
    private function SignupCheckUser($email){
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



    // Check the user exsitence in the db by email / username / phone number
    private function Login_CheckAccount($email, $password){
        global $database;
        $result         =   $database->query('SELECT * FROM users WHERE email = ? AND password = ?',$email, $password);
        $CheckExsits    =   $result->getRowCount(); // returns the number of rows if is known

        if ($CheckExsits != 0) {
            $response = true;
        }else{
            $response = false;
        }

        return $response;
    }





    // Check the signup type if used OAuth method or normal type
    private function Login_SignupType($email){
        global $database;
        $SignupType     =   $database->fetchField("SELECT signup_type FROM users WHERE email = ?", $email);
        if ( empty( $SignupType ) OR $SignupType == "normal") {
            $response   =   "normal";
        }else{
            $response   =   $SignupType;
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



    private function Error($code, $plusmessage = null){
        $ErrorCodes  =  array();
        $ErrorCodes = [
            1   =>  "User is already exsit in our records please try to login",
            2   =>  "Your should use another login method ! " . $plusmessage,
            3   =>  "Unavailable Account",
        ];
        return $ErrorCodes[ $code ];
    }




    private function SendEmail_Verification($To, $From = "center@oxygen-framework.org", $HtmlBody = "Test mail"){

    }

}

 
?>