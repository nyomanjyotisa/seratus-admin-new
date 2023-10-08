<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getCreatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['updated_at']));
    }

    public function getDateAttribute()
    {
        return date('d M Y', strtotime($this->attributes['date']));
    }
    
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
    
    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }
    
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
    
    public function otherIncomes(): HasMany
    {
        return $this->hasMany(OtherIncome::class);
    }
}
