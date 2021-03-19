<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name'];

    /**
     * Create Admin Role
     * 
     * @return \App\Models\Role
     */
    public static function adminRole()
    {
        return self::create([
            'name'          => 'admin',
            'display_name'  => 'Administrator'
        ]);
    }
}
