<?php

namespace App\Http\Controllers;

use App\Category;
use App\Tag;
use App\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index() {
        //Находим только по 2 значения записей
        $posts = Post::paginate(2);

        return view('pages.index', [
            'posts' => $posts
        ]);
    }

    function show($slug)
    {
        // обращаемся к базе и ищем в ней запись, где slug = $slug
        // а потом выдаем резульатат или показываем ошибку (firstOrFail)
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('pages.show', ['post'=>$post]);
    }

    function tag($slug)
    {
        // обращаемся к базе и ищем в ней запись, где slug = $slug
        // а потом выдаем резульатат или показываем ошибку (firstOrFail)
        $tag = Tag::where('slug', $slug)->firstOrFail();
        // берем посты связанные с тегами
        //и делаем сразу для них пагинацию
        $posts = $tag->posts()->paginate(2);
        // делаем для тегов и категорий нейтральное название страницы, т.к.
        // они она будем использоваться обеими
        return view('pages.list', ['posts'=>$posts]);
    }

    function category($slug)
    {
        // обращаемся к базе и ищем в ней запись, где slug = $slug
        // а потом выдаем резульатат или показываем ошибку (firstOrFail)
        $category = Category::where('slug', $slug)->firstOrFail();
        //берем посты связанные с категориями
        $posts = $category->posts()->paginate(2);
        return view('pages.list', ['posts'=>$posts]);
    }
}
