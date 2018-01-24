<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function posts()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function allow() // разрешили комментарий
    {
        $this->status = 1;
        $this->save();
    }

    public function disAllow() // запретили комментарий
    {       // комментарий создается со статусом 0
        $this->status = 0;
        $this->save();
    }

    public function toggleStatus() // Переключатель статуса разрешили/запретили
    {
        if ($this->status == 0) {
            return $this->allow();
        }

        return $this->disAllow();
    }

    public function remove() // удаляем комментарий
    {
        $this->delete();
    }
}
