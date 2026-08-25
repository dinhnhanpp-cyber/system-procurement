<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSheetItem extends Model
{
    use HasFactory;

    protected $table = 'price_sheet_items';

    protected $fillable = [
        'sheet_id',
        'product_id',
        
        // 1. Dữ liệu nhập tay
        'ttl',
        'fob',
        'logistics',
        'competitor_price',
        'competitor_discounted_price', // <-- THÊM CỘT NÀY

        // 2. Snapshot cấu hình chi phí
        'import_tax',
        'vat',
        'service_percent',
        'warehouse_percent',
        'thc',
        'do',
        'cic',
        'cleaning',
        'lcc',
        'operation',

        // 3. Kết quả tính toán
        'price_amount',
        'tax_amount',
        'service_amount',
        'warehouse_amount',
        'total_amount',
        'cost_per_ton',
    ];

    // Quan hệ ngược về PriceSheet
    public function sheet()
    {
        return $this->belongsTo(PriceSheet::class, 'sheet_id');
    }

    // Quan hệ tới Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // THÊM: Quan hệ 1 Item có nhiều Kịch bản Giá bán & Lợi nhuận
    public function results()
    {
        return $this->hasMany(PriceSheetItemResult::class, 'price_sheet_item_id');
    }
}
