<?php

namespace App\Models;

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

    public function destinos()
    {
        return $this->belongsToMany(TDestino::class, 'posts_destinos', 'post_id', 'destino_id');
    }

    public function paises()
    {
        return $this->belongsToMany(TPais::class, 'posts_pais', 'post_id', 'pais_id');
    }

}
