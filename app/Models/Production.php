<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    
    public function getDateAttribute()
    {
        return date('d M Y', strtotime($this->attributes['date']));
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}