<?php

namespace App\Http\Controllers;

use App\Models\PriceSheet;
use App\Models\PriceSheetItem;
use App\Models\PricingRule;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Exports\PriceSheetDetailsExport;
use App\Exports\PriceSheetExport;
use Maatwebsite\Excel\Facades\Excel;
class PriceSheetController extends Controller
{
    //
  public function add()
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        $pricingRules = PricingRule::with('details')->get();
        return view('admin.priceSheet.add', compact('suppliers', 'products', 'pricingRules'));
    }

    public function getCostSettings($productId)
    {
        // Truy vấn dữ liệu từ bảng product_cost_settings theo product_id
        $costSetting = DB::table('product_cost_settings')
                         ->where('product_id', $productId)
                         ->first();

        if (!$costSetting) {
            return response()->json(null);
        }

        return response()->json($costSetting);
    }
  public function addStore(Request $request)
{
    DB::transaction(function () use ($request) {
        // 1. Lưu PriceSheet
        $sheet = PriceSheet::create([
            'supplier_id' => $request->supplier_id,
            'name'        => $request->name,
            'sheet_date'  => $request->sheet_date,
            'created_by'  => auth()->id(),
        ]);

        // 2. Lặp qua danh sách items
        foreach ($request->items as $itemData) {
            // Ép kiểu dữ liệu để tính toán
            $ttl = floatval($itemData['ttl'] ?? 0);
            $fob = floatval($itemData['fob'] ?? 0);
            $logistics = floatval($itemData['logistics'] ?? 0);

            $importTaxPercent = floatval($itemData['import_tax'] ?? 0) / 100;
            $vatPercent = floatval($itemData['vat'] ?? 0) / 100;
            $servicePercent = floatval($itemData['service_percent'] ?? 0) / 100;
            $warehousePercent = floatval($itemData['warehouse_percent'] ?? 0) / 100;

            $lcc = floatval($itemData['lcc'] ?? 0);
            $operation = floatval($itemData['operation'] ?? 0);

            // TỰ TÍNH TOÁN CÁC CON SỐ
            $priceAmount = $fob * $ttl;
            $importTaxAmount = ($priceAmount + $logistics) * $importTaxPercent;
            $vatAmount = ($priceAmount + $logistics + $importTaxAmount) * $vatPercent;
            $taxAmount = $importTaxAmount + $vatAmount;
            $serviceAmount = $priceAmount * $servicePercent;
            $warehouseAmount = $priceAmount * $warehousePercent;

            $totalAmount = $priceAmount + $taxAmount + $serviceAmount + $warehouseAmount + $lcc + $logistics + $operation;
            $costPerTon = $ttl > 0 ? ($totalAmount / $ttl) : 0;

            // LƯU VÀO CƠ SỞ DỮ LIỆU
            $item = $sheet->items()->create([
                'product_id'                 => $itemData['product_id'],
                'ttl'                        => $ttl,
                'fob'                        => $fob,
                'logistics'                  => $logistics,
                'competitor_price'           => $itemData['competitor_price'] ?? null,
                'competitor_discounted_price'=> $itemData['competitor_discounted_price'] ?? null,
                'import_tax'                 => $itemData['import_tax'] ?? 0,
                'vat'                        => $itemData['vat'] ?? 0,
                'service_percent'            => $itemData['service_percent'] ?? 0,
                'warehouse_percent'          => $itemData['warehouse_percent'] ?? 0,
                'lcc'                        => $lcc,
                'operation'                  => $operation,

                // Gán kết quả tính toán vào đây
                'price_amount'               => $priceAmount,
                'tax_amount'                 => $taxAmount,
                'service_amount'             => $serviceAmount,
                'warehouse_amount'           => $warehouseAmount,
                'total_amount'               => $totalAmount,
                'cost_per_ton'               => $costPerTon,
            ]);

            // 3. Lưu danh sách Results (kịch bản % lợi nhuận)
            if (isset($itemData['results']) && is_array($itemData['results'])) {
                foreach ($itemData['results'] as $resultData) {
                    $marginPercent = floatval($resultData['margin_percent'] ?? 0);
                    
                    $sellingPrice = 0;
                    if ($marginPercent < 100) {
                        $sellingPrice = $costPerTon / (1 - ($marginPercent / 100));
                    }
                    $profit = $sellingPrice - $costPerTon;

                    $item->results()->create([
                        'pricing_rule_detail_id' => $resultData['pricing_rule_detail_id'] ?? null,
                        'margin_percent'         => $marginPercent,
                        'selling_price'          => $sellingPrice,
                        'profit'                 => $profit,
                    ]);
                }
            }
        }
    });

    return redirect('admin/priceSheet/list')->with('success', 'Tạo bảng giá thành công!');
}
public function list(Request $request)
{
    $query = PriceSheet::with('supplier')->withCount('items');

    // Tìm kiếm theo tên hoặc nhà cung cấp nếu có
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where('name', 'like', "%{$keyword}%")
              ->orWhereHas('supplier', function ($q) use ($keyword) {
                  $q->where('name', 'like', "%{$keyword}%");
              });
    }

    $priceSheets = $query->orderBy('id', 'desc')->paginate(15);

    return view('admin/priceSheet/list', compact('priceSheets'));
}
public function detail($id)
    {
        $priceSheet = PriceSheet::with(['supplier', 'items.product', 'items.results'])->findOrFail($id);
        
        return view('admin.priceSheet.detail', compact('priceSheet'));
    } 
    public function edit($id)
{
    $priceSheet = PriceSheet::with(['items.results'])->findOrFail($id);
    $suppliers = Supplier::all();
    $products = Product::all();
    $pricingRules = PricingRule::with('details')->get();

    return view('admin.priceSheet.edit', compact('priceSheet', 'suppliers', 'products', 'pricingRules'));
}
public function editStore(Request $request, $id)
{
    DB::transaction(function () use ($request, $id) {
        $sheet = PriceSheet::findOrFail($id);

        // 1. Cập nhật PriceSheet
        $sheet->update([
            'supplier_id' => $request->supplier_id,
            'name'        => $request->name,
            'sheet_date'  => $request->sheet_date,
        ]);

        // Lấy danh sách ID item gửi lên từ giao diện để giữ lại
        $submittedItemIds = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['id'])) {
                    $submittedItemIds[] = $itemData['id'];
                }
            }
        }

        // Xóa những item cũ không còn tồn tại trong $request (người dùng bấm nút Xóa ở giao diện)
        $sheet->items()->whereNotIn('id', $submittedItemIds)->delete();

        // 2. Lặp qua danh sách items gửi lên
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                // Ép kiểu dữ liệu tính toán tương tự addStore
                $ttl = floatval($itemData['ttl'] ?? 0);
                $fob = floatval($itemData['fob'] ?? 0);
                $logistics = floatval($itemData['logistics'] ?? 0);

                $importTaxPercent = floatval($itemData['import_tax'] ?? 0) / 100;
                $vatPercent = floatval($itemData['vat'] ?? 0) / 100;
                $servicePercent = floatval($itemData['service_percent'] ?? 0) / 100;
                $warehousePercent = floatval($itemData['warehouse_percent'] ?? 0) / 100;

                $lcc = floatval($itemData['lcc'] ?? 0);
                $operation = floatval($itemData['operation'] ?? 0);

                // Tính toán công thức trên Server
                $priceAmount = $fob * $ttl;
                $importTaxAmount = ($priceAmount + $logistics) * $importTaxPercent;
                $vatAmount = ($priceAmount + $logistics + $importTaxAmount) * $vatPercent;
                $taxAmount = $importTaxAmount + $vatAmount;
                $serviceAmount = $priceAmount * $servicePercent;
                $warehouseAmount = $priceAmount * $warehousePercent;

                $totalAmount = $priceAmount + $taxAmount + $serviceAmount + $warehouseAmount + $lcc + $logistics + $operation;
                $costPerTon = $ttl > 0 ? ($totalAmount / $ttl) : 0;

                $payload = [
                    'product_id'                  => $itemData['product_id'],
                    'ttl'                         => $ttl,
                    'fob'                         => $fob,
                    'logistics'                   => $logistics,
                    'competitor_price'            => $itemData['competitor_price'] ?? null,
                    'competitor_discounted_price' => $itemData['competitor_discounted_price'] ?? null,
                    'import_tax'                  => $itemData['import_tax'] ?? 0,
                    'vat'                         => $itemData['vat'] ?? 0,
                    'service_percent'             => $itemData['service_percent'] ?? 0,
                    'warehouse_percent'           => $itemData['warehouse_percent'] ?? 0,
                    'lcc'                         => $lcc,
                    'operation'                   => $operation,

                    'price_amount'                => $priceAmount,
                    'tax_amount'                  => $taxAmount,
                    'service_amount'              => $serviceAmount,
                    'warehouse_amount'            => $warehouseAmount,
                    'total_amount'                => $totalAmount,
                    'cost_per_ton'                => $costPerTon,
                ];

                // Nếu item đã có ID thì update, chưa có thì tạo mới
                if (!empty($itemData['id'])) {
                    $item = $sheet->items()->findOrFail($itemData['id']);
                    $item->update($payload);
                } else {
                    $item = $sheet->items()->create($payload);
                }

                // 3. Xử lý Results (Kịch bản lợi nhuận)
                // Xóa các record kịch bản cũ của item để insert lại theo thông số vừa cập nhật
                $item->results()->delete();

                if (isset($itemData['results']) && is_array($itemData['results'])) {
                    foreach ($itemData['results'] as $resultData) {
                        $marginPercent = floatval($resultData['margin_percent'] ?? 0);

                        $sellingPrice = 0;
                        if ($marginPercent < 100) {
                            $sellingPrice = $costPerTon / (1 - ($marginPercent / 100));
                        }
                        $profit = $sellingPrice - $costPerTon;

                        $item->results()->create([
                            'pricing_rule_detail_id' => $resultData['pricing_rule_detail_id'] ?? null,
                            'margin_percent'         => $marginPercent,
                            'selling_price'          => $sellingPrice,
                            'profit'                 => $profit,
                        ]);
                    }
                }
            }
        }
    });

    return redirect('admin/priceSheet/list')->with('success', 'Cập nhật bảng giá thành công!');
}
public function delete($id)
    {
        $priceSheet = PriceSheet::findOrFail($id);
        
        // Delete items liên quan nếu không cấu hình cascade delete ở DB
        $priceSheet->items()->delete();
        $priceSheet->delete();

        return redirect('admin/priceSheet/list')->with('success', 'Xóa bảng tính giá thành công!');
    }
public function allDetailItems(Request $request)
{
    $keyword = $request->input('keyword');

    $query = PriceSheetItem::with([
        'sheet.supplier', 
        'product', 
        'results.pricingRuleDetail'
    ]);

    if ($keyword) {
        $query->where(function ($q) use ($keyword) {
            // 1. Tìm theo Tên phiếu (Thêm rõ tên bảng price_sheets.name)
            $q->whereHas('sheet', function ($sq) use ($keyword) {
                $sq->where('price_sheets.name', 'like', "%{$keyword}%");
            })
            // 2. Tìm theo Mã / Tên Nhà cung cấp (Tách riêng cấp relation)
            ->orWhereHas('sheet.supplier', function ($ssq) use ($keyword) {
                $ssq->where('suppliers.name', 'like', "%{$keyword}%")
                   ->orWhere('suppliers.code', 'like', "%{$keyword}%");
            })
            // 3. Tìm theo Tên sản phẩm (Thêm rõ tên bảng products)
            ->orWhereHas('product', function ($pq) use ($keyword) {
                $pq->where('products.short_name', 'like', "%{$keyword}%");
            });
        });
    }

    $items = $query->orderBy('price_sheet_items.id', 'desc')->paginate(25);

    return view('admin.priceSheet.allDetails', compact('items'));
}


public function exportExcel(Request $request)
{
    $keyword = $request->get('keyword');
    $ids = $request->get('ids', []); // Mảng các ID được chọn

    return Excel::download(new PriceSheetDetailsExport($keyword, $ids), 'chi_tiet_bang_gia.xlsx');
}
}


