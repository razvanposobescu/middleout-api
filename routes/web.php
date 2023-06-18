<?php

use App\Repositories\Article\ArticleRepository;
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

    /**
     * @var UserRepository $user;
     */
    $userRepository = app(UserRepository::class);

    /**
     * @var \Illuminate\Database\Query\Builder $user1
     */
    $user1 = $userRepository->getById(9);


    /**
     * @var \Illuminate\Database\Query\Builder $user1
     */
    $user2 = $userRepository->getById(1);

//    $user3 = app(ArticleRepository::class)->getById(1);

    $res = app(ArticleRepository::class)->getById(1);

dd($res);

die;

    return view('welcome');
});
