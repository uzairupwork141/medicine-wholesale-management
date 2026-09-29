<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'product_code', 'barcode', 'name', 'generic_name', 'category_id',
        'manufacturer_id', 'dosage_form', 'strength', 'pack_size', 'unit',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function manufacturer(): BelongsTo { return $this->belongsTo(Manufacturer::class); }
    public function batches(): HasMany { return $this->hasMany(Batch::class); }
}
