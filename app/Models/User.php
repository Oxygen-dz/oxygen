<?php

namespace Oxygen\Models;

use Oxygen\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
}
