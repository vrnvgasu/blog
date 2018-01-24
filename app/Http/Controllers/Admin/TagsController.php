<?php

namespace App\Http\Controllers\Admin;

use App\Tag;    // я сам добавил модель Tag
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all(); // взяли все записи тегов
        return view('admin.tags.index', ['tags'=>$tags]);
        // передали все записи странице, на которую переходим
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tags.create');
        // просто переходим на страницу с формой для создания тегов
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
           'title' => 'required'  // обязательное поле
        ]);
        // создаем запись в модели Tag с помощью МАССОВОГО ЗАПОЛНЕНИЯ
        // для этого в модели Tag надо указать: protected $fillable = ['title'];
        Tag::create($request->all());
        // Делаем редирект контроллеру tags методу index
        return redirect()->route('tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = Tag::find($id); // нашли запись по id
        // перенаправляем на страницу admin/tags/edit
        // и передаем туда запись
        return view('admin.tags.edit', ['tag' => $tag]);
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
           'title' => 'required'
        ]);

        $tag = Tag::find($id);// нашли запись по id
        // обновляем запись целиком используя МАССОВОЕ ЗАПОЛНЕНИЕ
        // это $fillable в моделе Tag
        $tag->update($request->all());
        // делаем редирект методу index
        return redirect()->route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);
        $tag->delete();
        // короче:
        // Tad::find($id)->delete();
        return redirect()->route('tags.index');
    }
}
