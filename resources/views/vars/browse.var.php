<?php
$Courses   =   $database->fetchAll("SELECT * FROM courses WHERE is_active = ?", 1);

$BrowsePageArray      =   array();
$BrowsePageArray      =  [
    "PageTitle"     =>  "Browse courses - Home of learning",
    "Courses"       =>  $Courses,
];


?>