<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceSheetItemResult extends Model
{
   use HasFactory;

    protected $table = 'price_sheet_item_results';

    protected $fillable = [
        'price_sheet_item_id',
        'pricing_rule_detail_id',
        'margin_percent',
        'selling_price',
        'profit',
    ];

    // Quan hệ ngược về PriceSheetItem
    public function item()
    {
        return $this->belongsTo(PriceSheetItem::class, 'price_sheet_item_id');
    }

    // Quan hệ tới PricingRuleDetail (nếu cần truy vết quy tắc áp dụng)
    public function pricingRuleDetail()
    {
        return $this->belongsTo(PricingRuleDetail::class, 'pricing_rule_detail_id');
    }
}
