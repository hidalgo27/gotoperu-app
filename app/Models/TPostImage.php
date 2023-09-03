<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPostImage extends Model
{
    use HasFactory;
    protected $table = "imagens";
    public function post()
    {
        return $this->belongsTo(TPost::class, 'post_id');
    }
}
