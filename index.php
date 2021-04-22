<?php 
require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Blog;

echo "<pre>";

// var_dump(User::where('id', '<', 5));

$user = User::all();
// var_dump($user->comments()->get());
// var_dump($user);



// var_dump($comment->user());
// var_dump($user->comments());
