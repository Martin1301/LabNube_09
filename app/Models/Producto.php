<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
 use HasFactory;

 protected $primaryKey = 'idmanga';

 protected $fillable =  array('nombre', 'editorial', 'cantidad','genero','imagen');

 protected $hidden = ['created_at','updated_at'];
}
