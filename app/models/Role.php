<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role Extends Model{

    protected $table = 'roles';
    protected $fillable = ['role'];

    public function user()
    {
        return $this->belongsToMany('App\Models\User')->using('App\Models\Roleuser');
    }
}
