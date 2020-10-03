<?php

namespace App\Modules\Blog;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table='posts';
    protected $fillable=['photo'];


    public function posts(){
        return $this->hasMany('App\Modules\Blog\PostTranslation','post_id','id');
    }

    public function id(){
        $id=$this->id;
        foreach(\LaravelLocalization::getSupportedLocales() as $localeCode => $properties){
            if(\LaravelLocalization::getCurrentLocale() === $localeCode){
                $id=numberFormatter($this->id,$properties['regional']);
                break;
            }
        }

        return $id;
    }

    public function date(){
        return $this->created_at->format('Y-m-d');
    }

    public function title(){
        return $this->posts[0]->title;
    }

    public function content(){
        return $this->posts[0]->content;
    }
    public function photo(){
        return url('storage/'.$this->photo);
    }

}
