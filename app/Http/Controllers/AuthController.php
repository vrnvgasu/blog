<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('pages.register');
    }

    public function register(Request $request)
    {
        $this->validate($request,  [
           'name' => 'required',
           'email' => 'required|email|unique:users',
           'password' => 'required'
        ]);
        $user = User::add($request->all());
        $user->generatePassword($request->get('password'));
        // делаем редирект сразу на указанный адрес без обращения к роутеру
        return redirect('/login');
    }

    public function loginForm()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //Сценарий:
        //1. проверить и зологинить пользователя на основе email и password
        //2. Если человек ввел неправльный логин или пароль, выводить флеш сообщение
        //3. иначе редирект на главную

        // Метод attempt у класса Auth пытает найти запись в БД, соответствующую
        // переданным значениям. Если находит, то логинит эту запись
        // автоматически Auth:login($user)
        if (Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ])) {
            return redirect('/');
        }
        // редирект на предыдущую страницу, сделать в сессии элемент status и дать ему значение
        return redirect()->back()->with('status', 'Неправильный логин или пароль');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
