<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TBlog_categoria extends Model
{
    protected $table = "categorias";

    public function post()
    {
        return $this->hasOne(TBlog_post::class,'categoria_id');
    }
}
