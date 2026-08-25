<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'internal_code',
        'short_name',
        'international_name',
        'international_code',
        'category_id',
        'supplier_id',
        'unit',
        'status',
    ];

    /**
     * Mối quan hệ: Sản phẩm thuộc về một Loại sản phẩm (BelongsTo)
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Mối quan hệ: Sản phẩm thuộc về một Nhà cung cấp (BelongsTo)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
