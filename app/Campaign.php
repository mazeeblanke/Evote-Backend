<?php

namespace App;

use App\User;
use App\CampaignPosition;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{

    public function getActiveAttribute($value)
    {
        return (boolean) $value;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'active',
        'description',
        'start_date',
        'end_date'
    ];

    public function campaign_positions() {
        return $this->hasMany(CampaignPosition::class);
    }

    public function enrolledUsers() {
        return $this->belongsToMany(User::class);
    }

    public function enrolled() {
        return $this->hasOne(CampaignUser::class, 'campaign_id');
    }


}
