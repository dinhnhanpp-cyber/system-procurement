<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $table = 'pricing_rules';

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Quan hệ 1 - N: Một bộ công thức có nhiều chi tiết quy tắc con
     */
    public function details()
    {
        return $this->hasMany(PricingRuleDetail::class, 'rule_id', 'id');
    }
}
