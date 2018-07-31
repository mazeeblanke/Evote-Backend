<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'normination_id',
        // 'votee_id',
        // 'campaign_position_id',
        // 'campaign_id',
        'voter_id'
    ];

    public function normination()
    {
        return $this->hasOne(CampaignPositionNormination::class, 'id', 'normination_id');
    }
}
