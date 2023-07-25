<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TBlog_post extends Model
{
    //
    protected $table = "posts";
    public function categoria()
    {
        return $this->belongsTo(TBlog_categoria::class,'categoria_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function imagenes()
    {
        return $this->hasMany(TBlog_imagen::class,'post_id');
    }
}
