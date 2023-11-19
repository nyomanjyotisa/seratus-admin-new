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

    protected $appends = [
        'sales_count',
        'productions_count',
        'expenses_count',
        'other_incomes_count',
        'sales_total',
        'productions_total',
        'expenses_total',
        'other_incomes_total',
        'total',
        'persentase_laba',
    ];

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

    public function getSalesCountAttribute()
    {
        return $this->sales()->count();
    }

    public function getProductionsCountAttribute()
    {
        return $this->productions()->count();
    }

    public function getExpensesCountAttribute()
    {
        return $this->expenses()->count();
    }

    public function getOtherIncomesCountAttribute()
    {
        return $this->otherIncomes()->count();
    }

    public function getSalesTotalAttribute()
    {
        return $this->sales()->sum('amount') * 1;
    }

    public function getProductionsTotalAttribute()
    {
        return $this->productions()->sum('amount') * 1;
    }

    public function getExpensesTotalAttribute()
    {
        return $this->expenses()->sum('amount') * 1;
    }

    public function getOtherIncomesTotalAttribute()
    {
        return $this->otherIncomes()->sum('amount') * 1;
    }

    public function getTotalAttribute()
    {
        return $this->sales()->sum('amount') - $this->productions()->sum('amount') - $this->expenses()->sum('amount') + $this->otherIncomes()->sum('amount');
    }

    public function getPersentaseLabaAttribute()
    {
        if($this->sales()->sum('amount') == 0){
            return 0;
        }else{
            $persentase_laba = ($this->sales()->sum('amount') - $this->productions()->sum('amount') - $this->expenses()->sum('amount') + $this->otherIncomes()->sum('amount'))/$this->sales()->sum('amount');
            return sprintf('%0.2f', $persentase_laba * 100);
        }
    }
}
