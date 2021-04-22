<?php
namespace App\Models;
use App\Models\BaseModel;
use App\Models\User;

class Blog extends BaseModel
{

    protected static $table = 'blogs';
    /**
     * field in blogs table
     */
    public $title;
    public $content;
    public $user_id;
    public $view;
    public $is_activated;
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}