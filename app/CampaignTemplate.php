<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignTemplate extends Model
{
    protected $table = 'campaign_templates'; // Table name

    protected $primaryKey = 'id'; // Primary key

    public $timestamps = true; // Since your table has created_at and updated_at

    protected $fillable = [
        'owner_id',
        'title',
        'slug',
        'html',
        'css',
        'preview',
    ];
}
