<?php
/**
 * filesystems.php
 *
 * Files Systems and path
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 * NOTE THAT THIS FILE INCLUDE ALL THE MAIN PATH PLEASE DON'T CHANGE ANYTHING UNLESS YOU'RE KNOW WHAT YOU'RE DOING
 */


$FILES_SYSTEMS = [


    // Resources Path
        'public' => [
            'path'      => 'public'
        ],

    // Resources Path
        'resources' => [
            'path'      => 'resources'
        ],


    // View Path
        'view' => [
            'path'      => 'views'
        ],
        

    // Templates Path
        'templates' => [
            'Oxygen'    => 'templates/Oxygen',
            'Tabler'   => 'templates/Tabler'
        ],


    // Templates Types eg : Desktop, Panel, Car ect..
        'templates_types' => [
            'Desktop'   => 'desktop',
            'Mobile'    => 'mobile',
            'Tablet'    => 'tablet',
            'Tv'        => 'tv'
        ],


    // Storage and Caching path if we use cache
        'storage' => [
            'upload'    => [
                'images'    =>  'storage/upload/images',
                'videos'    =>  'storage/upload/videos',
                'audios'    =>  'storage/upload/audios',
                'files'     =>  'storage/upload/files',
                'avatars'   =>  'storage/upload/avatars'
            ],
            'cache'     => 'storage/cache',
            'temp'      => 'storage/temp'
        ]


];


?>