<?php

/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * ads.class.php
 *
 * Ads Class for the Ads Module
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


    class Ads {

       public function CreateAds($name, $description, $price, $category, $image, $user_id) {
            global $database;
            $InsertedID     =   $database->query('INSERT INTO ads', [ // here can be omitted question mark
                'public_id' => $this->GeneratePublicID(),
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'image' => $image
            ]);
            $response = $InsertedID;
            return $response;
        }


        public function ViewAdsbyID($id) {
            global $database,$AdsPageArray,$ErrorPageArray,$twig;

            $AdsAvailability    =   $this->IsAvailableAds($id);
            if($AdsAvailability != false ) {
                $response           =   $database->fetch('SELECT * FROM ads WHERE id = ?', $id);
                $AdsImages          =   $this->AdsImages($id);
                $CategoryID         =   $database->fetchField('SELECT category FROM ads WHERE id = ?', $id);
                $AdsCategory        =   $this->AdsCategories($CategoryID);  
                $AdsInformations    =   $this->AdsInformations($id);
                    // $AdsPageArray array is defined in ads.var.php
                    $AdsPageArray['AdsResponse']        =   $response;
                    $AdsPageArray['AdsImages']          =   $AdsImages;
                    $AdsPageArray['AdsCategory']        =   $AdsCategory;
                    $AdsPageArray['AdsInformations']    =   $AdsInformations;
                    echo $twig->render("ads-details.blade.html",$AdsPageArray);
                }else{
                echo $twig->render("errors.blade.html",$ErrorPageArray);
            }

        }


        private function AdsInformations($id){
            global $database;
            $result     =   $database->fetch('SELECT * FROM ads_informations WHERE ad_id = ?', $id);
            return $result;
        }


        private function AdsCategories($categoryID){
            global $database;
            $result     =   $database->fetch('SELECT * FROM ads_categories WHERE id = ? ', $categoryID);
            return $result;
        }


        private function AdsImages($id){
            global $database;
            $result         =   $database->fetchAll('SELECT * FROM ads_images WHERE ad_id = ?', $id);
            return $result;
        }


        private function IsAvailableAds($id){
            global $database;
            $result         =   $database->query('SELECT * FROM ads WHERE id = ? AND status = ?', $id, 1);
            $CheckExsits    =   $result->getRowCount(); // returns the number of rows if is known
    
            if ($CheckExsits != 0) {
                $response = true;
            }else{
                $response = false;
            }
    
            return $response;
        }

        private function GeneratePublicID() {
            $public_id      = OX_RandomChars();
            return $public_id;
        }


        public function CallRequests($ad_id, $name, $phone){
            global $database;
            $insertedID         =   $database->query("INSERT INTO call_requests", [
                "ad_id"         =>  $ad_id,
                "name"          =>  $name,
                "phone"         =>  $phone,
                "status"        =>  "panding"
            ]);

            $response = $this->Redirect($ad_id."?success=1");
            return $response;

        }





        // public function SuccessRequestAlert(){
        //     if(isset($_GET['success']) AND != empty($_GET['success']) ){
        //         $isSuccess  =   $_GET['success'];
        //         if($isSuccess != 1){
        //             $response   =   false;
        //         }else{
        //             $response   =   true;
        //         }

        //     }else{
        //         $response   =   false;
        //     }
        //     return $response;
        // }

        private function Redirect($to){
            header("Location: $to");
            exit();
        }


    }



?>