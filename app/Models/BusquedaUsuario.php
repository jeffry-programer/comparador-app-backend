<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusquedaUsuario extends Model
{
    use HasFactory;

    protected $table = 'busquedausuario';

    public $timestamps = false;
}
