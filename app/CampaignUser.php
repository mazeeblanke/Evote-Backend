<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignUser extends Model
{
    public $table = 'campaign_user';
    //

    public function userdetails () {
        return $this->belongsTo(User::class, 'user_id');
    }
}
