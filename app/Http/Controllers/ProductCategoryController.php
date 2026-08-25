<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ProductCategoryController extends Controller
{
    //
    function add(){
       return view('admin.productCategory.add');
    }
   public function addStore(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate(
        [
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:product_categories,code',
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1',
        ],
        [
            'name.required' => 'Vui lòng nhập tên loại sản phẩm.',
            'name.max'      => 'Tên loại sản phẩm không được vượt quá 255 ký tự.',
            
            'code.required' => 'Vui lòng nhập mã loại sản phẩm.',
            'code.max'      => 'Mã loại sản phẩm không được quá 50 ký tự.',
            'code.unique'   => 'Mã loại sản phẩm này đã tồn tại trong hệ thống.',
            
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in'       => 'Trạng thái không hợp lệ.',
        ]
    );

    // 2. Lưu vào Database (Ví dụ dùng Model ProductCategory)
    ProductCategory::create($request->all());
    return redirect('admin/productCategory/list')->with('success', 'Thêm loại sản phẩm thành công!');
}
   public function list(Request $request)
{
    // Tạo truy vấn ban đầu
    $query = ProductCategory::query();

    // Lọc theo từ khóa (Tên hoặc Mã loại)
    if ($request->has('keyword') && !empty($request->keyword)) {
        $keyword = trim($request->keyword);
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('code', 'LIKE', "%{$keyword}%");
        });
    }

    // Lấy danh sách, sắp xếp mới nhất và phân trang 10 bản ghi/trang
    $categories = $query->orderBy('id', 'DESC')->paginate(10);

    // Trả về view kèm biến $categories
    return view('admin.productCategory.list', compact('categories'));
}
function edit(Request $request , $id){
    $productCategory = ProductCategory::find($id);
    return view('admin.productCategory.edit', compact('productCategory'));
}
public function editStore(Request $request, $id)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate(
        [
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_categories', 'code')->ignore($id),
            ],
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1',
        ],
        [
            'name.required' => 'Vui lòng nhập tên loại sản phẩm.',
            'name.max'      => 'Tên loại sản phẩm không được vượt quá 255 ký tự.',
            
            'code.required' => 'Vui lòng nhập mã loại sản phẩm.',
            'code.max'      => 'Mã loại sản phẩm không được quá 50 ký tự.',
            'code.unique'   => 'Mã loại sản phẩm này đã tồn tại trong hệ thống.',
            
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in'       => 'Trạng thái không hợp lệ.',
        ]
    );

    // 2. Cập nhật dữ liệu vào Database
    $productCategory = ProductCategory::findOrFail($id);
    $productCategory->update($request->all());

    // 3. Chuyển hướng về trang danh sách
    return redirect('admin/productCategory/list')->with('success', 'Cập nhật loại sản phẩm thành công!');
}
public function delete($id)
{
    // 1. Tìm loại sản phẩm theo ID, nếu không thấy sẽ tự động bắn lỗi 404
    $category = ProductCategory::findOrFail($id);

    // 2. Thực hiện xóa khỏi Database
    $category->delete();

    // 3. Quay lại trang trước và gửi kèm thông báo thành công
    return redirect()->back()->with('success', 'Xóa loại sản phẩm thành công!');
}
}
