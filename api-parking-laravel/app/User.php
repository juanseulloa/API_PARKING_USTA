<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $table = 'users';
    protected $primaryKey = 'ID_USER';

    /**
     * relationship
     * @return hasMany
     */
    public function login()
    {
        return $this->hasMany('App\Login');
    }

    /**
     * @return HasMany
     */
    public function service()
    {
        return $this->hasMany('App\Service');
    }


}
