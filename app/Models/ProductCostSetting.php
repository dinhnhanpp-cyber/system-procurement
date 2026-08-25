<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class ProductCostSetting extends Model
{
    use HasFactory;

    protected $table = 'product_cost_settings';

    protected $fillable = [
        'product_id',
        'import_tax',
        'vat',
        'service_percent',
        'warehouse_percent',
        'thc',
        'do',
        'cic',
        'cleaning',
        // Lưu ý: KHÔNG thêm 'lcc' vào đây vì cột này do DB tự tính toán (storedAs)
    ];

    /**
     * Mối quan hệ với Model Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Accessor: Format LCC ra dạng chuỗi tiền tệ (Ví dụ: $ 270.00)
     * Cách gọi trên View: {{ $item->formatted_lcc }}
     */
    protected function formattedLcc(): Attribute
    {
        return Attribute::make(
            get: fn () => '$ ' . number_format($this->lcc, 2)
        );
    }

    /**
     * Accessor: Format THC ra dạng chuỗi tiền tệ (Ví dụ: $ 145.00)
     * Cách gọi trên View: {{ $item->formatted_thc }}
     */
    protected function formattedThc(): Attribute
    {
        return Attribute::make(
            get: fn () => '$ ' . number_format($this->thc, 2)
        );
    }

    /**
     * Accessor: Format D/O ra dạng chuỗi tiền tệ (Ví dụ: $ 45.00)
     * Cách gọi trên View: {{ $item->formatted_do }}
     */
    protected function formattedDo(): Attribute
    {
        return Attribute::make(
            get: fn () => '$ ' . number_format($this->do, 2)
        );
    }

    /**
     * Accessor: Format CIC ra dạng chuỗi tiền tệ (Ví dụ: $ 50.00)
     * Cách gọi trên View: {{ $item->formatted_cic }}
     */
    protected function formattedCic(): Attribute
    {
        return Attribute::make(
            get: fn () => '$ ' . number_format($this->cic, 2)
        );
    }

    /**
     * Accessor: Format Cleaning ra dạng chuỗi tiền tệ (Ví dụ: $ 10.00)
     * Cách gọi trên View: {{ $item->formatted_cleaning }}
     */
    protected function formattedCleaning(): Attribute
    {
        return Attribute::make(
            get: fn () => '$ ' . number_format($this->cleaning, 2)
        );
    }
}
