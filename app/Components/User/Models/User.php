<?php

namespace App\Components\User\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Components\User\Models
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property array $permissions
 * @property string|null $active
 */
class User extends Authenticatable
{
    use HasApiTokens,Notifiable, UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','remember_token','permissions','last_login','active','activation_key','area','mediaselid','username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * the validation rules
     *
     * @var array
     */
    public static $rules = [];
}
