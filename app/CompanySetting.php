<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = ['company_name', 'company_email', 'company_phone', 'website', 'address', 'timezone', 'latitude', 'longitude', 'locale', 'logo', 'system_update'];
    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('app-logo.png');
        }
        return asset_url('app-logo/' . $this->logo);
    }

    public function supportedUntilIsPast()
    {
        return Carbon::parse($this->supported_until)->isPast();
    }

    public function isWithin175Days()
    {
        return Carbon::parse($this->supported_until)->diffInDays(Carbon::now()) <= 175;
    }

    public function isBeyond200DaysAndWithin360Days()
    {
        $daysDifference = Carbon::parse($this->supported_until)->diffInDays(Carbon::now());

        return $daysDifference > 200 && $daysDifference <= 360;
    }
}
