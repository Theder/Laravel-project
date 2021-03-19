<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tab', 'name', 'display_name', 'value'
    ];

    /**
     * Get setting by property name
     * 
     * @param string  $propertyName
     * @return mixed
     */
    public static function get($propertyName)
    {
        return self::where('name', $propertyName)->first()->value;
    }

    /**
     * Set setting by property name
     * 
     * @param string  $propertyName
     * @param mixed  $property
     */
    public static function set($propertyName, $property)
    {
        self::where('name', $propertyName)->update(['value' => $property]);
    }
}
