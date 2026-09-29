<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    public $timestamps = false;
    protected $fillable = ['customer_payment_id','sale_id','amount'];
    protected $casts = ['amount'=>'decimal:2'];
    public function payment(): BelongsTo { return $this->belongsTo(CustomerPayment::class, 'customer_payment_id'); }
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
}
