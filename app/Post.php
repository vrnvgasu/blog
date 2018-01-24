<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // стандартный пакет ларавеля для работы с датами

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;

    protected $fillable = ['title', 'content', 'date', 'description'];

    public function category ()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function author ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'posts_tags',
            'post_id',
            'tag_id'
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = Auth::user()->id;
        $post->save();

        return $post;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete(); // удалили объект
    }

    public function removeImage() {
        if ($this->image != null) {
            Storage::delete('uploads/' . $this->image); // Класс Storage может сразу удалять указанный файл
            // (удаляем старую картинку к посту)
        }
    }

    public function uploadImage($image) {
        if ($image == null) {
            return;
         }

        $this->removeImage();

        $filename = str_random(10) . '.' . $image->extension(); // $image - это уже готовый объект
                                                                      // изображения в ларавеле
                                                    // метод extension() возвращает расширение изображения
        $image->storeAs('uploads', $filename);   // сохраняем изображение в папке 'uploads' с именем $filename
        $this->image = $filename;               // передаем атрибуту image (в БД) имя картинки
        $this->save();
    }

    public function getImage() // вернуть картинку данного поста (для тега img)
    {
        if ($this->image == null) {
            return '/img/no-image.png'; // если нет картинки, то возврщаем дефолтную
        }
        return '/uploads/' . $this->image;
    }

    public function setCategory($id)
    {
        if ($id == null) {
            return;
        }
        $this->category_id = $id;
        $this->save();

        // а могли бы сразу свзяать с моделью категорий
        // $category = Category::find($id);
        // $this->category()->save($category);
    }

    public function setTags($ids) // передали сразу массив
    {
        if ($ids == null) {
            return;
        }

        $this->tags()->sync($ids); // синхронизировали массив с моделью тегов
                                //[1,4,9] - допустим такой массив, теперь у нас этот
                                // пост связан с этими тегами
                                // потом обновили $ids на [5], и пост стал связан только с 5

    }

    public function setDraft() // Присваиваем статус черновика
                            // при создании пост будет по умолчанию черновиком (см. migrations)
    {
        $this->status = Post::IS_DRAFT; // 0 - делаем для примера через константу, чтобы было легче читать
        $this->save();
    }

    public function setPublic() // Присваиваем статус публикации
    {
        $this->status = Post::IS_PUBLIC; // 1
        $this->save();
    }

    public function toggleStatus($value) // Переключатель статуса публикация/черновик
                                        // в $value передается любое значение при нажатии
    {
        if ($value == null) {  // если передали null - черновик
            return $this->setDraft();
        }

        return$this->setPublic();
    }

    public function setFeatured() // Присваиваем статус рекомендованной статьи
    {
        $this->is_featured  = 1;
        $this->save();
    }

    public function setStandart() // Присваиваем статус обычной статьи
    {
        $this->is_featured = 0;
        $this->save();
    }

    public function toggleFeatured($value) // Переключатель статуса рекомендовано/обычная статья
        // в $value передается любое значение при нажатии
    {
        if ($value == null) {  // если передали null - обычная статья
            return $this->setStandart();
        }

        return$this->setFeatured();
    }

    // Делаем функцию сеттор (мутатор) для поля даты (date)
    // теперь когда мы передаем date в методы из формы, то сначала запускаем эту функцию
    public function setDateAttribute($value)
    {
        // создаем дату из одного формата и преобразуем в другой
        $date = Carbon::createFromFormat('d/m/y', $value)->format('Y-m-d');
        //передаем это значение дальше внутри формы к методу add
        $this->attributes['date'] = $date;
    }

    // Делаем функцию геттор (аксессор) для поля даты (date)
    // теперь когда мы возвращаем date на страницу из БД, то сначала запускаем эту функцию
    public function getDateAttribute($value)
    {
        // создаем дату из одного формата и преобразуем в другой
        $date = Carbon::createFromFormat('Y-m-d', $value)->format('d/m/y');
        //передаем это значение дальше на страницу
        return $date;
    }

    public function getCategoryTitle()
    {
        if ($this->category != null) {
            return $this->category->title;
        }
        return 'Нет категории';
    }

    public function getTagsTitles()
    {
        if ($this->tags) {
            //dd($this->tags);
            return (implode(', ', $this->tags->pluck('title')->all()));
        }
        return 'Нет тегов';
    }

    public function getCategoryId()
    {
        return ($this->category->id)? $this->category->id:null;
    }

    public function getDate()
    {
        return Carbon::createFromFormat('d/m/y', $this->date)->format('F d, Y'); // F  - название месяца
    }

    public function hasPrevious()
    {
        // ищем в классе запись, где
        // id < id этого объекта, и выбираем максимальный id из полученных записей
        // если наш id=5, то получим записи id: 1 2 3 4
        // из них выберем 4
        return self::where('id', '<', $this->id)->max('id');
    }

    public function getPrevious()
    {
        $postID = $this->hasPrevious(); // получим предыдущий id
        return self::find($postID); // находим этот пост в БД и возвращаем
    }

    public function hasNext()
    {
        // ищем в классе запись, где
        // id > id этого объекта, и выбираем минимальный id из полученных записей
        return self::where('id', '>', $this->id)->min('id');
    }

    public function getNext()
    {
        $postID = $this->hasNext();
        return self::find($postID);
    }

    public function related()
    {
        // делаем КАРУСЕЛЬ
        // получаем все посты КРОМЕ текущего
        return self::all()->except($this->id);
    }

    public function hasCategory()
    {
        return $this->category !=null ? true : false;
    }

    public function getComments()
    {
        return $this->comments->where('status', 1)->all();
    }
}
