<?php
/**
 * Payment Configuration
 *
 * OxygenFramework 2.0
 *
 * @package    OxygenFramework
 * @author     REDWAN AOUNI <aouniradouan@gmail.com>
 * @copyright  2024 - REDWAN AOUNI
 * @version    2.0.0
 */

return [
	/*
	|--------------------------------------------------------------------------
	| Payment Gateways
	|--------------------------------------------------------------------------
	|
	| Configure payment gateway settings
	| Note: Payment packages removed for lightweight framework
	| Install omnipay packages if needed: composer require omnipay/stripe
	|
	*/

	'default' => 'stripe',

	'gateways' => [
		'stripe' => [
			'enabled' => false,
			'secret_key' => '',
			'public_key' => '',
		],

		'chargily' => [
			'enabled' => false,
			'api_key' => '',
			'secret_key' => '',
		],
	],
];