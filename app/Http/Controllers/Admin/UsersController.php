<?php

namespace App\Http\Controllers\Admin;

use App\User;
//use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', ['users'=>$users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
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
           'name'=>'required',
           // email должен быть уникальным в таблице users
            // если в самой таблице поле отличается от названия
            // в передаваемом массиве (например будет users_email),
            // то была бы такая запись:
            //'email'=>'required|email|unique:users,users_email',
           'email'=>'required|email|unique:users',
           'password'=>'required',
            'avatar'=>'nullable|image' // необязательно и должно быть картинкой
            //из разрешенных ларавелем форматов
        ]);
        // с помощью массового присваивания передали значения из формы
        // (кроме аватара) методу add, который написали ранее
        // метод кстати возвращает объект пользователя
        $user = User::add($request->all());
        //самописный метод хеширования пароля
        $user->generatePassword($request->get('password'));
        // в метод uploadAvatar передаем картинку. У $request вызываем встрокенным
        //методом file() (могли бы и get). Этот метод всю инфу по файлу выводит
        $user->uploadAvatar($request->file('avatar'));
        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        //return view('admin.users.edit', compact('user'));
        // compact('user') - тоже самое, что и ['isers'=>$user]
        // т.е. могли бы записать
        return view('admin.users.edit', ['user'=>$user]);
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
        $user = User::find($id);
        $this->validate($request, [
           'name'=>'required',
            'email'=> [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar'=>'nullable|image'
        ]);
        // самописный метод обновления из модели.
        // Был сделан с массовым заполнением
        $user->edit($request->all());
        //самописный метод хеширования пароля
        $user->generatePassword($request->get('password'));
        // используем самописный метод uploadAvatar($image)
        $user->uploadAvatar($request->file('avatar')); // file() - метод из ларовеля
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //вместо встроенного метода delete() используем
        //самописный метод в моделе: remove()
        User::find($id)->remove();
        return redirect()->route('users.index');
    }
}
