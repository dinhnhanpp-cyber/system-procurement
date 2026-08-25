<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class SupplierController extends Controller
{
    //
    function add(){
         return view('admin.supplier.add');
    }
   public function addStore(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'code'    => 'required|string|max:50|unique:suppliers,code',
        'name'    => 'required|string|max:255',
        'country' => 'nullable|string|max:100',
    ], [
        'code.required' => 'Vui lòng nhập mã nhà cung cấp.',
        'code.unique'   => 'Mã nhà cung cấp này đã tồn tại.',
        'code.max'      => 'Mã nhà cung cấp không được quá 50 ký tự.',
        'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
        'name.max'      => 'Tên nhà cung cấp không được quá 255 ký tự.',
        'country.max'   => 'Tên quốc gia không được quá 100 ký tự.',
    ]);

    // 2. Lưu vào CSDL
    Supplier::create([
        'code'    => $request->input('code'),
        'name'    => $request->input('name'),
        'country' => $request->input('country'),
    ]);

    // 3. Chuyển hướng đến trang danh sách kèm thông báo thành công
    return redirect('admin/supplier/list')->with('success', 'Thêm nhà cung cấp thành công!');
}
public function list(Request $request)
{
    // Tạo truy vấn ban đầu
    $query = Supplier::query();

    // Lọc theo từ khóa (Tên, Mã nhà cung cấp hoặc Quốc gia)
    if ($request->has('keyword') && !empty($request->keyword)) {
        $keyword = trim($request->keyword);
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('code', 'LIKE', "%{$keyword}%")
              ->orWhere('country', 'LIKE', "%{$keyword}%");
        });
    }

    // Lấy danh sách, sắp xếp mới nhất và phân trang 10 bản ghi/trang
    $suppliers = $query->orderBy('id', 'DESC')->paginate(10);

    // Trả về view kèm biến $suppliers
    return view('admin.supplier.list', compact('suppliers'));
}
public function edit(Request $request, $id)
{
    $supplier = Supplier::findOrFail($id);
    return view('admin.supplier.edit', compact('supplier'));
}
public function editStore(Request $request, $id)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate(
        [
            'name'    => 'required|string|max:255',
            'code'    => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'code')->ignore($id),
            ],
            'country' => 'nullable|string|max:100',
        ],
        [
            'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'name.max'      => 'Tên nhà cung cấp không được vượt quá 255 ký tự.',
            
            'code.required' => 'Vui lòng nhập mã nhà cung cấp.',
            'code.max'      => 'Mã nhà cung cấp không được quá 50 ký tự.',
            'code.unique'   => 'Mã nhà cung cấp này đã tồn tại trong hệ thống.',
            
            'country.max'   => 'Tên quốc gia không được vượt quá 100 ký tự.',
        ]
    );

    // 2. Cập nhật dữ liệu vào Database
    $supplier = Supplier::findOrFail($id);
    $supplier->update([
        'code'    => $request->input('code'),
        'name'    => $request->input('name'),
        'country' => $request->input('country'),
    ]);

    // 3. Chuyển hướng về trang danh sách
    return redirect('admin/supplier/list')->with('success', 'Cập nhật nhà cung cấp thành công!');
}
public function delete($id)
{
    // 1. Tìm nhà cung cấp theo ID, nếu không thấy sẽ tự động bắn lỗi 404
    $supplier = Supplier::findOrFail($id);

    // 2. Thực hiện xóa khỏi Database
    $supplier->delete();

    // 3. Quay lại trang trước và gửi kèm thông báo thành công
    return redirect()->back()->with('success', 'Xóa nhà cung cấp thành công!');
}
}
