<?php

namespace App;

use App\CampaignPositionNormination;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    protected $guard_name = 'api';

    // protected $appends = ['roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'username',
        'firstname',
        'lastname',
        'security_question',
        'address',
        'city',
        'state',
        'country',
        'phone',
        'avatar',
        'date_of_birth',
        'confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function norminations()
    {
        return $this->hasMany(CampaignPositionNormination::class, 'votee_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

}
