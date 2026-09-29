<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_code', 'business_name', 'contact_person', 'phone',
        'email', 'address', 'license_no', 'credit_limit', 'opening_balance', 'is_active', 'deleted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted' => 'boolean',
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }
}
