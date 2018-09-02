<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Roleuser extends Pivot
{
     protected $table = 'role_user';
     protected $fillable = ['user_id', 'role_id'];

    

}
   
    
