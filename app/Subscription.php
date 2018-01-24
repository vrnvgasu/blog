<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model // подписчики
{
    public static function add($email)
    {
        $sub = new static;
        $sub->email = $email; // валидацию будем делать на фронте

        $sub->save();

        return $sub;
    }

    public function generateToken()
    {
        $this->token = str_random(100);
        $this->save();
    }

    public function remove()
    {
        $this->delete();
    }
}
