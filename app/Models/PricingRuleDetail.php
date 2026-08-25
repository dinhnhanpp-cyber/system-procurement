<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRuleDetail extends Model
{
    use HasFactory;

    protected $table = 'pricing_rule_details';

    protected $fillable = [
        'rule_id',
        'type',
        'name',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    /**
     * Quan hệ N - 1: Chi tiết quy tắc thuộc về một bộ công thức mẹ
     */
    public function rule()
    {
        return $this->belongsTo(PricingRule::class, 'rule_id', 'id');
    }
}
