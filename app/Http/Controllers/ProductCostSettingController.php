<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCostSetting;
use Illuminate\Http\Request;

class ProductCostSettingController extends Controller
{
    //
   public function add()
{
    // Lấy danh sách sản phẩm để người dùng chọn khi thiết lập chi phí
    $products = Product::select('id', 'short_name', 'internal_code')->get();

    return view('admin.productCostSetting.add', compact('products'));
}
public function addStore(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'product_id'        => 'required|exists:products,id|unique:product_cost_settings,product_id',
        'import_tax'        => 'nullable|numeric|min:0|max:100',
        'vat'               => 'nullable|numeric|min:0|max:100',
        'service_percent'   => 'nullable|numeric|min:0|max:100',
        'warehouse_percent' => 'nullable|numeric|min:0|max:100',
        'thc'               => 'required|numeric|min:0',
        'do'                => 'required|numeric|min:0',
        'cic'               => 'required|numeric|min:0',
        'cleaning'          => 'required|numeric|min:0',
    ], [
        'product_id.required' => 'Vui lòng chọn sản phẩm.',
        'product_id.exists'   => 'Sản phẩm chọn không tồn tại.',
        'product_id.unique'   => 'Sản phẩm này đã được thiết lập cấu hình chi phí.',

        'import_tax.numeric'  => 'Thuế nhập khẩu phải là số.',
        'vat.numeric'         => 'Thuế VAT phải là số.',
        'service_percent.numeric'   => 'Tỷ lệ Service phải là số.',
        'warehouse_percent.numeric' => 'Tỷ lệ kho phải là số.',

        'thc.required'        => 'Vui lòng nhập phí THC.',
        'thc.numeric'         => 'Phí THC phải là số.',
        
        'do.required'         => 'Vui lòng nhập phí D/O.',
        'do.numeric'          => 'Phí D/O phải là số.',
        
        'cic.required'        => 'Vui lòng nhập phí CIC.',
        'cic.numeric'         => 'Phí CIC phải là số.',
        
        'cleaning.required'   => 'Vui lòng nhập phí Cleaning.',
        'cleaning.numeric'    => 'Phí Cleaning phải là số.',
    ]);

    // 2. Lưu cấu hình chi phí sản phẩm (Database tự tính cột LCC via storedAs)
    ProductCostSetting::create([
        'product_id'        => $request->input('product_id'),
        'import_tax'        => $request->input('import_tax', 0.00),
        'vat'               => $request->input('vat', 5.00),
        'service_percent'   => $request->input('service_percent', 3.00),
        'warehouse_percent' => $request->input('warehouse_percent', 1.00),
        'thc'               => $request->input('thc'),
        'do'                => $request->input('do'),
        'cic'               => $request->input('cic'),
        'cleaning'          => $request->input('cleaning'),
    ]);

    // 3. Chuyển hướng về trang danh sách kèm thông báo thành công
    return redirect('admin/productCostSetting/list')->with('success', 'Thêm cấu hình chi phí sản phẩm thành công!');
}
   public function list(Request $request)
{
    $keyword = $request->input('keyword');

    // Query lấy danh sách kèm thông tin sản phẩm (tránh N+1 query)
    $settings = ProductCostSetting::with('product')
        ->when($keyword, function ($query, $keyword) {
            $query->whereHas('product', function ($q) use ($keyword) {
                $q->where('short_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('internal_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('international_code', 'LIKE', "%{$keyword}%");
            });
        })
        ->latest('id')
        ->paginate(10); // Phân trang 10 bản ghi / trang

    return view('admin.productCostSetting.list', compact('settings'));
}
public function edit(Request $request, $id)
{
    // 1. Tìm cấu hình chi phí sản phẩm theo ID, kèm theo thông tin sản phẩm liên quan
    $setting = ProductCostSetting::with('product')->findOrFail($id);

    // 2. Lấy danh sách sản phẩm để đổ vào select box
    $products = Product::all();

    // 3. Trả về view edit kèm dữ liệu cấu hình chi phí và danh sách sản phẩm
    return view('admin.productCostSetting.edit', compact('setting', 'products'));
}
public function editStore(Request $request, $id)
{
    // 1. Tìm bản ghi cấu hình chi phí theo ID
    $setting = ProductCostSetting::findOrFail($id);

    // 2. Validate dữ liệu đầu vào (ignore ID hiện tại đối với rule unique)
    $request->validate([
        'product_id'        => 'required|exists:products,id|unique:product_cost_settings,product_id,' . $setting->id,
        'import_tax'        => 'nullable|numeric|min:0|max:100',
        'vat'               => 'nullable|numeric|min:0|max:100',
        'service_percent'   => 'nullable|numeric|min:0|max:100',
        'warehouse_percent' => 'nullable|numeric|min:0|max:100',
        'thc'               => 'required|numeric|min:0',
        'do'                => 'required|numeric|min:0',
        'cic'               => 'required|numeric|min:0',
        'cleaning'          => 'required|numeric|min:0',
    ], [
        'product_id.required' => 'Vui lòng chọn sản phẩm.',
        'product_id.exists'   => 'Sản phẩm chọn không tồn tại.',
        'product_id.unique'   => 'Sản phẩm này đã được thiết lập cấu hình chi phí.',

        'import_tax.numeric'        => 'Thuế nhập khẩu phải là số.',
        'vat.numeric'               => 'Thuế VAT phải là số.',
        'service_percent.numeric'   => 'Tỷ lệ Service phải là số.',
        'warehouse_percent.numeric' => 'Tỷ lệ kho phải là số.',

        'thc.required' => 'Vui lòng nhập phí THC.',
        'thc.numeric'  => 'Phí THC phải là số.',
        
        'do.required'  => 'Vui lòng nhập phí D/O.',
        'do.numeric'   => 'Phí D/O phải là số.',
        
        'cic.required' => 'Vui lòng nhập phí CIC.',
        'cic.numeric'  => 'Phí CIC phải là số.',
        
        'cleaning.required' => 'Vui lòng nhập phí Cleaning.',
        'cleaning.numeric'  => 'Phí Cleaning phải là số.',
    ]);

    // 3. Cập nhật cấu hình chi phí sản phẩm (Database tự re-calculate cột LCC)
    $setting->update([
        'product_id'        => $request->input('product_id'),
        'import_tax'        => $request->input('import_tax', 0.00),
        'vat'               => $request->input('vat', 5.00),
        'service_percent'   => $request->input('service_percent', 3.00),
        'warehouse_percent' => $request->input('warehouse_percent', 1.00),
        'thc'               => $request->input('thc'),
        'do'                => $request->input('do'),
        'cic'               => $request->input('cic'),
        'cleaning'          => $request->input('cleaning'),
    ]);

    // 4. Chuyển hướng về trang danh sách kèm thông báo thành công
    return redirect('admin/productCostSetting/list')->with('success', 'Cập nhật cấu hình chi phí sản phẩm thành công!');
}
public function delete($id)
{
    // 1. Tìm bản ghi cấu hình chi phí theo ID, không thấy sẽ bắn lỗi 404
    $setting = ProductCostSetting::findOrFail($id);

    // 2. Thực hiện xóa khỏi Database
    $setting->delete();

    // 3. Quay lại trang trước kèm thông báo thành công
    return redirect()->back()->with('success', 'Xóa cấu hình chi phí sản phẩm thành công!');
}
}
