<?php

namespace App\Modules\Blog;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    protected $table='posts_translations';
    protected $fillable=['post_id','title','locale','content'];
    public $timestamps=false;

    public function post(){
        return $this->belongsTo('App\Modules\Blog\Post','post_id','id');
    }

}
