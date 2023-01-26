<?php
/**
 * utilities.php
 *
 * Some dependencies will be called from here
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */


// Call Guzzle http [Client] library
use GuzzleHttp\Client;
$httpClient = new Client();



// Call Device Detactor Class !
use Jenssegers\Agent\Agent;
$agent  =   new Agent();

/*-----------------------------------------------------------------*/

// This load .ENV file in the system
$dotenv = Dotenv\Dotenv::createImmutable( dirname(__DIR__) );
$dotenv->load();

/*-----------------------------------------------------------------*/

// Creating random chars and string [Check RandomGenerator function in functions.php file]
$random = new PragmaRX\Random\Random();


/*-----------------------------------------------------------------*/

