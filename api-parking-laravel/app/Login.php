<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Login as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Login extends Model
{
    use Notifiable;
    protected $table = 'login';
    protected $primaryKey = 'ID_LOGIN';
    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     *  relationship user-login
     * @return HasMany
     */
    public function service()
    {
        return $this->hasMany('App\Service');
    }

    /**
     * relationship login-service
     * @return BelongsTo
     */
    public function users()
    {
        return $this->belongsTo('App\User', 'ID_USER');
    }
}
