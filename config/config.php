<?php
/**
 * General Configuration
 *
 * OxygenFramework 2.0
 *
 * @package    OxygenFramework
 * @author     REDWAN AOUNI <aouniradouan@gmail.com>
 * @copyright  2024 - REDWAN AOUNI
 * @version    2.0.0
 */

return [
  'template' => env('DEFAULT_TEMPLATE', 'default'),
  'mobile_template' => env('FORCE_MOBILE_TEMPLATE', false),
  'verification' => [
    'email' => true,
    'phone' => true,
  ],
];