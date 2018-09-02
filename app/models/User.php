<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User Extends Model{
    protected $table = 'users';
    protected $fillable = ['email',
                            'password',
                            'name',
                            'last_name',
                            'birth_date',
                            'id_number'
                            ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    public function requirements()
    {
        return $this->hasMany('App\Models\Requirement');
    }

}
