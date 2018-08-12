<?php

namespace App;

use App\User;
use App\Vote;
use App\Campaign;
use App\CampaignPosition;
use Illuminate\Database\Eloquent\Model;

class CampaignPositionNormination extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'votee_id',
        'campaign_position_id',
        'campaign_id'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function votee()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign_position()
    {
        return $this->belongsTo(CampaignPosition::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class, 'normination_id', 'id');
    }

}
