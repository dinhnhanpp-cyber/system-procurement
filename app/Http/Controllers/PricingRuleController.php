<?php

namespace App\Http\Controllers;

use App\Models\PricingRule;
use App\Models\PricingRuleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingRuleController extends Controller
{
    //
    function add(){
        return view('admin.pricingRule.add');
    }
   public function addStore(Request $request)
{
    // 1. Validate dữ liệu đầu vào (cả bảng mẹ và mảng chi tiết)
    $request->validate(
        [
            'name'             => 'required|string|max:255',
            'status'           => 'required|in:0,1',
            'details'          => 'required|array|min:1',
            'details.*.type'   => 'required|string|in:profit,discount',
            'details.*.name'   => 'required|string|max:255',
            'details.*.value'  => 'required|numeric|min:0',
        ],
        [
            'name.required'        => 'Vui lòng nhập tên bộ công thức.',
            'name.max'             => 'Tên bộ công thức không được vượt quá 255 ký tự.',
            'status.required'      => 'Vui lòng chọn trạng thái.',
            'status.in'            => 'Trạng thái không hợp lệ.',
            
            'details.required'     => 'Bộ quy tắc phải có ít nhất 1 dòng chi tiết.',
            'details.min'          => 'Bộ quy tắc phải có ít nhất 1 dòng chi tiết.',
            'details.*.type.required'  => 'Vui lòng chọn loại quy tắc.',
            'details.*.name.required'  => 'Vui lòng nhập tên hiển thị cho quy tắc.',
            'details.*.value.required' => 'Vui lòng nhập giá trị.',
            'details.*.value.numeric'  => 'Giá trị phải là số.',
        ]
    );

    // 2. Thực hiện lưu bằng Transaction để đảm bảo toàn vẹn dữ liệu
    DB::transaction(function () use ($request) {
        // Lưu bảng mẹ PricingRule
        $rule = PricingRule::create([
            'name'   => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        // Chuẩn bị dữ liệu mảng con PricingRuleDetail
        $detailsData = [];
        foreach ($request->input('details') as $detail) {
            $detailsData[] = [
                'rule_id'    => $rule->id,
                'type'       => $detail['type'],
                'name'       => $detail['name'],
                'value'      => $detail['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Lưu danh sách chi tiết vào database
        PricingRuleDetail::insert($detailsData);
    });

    // 3. Chuyển hướng kèm thông báo thành công
    return redirect('admin/pricingRule/list')->with('success', 'Thêm bộ công thức tính giá thành công!');
}
public function list(Request $request)
{
    // 1. Lấy từ khóa tìm kiếm từ request
    $keyword = $request->input('keyword');

    // 2. Query dữ liệu bộ công thức
    $pricingRules = PricingRule::withCount('details') // Tự động đếm số lượng dòng chi tiết con (tạo ra biến details_count)
        ->when($keyword, function ($query, $keyword) {
            // Lọc theo tên bộ công thức nếu có nhập keyword
            return $query->where('name', 'LIKE', "%{$keyword}%");
        })
        ->orderBy('created_at', 'desc') // Sắp xếp mới nhất lên đầu
        ->paginate(10); // Phân trang 10 bản ghi/trang

    // 3. Trả về view kèm dữ liệu
    return view('admin.pricingRule.list', compact('pricingRules'));
}
public function edit($id)
{
    // Lấy record cần chỉnh sửa kèm theo toàn bộ chi tiết con
    $rule = PricingRule::with('details')->findOrFail($id);

    return view('admin.pricingRule.edit', compact('rule'));
}

// 2. Xử lý Cập nhật dữ liệu
public function editStore(Request $request, $id)
{
    // Tìm record cha
    $rule = PricingRule::findOrFail($id);

    // Validate dữ liệu
    $request->validate(
        [
            'name'             => 'required|string|max:255',
            'status'           => 'required|in:0,1',
            'details'          => 'required|array|min:1',
            'details.*.type'   => 'required|string|in:profit,discount',
            'details.*.name'   => 'required|string|max:255',
            'details.*.value'  => 'required|numeric|min:0',
        ],
        [
            'name.required'            => 'Vui lòng nhập tên bộ công thức.',
            'name.max'                 => 'Tên bộ công thức không được vượt quá 255 ký tự.',
            'status.required'          => 'Vui lòng chọn trạng thái.',
            'status.in'                => 'Trạng thái không hợp lệ.',
            'details.required'         => 'Bộ quy tắc phải có ít nhất 1 dòng chi tiết.',
            'details.min'              => 'Bộ quy tắc phải có ít nhất 1 dòng chi tiết.',
            'details.*.type.required'  => 'Vui lòng chọn loại quy tắc.',
            'details.*.name.required'  => 'Vui lòng nhập tên hiển thị cho quy tắc.',
            'details.*.value.required' => 'Vui lòng nhập giá trị.',
            'details.*.value.numeric'  => 'Giá trị phải là số.',
        ]
    );

    try {
        DB::transaction(function () use ($request, $rule) {
            // Cập nhật thông tin bảng mẹ
            $rule->update([
                'name'   => $request->input('name'),
                'status' => $request->input('status'),
            ]);

            // Xóa toàn bộ chi tiết cũ
            $rule->details()->delete();

            // Thêm lại toàn bộ danh sách chi tiết mới
            foreach ($request->input('details') as $detail) {
                $rule->details()->create([
                    'type'  => $detail['type'],
                    'name'  => $detail['name'],
                    'value' => $detail['value'],
                ]);
            }
        });

        return redirect('admin/pricingRule/list')->with('success', 'Cập nhật bộ công thức tính giá thành công!');

    } catch (\Exception $e) {
        Log::error('Lỗi khi cập nhật PricingRule: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}
}
