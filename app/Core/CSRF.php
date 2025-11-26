<?php

namespace Oxygen\Core;

/**
 * CSRF - Cross-Site Request Forgery Protection
 * 
 * Generates and verifies CSRF tokens to protect against CSRF attacks.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class CSRF
{
    /**
     * Constructor - Initialize CSRF token
     */
    public function __construct()
    {
        if (!OxygenSession::has('csrf_token')) {
            OxygenSession::put('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    /**
     * Get the CSRF token
     * 
     * @return string
     */
    public function token()
    {
        return OxygenSession::get('csrf_token');
    }

    /**
     * Generate a hidden input field with the CSRF token
     * 
     * @return string
     */
    public function field()
    {
        return '<input type="hidden" name="csrf_token" value="' . $this->token() . '">';
    }

    /**
     * Verify a CSRF token
     * 
     * @param string $token Token to verify
     * @return bool
     */
    public function verify($token)
    {
        if (empty($token) || !OxygenSession::has('csrf_token')) {
            return false;
        }
        return hash_equals(OxygenSession::get('csrf_token'), $token);
    }
}
