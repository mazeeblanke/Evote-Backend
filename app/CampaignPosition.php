<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignPosition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'campaign_id'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function norminations()
    {
        return $this->hasMany(CampaignPositionNormination::class);
    }
}
