<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdList extends Model
{
    use HasFactory;

    protected $table = "productoslista";

    public $timestamps = false;
}
