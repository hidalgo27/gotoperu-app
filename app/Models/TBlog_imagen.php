<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TBlog_imagen extends Model
{
    //
    protected $table = "imagens";
    public function post()
    {
        return $this->belongsTo(TBlog_post::class, 'post_id');
    }
}
