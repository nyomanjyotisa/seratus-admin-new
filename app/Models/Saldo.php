<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Saldo extends Model
{
    use HasFactory, LogsActivity;
    
    protected $guarded = [];

    public function getDateAttribute()
    {
        return date('d M Y', strtotime($this->attributes['date']));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
