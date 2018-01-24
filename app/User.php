<?php

namespace App;

use \Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const IS_ACTIVE = 0;
    const IS_BANNED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'textStatus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function add($fields) // создаем пользователя
    {
        $user = new static();
        $user->fill($fields); // в массив $fields можем передать любое
                            // количество значений, но будут выбраны только
                            // значения, указанные в массиве $fillable

        $user->save();

        return $user; // пока вернем данные о пользовате на всякий случай
    }

    public function edit($fields) // обновляем данные о пользователе
    {
        $this->fill($fields);


        $this->save();
    }

    public function generatePassword($password)
    {
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    public function remove() // удаляем пользователя
    {
        $this->removeAvatar(); // удалили картинку аватара пользователя из папки uploads
        $this->delete(); // удалили объект
    }

    public function uploadAvatar($image) { // загружаем аватар пользователю
        if ($image == null) {
            return;
        }
        //dd(get_class_methods($image));
        $this->removeAvatar();

        $filename = str_random(10) . '.' . $image->extension(); // $image - это уже готовый объект
        // изображения в ларавеле
        // метод extension() возвращает расширение изображения
        $image->storeAs('uploads', $filename);   // сохраняем изображение в папке 'uploads' с именем $filename
        $this->avatar = $filename;               // передаем атрибуту image (в БД) имя картинки
        $this->save();
    }

    public function removeAvatar()
    {
        if ($this->avatar) {
            Storage::delete('uploads/' . $this->avatar); // Класс Storage может сразу удалять указанный файл
            // (удаляем старую картинку к посту)
        }
    }

    public function getImage() // вернуть картинку пользователя
    {
        if ($this->avatar == null) {
            return '/img/no-image.gif'; // если нет картинки, то возврщаем дефолтную
        }
        return '/uploads/' . $this->avatar;
    }

    public function makeAdmin() // пользователь стал админом
    {
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormal() // вернули обычные права
    {
        $this->is_admin = 0;
        $this->save();
    }

    public function toggleAdmin($value) // Переключатель статуса админ/обычные права
        // в $value передается любое значение при нажатии
    {
        if ($value == null) {  // если передали null - обычные права
            return $this->makeNormal();
        }

        return$this->makeAdmin();
    }

    public function ban() // забанили пользователя
    {
        $this->status = User::IS_BANNED; // 1 через константу
        $this->save();
    }

    public function unban() // разбанили пользователя
    {       // пользователь создается со статусом 0
        $this->status = User::IS_ACTIVE; // 0 через константу
        $this->save();
    }

    public function toggleBan($value) // Переключатель статуса забанили/разбанили
        // в $value передается любое значение при нажатии
    {
        if ($value == null) {  // если передали null - разбанили
            return $this->unban();
        }

        return$this->ban();
    }
}
