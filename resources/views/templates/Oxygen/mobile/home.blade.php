<?php
// $result = $database->query('SELECT * FROM users');

// foreach ($result as $row) {
// 	echo $row->id;
// 	echo $row->name;
// }

//echo $result->getRowCount(); // returns the number of rows if is known

//echo var_dump($APP);

use Carbon\Carbon;

$now = Carbon::now(); // will use timezone as set with date_default_timezone_set
// PS: we recommend you to work with UTC as default timezone and only use
// other timezones (such as the user timezone) on display

$DateTime = Carbon::now(new DateTimeZone( $APP['TIMEZONE'] ));
//echo $DateTime;

 // OX_SendEmail("aouniradouan99@gmail.com", "Redwan Aouni", "Oxygen", "Hello My Dear");
//OX_SendSms("+213772049732","Oussama");

// OX_STRIPE_OmniPay(4242424242424242, 6, 2030, 350, "1000.00", "DZD" );
// OX_Paysera_OmniPay("hdmi@gmail.com",null,null,4);
//OX_Mollie_OmniPay(10,'EUR','Good Description');
//$Array = OX_PaymentGetways_Status("STRIPE");


// echo (new thiagoalessio\TesseractOCR\TesseractOCR('https://raw.githubusercontent.com/thiagoalessio/tesseract-ocr-for-php/e85c7bcd8617607a261d0ecf37a5b8e42eb80c53/tests/EndToEnd/images/text.png'))
//     ->run();



 // $Face1 = OX_FaceDetection("https://wl-brightside.cf.tsp.li/resize/728x/jpg/e35/4ec/4bb0ae5afebff4afdab1b29f5b.jpg");
 // $Face2 = OX_FaceDetection("https://www.dzairdaily.com/wp-content/uploads/2021/04/souhila-mallem-refuse-films-francais-scenes-osees.jpg");
// echo $Face['FaceToken'];


//echo OX_FaceCompare("https://www.youngisthan.in/wp-content/uploads/2018/03/4.png","https://pbs.twimg.com/media/EBOD0PxUEAAARFe.jpg");

// reference the Dompdf namespace


?>