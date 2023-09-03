<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPostCategory extends Model
{
    use HasFactory;

    protected $table = "categorias";

    public function post()
    {
        return $this->hasOne(TPost::class,'categoria_id');
    }

}
