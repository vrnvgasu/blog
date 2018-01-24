<?php

namespace App\Providers;

use App\Comment;
use Illuminate\Support\ServiceProvider;
use App\Post;
use App\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    // Этот метод позволяет перехватить все в момент загрузки самого сайта
    public function boot()
    {
        //Когда будет вызываться
        // resources/pages/_sidebar.blade.php,
        // независимо от того, где он находится, вызывать функцию function($view)
        // эта функция принимает в качестве параметра сам вид _sidebar.blade.php
        view()->composer('pages._sidebar', function($view){
            //загружаем вид с переменной popularPosts и передаем ей значение запроса
            $view->with('popularPosts',
                $popularPosts = Post::orderBy('views', 'desc')->take(3)->get());
            $view->with('featuredPosts',
                $featuredPosts = Post::where('is_featured', 1)->take(3)->get());
            $view->with('recentPosts',
                $recentPosts = Post::orderBy('date', 'desc')->take(4)->get());
            $view->with('categories',
                $categories = Category::all());
        });

        view()->composer('admin._sidebar', function($view){
            //загружаем вид с переменной popularPosts и передаем ей значение запроса
            $view->with('newCommentsCount',
                $newCommentsCount = Comment::where('status', 0)->count());

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
