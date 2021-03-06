<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable=['typeName'];

    // For each type can contain many rental lists(types of houses) 
    // (eg.condo,mini condo,etc )

    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
