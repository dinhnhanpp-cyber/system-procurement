@extends('layouts.admin')
@section('content')
<div id="content" class="container-fluid">
    <!-- Hiển thị thông báo lỗi hệ thống/controller (nếu có) -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header font-weight-bold">
            Cập nhật bộ công thức tính giá
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricingRule.editStore', ['id' => $rule->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Tên bộ quy tắc -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Tên bộ công thức <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $rule->name) }}" placeholder="Ví dụ: Công thức giá bán 2026">
                            @error('name')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-block">Trạng thái</label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', (string)$rule->status) === '1' || old('status', $rule->status) === true ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_active">Kích hoạt</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status', (string)$rule->status) === '0' || old('status', $rule->status) === false ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_inactive">Ngưng sử dụng</label>
                            </div>
                            @error('status')
                                <div class="d-block">
                                    <small class="text-danger font-italic">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Bảng Chi tiết quy tắc (pricing_rule_details) -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold mb-0">Chi tiết quy tắc giá</h5>
                    <button type="button" class="btn btn-sm btn-success" id="btn-add-row">
                        <i class="fas fa-plus"></i> Thêm dòng
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="rule-details-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 25%;">Loại quy tắc (Type)</th>
                                <th style="width: 45%;">Tên hiển thị</th>
                                <th style="width: 20%;">Giá trị (%)</th>
                                <th style="width: 10%;" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Ưu tiên dữ liệu old() nếu submit lỗi, ngược lại lấy từ mối quan hệ details trong DB
                                $details = old('details', $rule->details);
                            @endphp

                            @foreach ($details as $index => $detail)
                                @php
                                    $type = is_array($detail) ? ($detail['type'] ?? 'profit') : $detail->type;
                                    $name = is_array($detail) ? ($detail['name'] ?? '') : $detail->name;
                                    $value = is_array($detail) ? ($detail['value'] ?? '') : $detail->value;
                                @endphp
                                <tr>
                                    <td>
                                        <select name="details[{{ $index }}][type]" class="form-control @error("details.{$index}.type") is-invalid @enderror">
                                            <option value="profit" {{ $type == 'profit' ? 'selected' : '' }}>Profit (Lợi nhuận)</option>
                                            <option value="discount" {{ $type == 'discount' ? 'selected' : '' }}>Discount (Chiết khấu)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{ $index }}][name]" class="form-control @error("details.{$index}.name") is-invalid @enderror" value="{{ $name }}" placeholder="Tên quy tắc" required>
                                        @error("details.{$index}.name")
                                            <small class="text-danger font-italic">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="details[{{ $index }}][value]" class="form-control @error("details.{$index}.value") is-invalid @enderror" value="{{ $value }}" placeholder="Giá trị" required>
                                        @error("details.{$index}.value")
                                            <small class="text-danger font-italic">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Cập nhật bộ công thức</button>
                    <a href="{{ url('admin/pricingRule/list') }}" class="btn btn-secondary ml-2">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script thêm/xóa dòng chi tiết động -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#rule-details-table tbody');
        const btnAddRow = document.getElementById('btn-add-row');
        
        // Khởi tạo index dựa trên số lượng dòng hiện có
        let rowIndex = tableBody.querySelectorAll('tr').length;

        btnAddRow.addEventListener('click', function () {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="details[${rowIndex}][type]" class="form-control">
                        <option value="profit">Profit (Lợi nhuận)</option>
                        <option value="discount">Discount (Chiết khấu)</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="details[${rowIndex}][name]" class="form-control" placeholder="Tên quy tắc" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="details[${rowIndex}][value]" class="form-control" placeholder="Giá trị" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tableBody.appendChild(newRow);
            rowIndex++;
        });

        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-row')) {
                const rows = tableBody.querySelectorAll('tr');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Bộ quy tắc phải có ít nhất 1 dòng chi tiết!');
                }
            }
        });
    });
</script>
@endsection