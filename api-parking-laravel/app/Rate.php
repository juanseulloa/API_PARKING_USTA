<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $table = 'rate';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $primaryKey = 'ID_RATE';

    public function service()
    {
        return $this->hasMany('App\Service');
    }
}
