<?php

namespace App\Models;

use App\Models\BaseModel;

class User extends BaseModel
{

    protected static $table = 'users';
    /**
     * field in users table
     */
    public $full_name;
    public $email;
    public function __construct()
    {
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id');
    }
}
