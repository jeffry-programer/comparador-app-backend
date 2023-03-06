<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class List_Has_User extends Model
{
    use HasFactory;

    protected $table = 'listacompra_has_usuario';

    public $timestamps = false;
}
