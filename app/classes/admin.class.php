<?php

/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * admin.class.php
 *
 * Admin ads class where the admin classes stored
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2022 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

/* =============================================================================================================================*/
/* =============================================================================================================================*/

    class AdminAds{
        public function index(){
            global $database, $twig, $AdminPage;
            echo $twig->render("add-ads.blade.html",$AdminPage);

        }
    }
 

?>