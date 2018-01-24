<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();  // получаем все записи категорий
                                        // из таблицы этой модели
                                    //$categories - будем массивом с записями
        return view('admin.categories.index',
            ['categories' => $categories] // 'categories' - массив, к которому будем обращаться
                                        // на стринице в вебе
                                        // $categories - массив, который передаем 'categories'
        );
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request) //Request это означает,
                                    // что получаем запрос из формы
    {
        //dd($request->all()); // выводим массив данных, полученных из формы
        //dd($request->get('title')); // выводим только $_POST['title']

        $this->validate($request, [ // проверка на валидацию данных из формы
            'title' => 'required' // обязательно к заполнению
        ]);

        Category::create($request->all()); // создали новую запись в категории
                // из массива данных. Но чтобы ларавел понял, какие поля из массива
            // заполнять, надо настроить МАССОВОЕ ЗАПОЛНЕНИЕ
        return redirect()->route('categories.index'); // перенаправили после
                                                // на метод index  этого же котроллера
    }

    public function edit($id)
    {
        $category = Category::find($id); // найти запись по значению
        return view('admin.categories.edit', ['category' => $category]);
        // перенаправляем на страницу resouces/view/admin/categories/edit.blade.php
        // и передаем туда полученную запись $category
        // 'category' - это будет переменная на новой странице (можно и по другому назвать
    }

    public function update(Request $request, $id)
        // получаем запрос от формы с новыми данными и дополнительно получаем id нужной записи
    {
        $this->validate($request, [ // проверяем данные из формы изменения на валидацию
           'title' => 'required' // обязательно
        ]);
        $category = Category::find($id);
        $category->update($request->all()); // обновили сразу все значения у записи

        return redirect()->route('categories.index'); // перенаправили после
                                                    // на метод index  этого же котроллера
    }

    public function destroy($id)
        // тут не будем получать объект Request из формы, т.к. ничего по сути не отправляем
    {
        Category::find($id)->delete(); // нашли запись по id и удалили ее
        return redirect()->route('categories.index'); // редирект на метод index
    }
}
