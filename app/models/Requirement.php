<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Requirement extends Model{

    protected $table = 'user_requirements';
    protected $fillable = ['user_id', 'semester_requirements', 'asunto', 'carta_explicativa','asunto_ingresos', 'carta_explicativa_ingresos' ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
