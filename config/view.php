<?php
/**
 * view.php
 *
 * Views Path
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */


/*

=======================================================================
    Oxygen use Twig to load templates                                 *
    Developer Doc : https://twig.symfony.com/doc/2.x/api.html         *  Author : Redwan Aouni, Algeria
    Front-end Doc : https://twig.symfony.com/doc/2.x/templates.html   *
=======================================================================
    
    NOTE : Don't forget to turn the debug mode OFF (false) in production mode because of security reasons.
    NOTE : Uncomment the cache if you're in production mode, that will help to optimize the performance of the Oxygen system

*/


$Templateloader     =   new \Twig\Loader\FilesystemLoader(  dirname(__DIR__,1) . '/' . OX_template( DEFAULT_TEMPLATE ) );
$twig       =   new \Twig\Environment($Templateloader, [
    //'cache' =>  dirname(__DIR__,1) . '/' . $FILES_SYSTEMS['storage']['cache'],
    'debug' => true,
    // 'auto_reload' => true
]);



$twig->addGlobal('APP_URL', $_ENV['APP_URL']);
$twig->addGlobal('APP_NAME', $_ENV['APP_NAME']);
$twig->addGlobal('APP_VERSION', $_ENV['APP_VERSION']);

$twig->addGlobal('STORAGE_IMAGE', $_ENV['APP_URL'] . '/' . $FILES_SYSTEMS['storage']['upload']['images']);
$twig->addGlobal('STORAGE_VIDEO', $_ENV['APP_URL'] . '/' . $FILES_SYSTEMS['storage']['upload']['videos']);
$twig->addGlobal('STORAGE_AUDIO', $_ENV['APP_URL'] . '/' . $FILES_SYSTEMS['storage']['upload']['audios']);
$twig->addGlobal('STORAGE_FILES', $_ENV['APP_URL'] . '/' . $FILES_SYSTEMS['storage']['upload']['files']);
$twig->addGlobal('STORAGE_AVATAR', $_ENV['APP_URL'] . '/' . $FILES_SYSTEMS['storage']['upload']['avatars']);

$twig->addGlobal('TemplatePath', OX_template( DEFAULT_TEMPLATE ) );
$twig->addGlobal('IS_FORCE_MOBILE_TEMPLATE', FORCE_MOBILE_TEMPLATE );
(isset($_GET['message'])) ? $twig->addGlobal('GET_MESSAGE', $_GET['error'] ) : null;
(isset($_SESSION['Login']) ? $twig->addGlobal('IsLogin', TRUE ) : $twig->addGlobal('IsLogin', FALSE ) );
(isset($_SESSION['Login']) ? $twig->addGlobal('UserName', $database->fetchField("SELECT name FROM users WHERE id = '$_SESSION[Login]' ") ) : null );
?>