<?php

namespace App;

use App\CampaignPosition;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
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
}
