<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    // Menonaktifkan manajemen timestamps otomatis Laravel
    public $timestamps = false; 

    protected $table = 'berita'; 
    // ... properti lain seperti fillable ...
}
