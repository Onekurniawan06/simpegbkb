<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Override default primary key name 'id'
    protected $primaryKey = 'role_id';

    // Disable timestamps if you chose not to use them in the migration
    public $timestamps = false;

    // Define which attributes are mass assignable if you use Role::create()
    protected $fillable = ['role_id', 'role_name'];
}
