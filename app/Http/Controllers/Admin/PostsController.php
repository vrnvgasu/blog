<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use phpDocumentor\Reflection\DocBlock\Tag;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('admin.posts.index', ['posts'=>$posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // передаем массив всех тегов и категорий в видемасси
        // [id => title]. Для категорий выведет (пример):
        //array:4 [[3 => "работа"], [б4 => "123"], [6 => "кат 1"], [7 => "кат 2"]]
        $tags = Tag::pluck('title', 'id')->all();
        $categories = Category::pluck('title', 'id')->all();

        //compact используем вместо массива ['posts'=>$posts]
        // как сокращенную запись
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'date' => 'required',
            'image' => 'nullable|image'
        ]);

        $post = Post::add($request->all());
        $post->uploadImage($request->file('image'));
        //закину полученное значение из формы из select
        // с именем category_id
        $post->setCategory($request->get('category_id'));
        // тоже с тегами
        $post->setTags($request->get('tags'));
        $post->toggleStatus($request->get('status'));
        $post->toggleFeatured($request->get('is_featured'));

        return redirect()->route('posts.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $categories = Category::pluck('title', 'id')->all();
        $tags = Tag::pluck('title', 'id')->all();
        //выбираем только id по тегам, связанным с постами
        $selectedTags = $post->tags->pluck('id')->all();

        return view('admin.posts.edit', compact(
           'categories', 'tags', 'post', 'selectedTags'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
           'title'=>'required',
           'content'=>'required',
           'date'=>'required',
           'image'=>'nullable|image'
        ]);
        $post = Post::find($id);
        $post->edit($request->all());
        $post->uploadImage($request->file('image'));
        //закину полученное значение из формы из select
        // с именем category_id
        $post->setCategory($request->get('category_id'));
        // тоже с тегами
        $post->setTags($request->get('tags'));
        $post->toggleStatus($request->get('status'));
        $post->toggleFeatured($request->get('is_featured'));

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::find($id)->remove();
        return redirect()->route('posts.index');
    }
}
