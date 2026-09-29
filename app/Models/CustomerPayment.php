<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPayment extends Model
{
    protected $fillable = ['customer_id','payment_date','amount','reference_no','created_by'];
    protected $casts = ['payment_date'=>'date','amount'=>'decimal:2'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function allocations(): HasMany { return $this->hasMany(PaymentAllocation::class); }
}
