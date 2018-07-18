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
        'votee_id',
        'campaign_position_id',
        'campaign_id',
        'voter_id'
    ];
}
