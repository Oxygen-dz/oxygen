<?php

( isset ( $_SESSION['FirstLogin'] ) ? $FirstLoginToken = $_SESSION['FirstLogin'] : $FirstLoginToken = NULL );

$welcomePageArrays      =   array();
$LoggedUserInfo         =   $database->fetch("SELECT * FROM users WHERE session_firstlogin = ?", $FirstLoginToken);
$UserName               =   $LoggedUserInfo['name'];
$UserCountry            =   $LoggedUserInfo['country'];
$CountriesList          =   $database->fetchAll("SELECT * FROM countries WHERE is_active = ?", 1);
$UserCountryCapital     =   $database->fetchField("SELECT capital FROM countries WHERE iso2 = ?", $UserCountry);
$welcomePageArrays      =  [
    "PageTitle"             =>  "Just in the time",
    "WelcomeUser"           =>  "Hey " . $UserName,
    "selectCountry"         =>  "Where you live ?",
    "CountriesList"         =>  $CountriesList,
    "sexe"                  =>  "You are",
    "male"                  =>  "Male",
    "female"                =>  "Female",
    "UserCountryCapital"    =>  $UserCountryCapital,
];


?>