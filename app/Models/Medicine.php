<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'product_code',
        'barcode',
        'name',
        'generic_name',
        'category_id',
        'manufacturer_id',
        'dosage_form',
        'strength',
        'pack_size',
        'unit',
        'default_sale_price',
        'mrp',
        'reorder_level',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}