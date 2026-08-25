<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ProductController extends Controller
{
    //
 public function add()
{
    // 1. Lấy danh sách loại sản phẩm và nhà cung cấp
    $categories = ProductCategory::all();
    $suppliers = Supplier::all();

    // 2. Truyền biến $categories và $suppliers sang View
    return view('admin.product.add', compact('categories', 'suppliers'));
}
public function addStore(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'internal_code'      => 'required|string|max:50|unique:products,internal_code',
        'short_name'         => 'required|string|max:255',
        'international_name' => 'required|string|max:255',
        'international_code' => 'required|string|max:50',
        'category_id'        => 'required|exists:product_categories,id',
        'supplier_id'        => 'nullable|exists:suppliers,id',
        'unit'               => 'required|string|max:50',
        'status'             => 'required|in:0,1',
    ], [
        'internal_code.required'      => 'Vui lòng nhập mã nội bộ.',
        'internal_code.unique'        => 'Mã nội bộ này đã tồn tại trong hệ thống.',
        'internal_code.max'           => 'Mã nội bộ không được quá 50 ký tự.',
        
        'short_name.required'         => 'Vui lòng nhập tên dễ nhớ.',
        'short_name.max'              => 'Tên dễ nhớ không được vượt quá 255 ký tự.',
        
        'international_name.required' => 'Vui lòng nhập tên quốc tế.',
        'international_name.max'      => 'Tên quốc tế không được vượt quá 255 ký tự.',
        
        'international_code.required' => 'Vui lòng nhập mã quốc tế.',
        'international_code.max'      => 'Mã quốc tế không được quá 50 ký tự.',
        
        'category_id.required'        => 'Vui lòng chọn loại sản phẩm.',
        'category_id.exists'          => 'Loại sản phẩm chọn không hợp lệ.',
        
        'supplier_id.exists'          => 'Nhà cung cấp chọn không hợp lệ.',
        
        'unit.required'               => 'Vui lòng nhập đơn vị tính.',
        'unit.max'                    => 'Đơn vị tính không được quá 50 ký tự.',
        
        'status.required'             => 'Vui lòng chọn trạng thái.',
        'status.in'                   => 'Trạng thái không hợp lệ.',
    ]);

    // 2. Lưu thông tin sản phẩm vào CSDL
    Product::create([
        'internal_code'      => $request->input('internal_code'),
        'short_name'         => $request->input('short_name'),
        'international_name' => $request->input('international_name'),
        'international_code' => $request->input('international_code'),
        'category_id'        => $request->input('category_id'),
        'supplier_id'        => $request->input('supplier_id'),
        'unit'               => $request->input('unit'),
        'status'             => $request->input('status'),
    ]);

    // 3. Chuyển hướng về trang danh sách sản phẩm kèm thông báo thành công
    return redirect('admin/product/list')->with('success', 'Thêm sản phẩm thành công!');
}
public function list(Request $request)
{
    // Tạo truy vấn ban đầu kèm theo Mối quan hệ (Loại sản phẩm & Nhà cung cấp)
    $query = Product::with(['category', 'supplier']);

    // Lọc theo từ khóa (Mã nội bộ, Tên dễ nhớ, Tên quốc tế, Mã quốc tế)
    if ($request->has('keyword') && !empty($request->keyword)) {
        $keyword = trim($request->keyword);
        $query->where(function ($q) use ($keyword) {
            $q->where('internal_code', 'LIKE', "%{$keyword}%")
              ->orWhere('short_name', 'LIKE', "%{$keyword}%")
              ->orWhere('international_name', 'LIKE', "%{$keyword}%")
              ->orWhere('international_code', 'LIKE', "%{$keyword}%");
        });
    }

    // Lấy danh sách, sắp xếp mới nhất và phân trang 10 bản ghi/trang
    $products = $query->orderBy('id', 'DESC')->paginate(10);

    // Trả về view kèm biến $products
    return view('admin.product.list', compact('products'));
}
public function edit(Request $request, $id)
{
    // 1. Tìm sản phẩm theo ID, nếu không thấy sẽ bắn lỗi 404
    $product = Product::findOrFail($id);

    // 2. Lấy danh sách loại sản phẩm và nhà cung cấp để đổ vào select box
    $categories = ProductCategory::all();
    $suppliers = Supplier::all();

    // 3. Trả về view edit kèm các dữ liệu cần thiết
    return view('admin.product.edit', compact('product', 'categories', 'suppliers'));
}
public function editStore(Request $request, $id)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate(
        [
            'internal_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'internal_code')->ignore($id),
            ],
            'short_name'         => 'required|string|max:255',
            'international_name' => 'required|string|max:255',
            'international_code' => 'required|string|max:50',
            'category_id'        => 'required|exists:product_categories,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'unit'               => 'required|string|max:50',
            'status'             => 'required|in:0,1',
        ],
        [
            'internal_code.required' => 'Vui lòng nhập mã nội bộ.',
            'internal_code.max'      => 'Mã nội bộ không được quá 50 ký tự.',
            'internal_code.unique'   => 'Mã nội bộ này đã tồn tại trong hệ thống.',

            'short_name.required'    => 'Vui lòng nhập tên dễ nhớ.',
            'short_name.max'         => 'Tên dễ nhớ không được vượt quá 255 ký tự.',

            'international_name.required' => 'Vui lòng nhập tên quốc tế.',
            'international_name.max'      => 'Tên quốc tế không được vượt quá 255 ký tự.',

            'international_code.required' => 'Vui lòng nhập mã quốc tế.',
            'international_code.max'      => 'Mã quốc tế không được quá 50 ký tự.',

            'category_id.required'   => 'Vui lòng chọn loại sản phẩm.',
            'category_id.exists'     => 'Loại sản phẩm chọn không hợp lệ.',

            'supplier_id.exists'     => 'Nhà cung cấp chọn không hợp lệ.',

            'unit.required'          => 'Vui lòng nhập đơn vị tính.',
            'unit.max'               => 'Đơn vị tính không được quá 50 ký tự.',

            'status.required'        => 'Vui lòng chọn trạng thái.',
            'status.in'              => 'Trạng thái không hợp lệ.',
        ]
    );

    // 2. Tìm sản phẩm và Cập nhật dữ liệu vào CSDL
    $product = Product::findOrFail($id);
    $product->update([
        'internal_code'      => $request->input('internal_code'),
        'short_name'         => $request->input('short_name'),
        'international_name' => $request->input('international_name'),
        'international_code' => $request->input('international_code'),
        'category_id'        => $request->input('category_id'),
        'supplier_id'        => $request->input('supplier_id'),
        'unit'               => $request->input('unit'),
        'status'             => $request->input('status'),
    ]);

    // 3. Chuyển hướng về trang danh sách kèm thông báo thành công
    return redirect('admin/product/list')->with('success', 'Cập nhật sản phẩm thành công!');
}
public function delete($id)
{
    // 1. Tìm sản phẩm theo ID, nếu không thấy sẽ tự động bắn lỗi 404
    $product = Product::findOrFail($id);

    // 2. Thực hiện xóa khỏi Database
    $product->delete();

    // 3. Quay lại trang trước (hoặc trang danh sách) kèm thông báo thành công
    return redirect()->back()->with('success', 'Xóa sản phẩm thành công!');
}
}
