<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Township extends Model
{
  protected $fillable=['name'];

  // For a township can have many rental lists
  
  public function rental_lists()
    {
        return $this->hasMany('App\Rental_list');
    }

}
