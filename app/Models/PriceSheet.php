<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSheet extends Model
{
    use HasFactory;

    protected $table = 'price_sheets';

    protected $fillable = [
        'supplier_id',
        'name',
        'sheet_date',
        'created_by',
        'status',
    ];

    // Quan hệ 1 PriceSheet có nhiều PriceSheetItem
    public function items()
    {
        return $this->hasMany(PriceSheetItem::class, 'sheet_id');
    }

    // Quan hệ tới Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Quan hệ tới Người tạo
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
