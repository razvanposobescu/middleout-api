<?php

use App\Repositories\User\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function ()
{


//    $repo = app(UserRepository::class);

    $user1 = app(UserRepository::class);
    $user2 = app(UserRepository::class);


    dd($user1->getById(1), $user1->getById(53), $user2->all());



    return view('welcome');
});
