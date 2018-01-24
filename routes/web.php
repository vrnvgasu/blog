<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// перекидываем обращение к домену на HomeController и его метод index
Route::get('/', 'HomeController@index');
// запрос делаем к slug (альтернатива id) поста
// назвали этот роут 'post.show'
Route::get('/posts/{slug}', 'HomeController@show')->name('post.show');
Route::get('/tag/{slug}', 'HomeController@tag')->name('tag.show');
Route::get('/category/{slug}', 'HomeController@category')->name('category.show');
Route::post('/subscribe', 'SubsController@subscribe');
Route::get('/verify/{token}', 'SubsController@verify');

//миддлвер для проверки аутентифицированных пользователей (втроен в ларавель)
Route::group(['middleware' => 'auth'], function() {
    Route::get('/profile', 'ProfileController@index');
    Route::post('/profile', 'ProfileController@store');
    Route::get('/logout', 'AuthController@logout');
    Route::post('/comment', 'CommentController@store');
});

//миддлвер для проверки неаутентифицированных пользователей (втроен в ларавель)
Route::group(['middleware' => 'guest'], function() {
    //Этот метод выводит на метод, который показывает форму
    Route::get('/register', 'AuthController@registerForm');
//Этот метод выводит на метод, который регистрирует данные из формы
    Route::post('/register', 'AuthController@register');
    //даем этому роутеру имя "логин" для миддлвара 'auth'
    // если этот встроенный миддлвар не пропустил человека к роутеру
     // (пользователь не аутентифицирован, то ищет именно роутер "логин"
    Route::get('/login', 'AuthController@loginForm')->name('login');
    Route::post('/login', 'AuthController@login');
});


Route::group([
        'prefix'=>'admin',
        'namespace'=>'Admin',
        'middleware' => 'admin'
    ], function(){
    Route::get('/', 'DashboardController@index');
    Route::resource('/categories', 'CategoriesController');
    Route::resource('/tags', 'TagsController');
    Route::resource('/users', 'UsersController');
    Route::resource('/posts', 'PostsController');
    Route::get('/comments', 'CommentsController@index');
    Route::get('/comments/toggle/{id}', 'CommentsController@toggle');
    Route::delete('/comments/{id}/destroy', 'CommentsController@destroy')->name('comments.destroy');
    Route::resource('/subscribers', 'SubscribersController');
});

