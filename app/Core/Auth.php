<?php

namespace Oxygen\Core;

use Oxygen\Models\User;
use Oxygen\Core\OxygenSession;

/**
 * Auth - Authentication Manager
 * 
 * Handles user authentication, login, logout, and session management.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class Auth
{
    /**
     * Attempt to authenticate a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return bool
     */
    public function attempt($email, $password)
    {
        $user = User::where('email', '=', $email);

        if (empty($user)) {
            return false;
        }

        $user = $user[0]; // Get first result

        // Verify password (assuming password_hash was used)
        if (password_verify($password, $user['password'])) {
            $this->login($user);
            return true;
        }

        return false;
    }

    /**
     * Log in a user
     * 
     * @param array $user User data
     * @return void
     */
    public function login($user)
    {
        OxygenSession::put('user_id', $user['id']);
        OxygenSession::put('user', $user);
        OxygenSession::regenerate(); // Regenerate session ID for security
    }

    /**
     * Log out the current user
     * 
     * @return void
     */
    public function logout()
    {
        OxygenSession::forget('user_id');
        OxygenSession::forget('user');
        OxygenSession::regenerate();
    }

    /**
     * Check if a user is authenticated
     * 
     * @return bool
     */
    public function check()
    {
        return OxygenSession::has('user_id');
    }

    /**
     * Get the currently authenticated user
     * 
     * @return array|null
     */
    public function user()
    {
        if ($this->check()) {
            // Fetch fresh user data from database
            return User::find(OxygenSession::get('user_id'));
        }
        return null;
    }

    /**
     * Get the current user's ID
     * 
     * @return int|null
     */
    public function id()
    {
        return OxygenSession::get('user_id');
    }
}
