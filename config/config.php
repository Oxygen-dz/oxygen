<?php
/**
 * config.php
 *
 * configurations
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */


    define("DEFAULT_TEMPLATE", $_ENV['DEFAULT_TEMPLATE']);    // Default Template Folder Name
    define("FORCE_MOBILE_TEMPLATE", $_ENV['FORCE_MOBILE_TEMPLATE']);   // if set TRUE will show the mobile template even the detecting device is not a mobile! 
    define("EMAIL_VERIFICATION", TRUE); // User email verification [[ True : On / False : off ]]
    define("PHONE_VERIFICATION", TRUE); // User phone number verification [[ True : On / False : off ]]



    // TWILIO SMS/VOICE Account
    define("TWILIO_SID", $_ENV['TWILIO_SID']);   // Your Account SID from www.twilio.com/console
    define("TWILIO_TOKEN", $_ENV['TWILIO_TOKEN']);   // Your Auth Token from www.twilio.com/console
    define("TWILIO_PHONE_NUMBER", $_ENV['TWILIO_PHONE_NUMBER']);// Your Phone Number from www.twilio.com/console
    
    // Twilio WhatApp Service
    define("TWILIO_WHATSAPP_NUMBER", $_ENV['TWILIO_WHATSAPP_NUMBER']); // Your WhatsApp Number from www.twilio.com/console




    /* 
      FaceAPP [Face++ Service] Api's
      Create FREE api type if you want to TEST and STANDARD for Production
      API KEY & SECRET KEY USED FOR ALL Face++ Services
    */
    define("FACE_PLUSPLUS_APIKEY", $_ENV['FACE_PLUSPLUS_APIKEY']);      // From https://console.faceplusplus.com/app/apikey/list
    define("FACE_PLUSPLUS_SECRETKEY", $_ENV['FACE_PLUSPLUS_SECRETKEY']);   // From https://console.faceplusplus.com/app/apikey/list

    // Face Compare Parametres
    define("FACE_PLUS_PROB_HEIGH_FROM", $_ENV['FACE_PLUS_PROB_HEIGH_FROM']);
    define("FACE_PLUS_PROB_LOW_FROM", $_ENV['FACE_PLUS_PROB_LOW_FROM']);

?>