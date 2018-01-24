<?php

namespace App\Http\Controllers;

use App\User;
//use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        // этот встроенный класс аутентификации автоматически находит
        //авторизованного на данный момент пользователя
        $user = Auth::user();
        return view('pages.profile', ['user'=>$user]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'avatar' => 'nullable|image',
        ]);

        $user->edit($request->all());
        $user->generatePassword($request->get('password'));
        $user->uploadAvatar($request->file('avatar'));
        //делаем редирект назад на страницу профиля и передаем
        //параметр status в сессию с данным сообщением
        return redirect()->back()->with('status', 'Профиль успешно сохранен');
    }
}
