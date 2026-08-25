@extends('layouts.admin')
@section('content')
<div id="content" class="container-fluid">
    <!-- Hiển thị thông báo thành công -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header font-weight-bold">
            Thêm bộ công thức tính giá
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricingRule.addStore') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Tên bộ quy tắc -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Tên bộ công thức <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Ví dụ: Công thức giá bán 2026">
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
                                <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_active">Kích hoạt</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status') === '0' ? 'checked' : '' }}>
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
                            <!-- Dòng mặc định -->
                            <tr>
                                <td>
                                    <select name="details[0][type]" class="form-control">
                                        <option value="profit">Profit (Lợi nhuận)</option>
                                        <option value="discount">Discount (Chiết khấu)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="details[0][name]" class="form-control" placeholder="Ví dụ: Giá bán 5%" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="details[0][value]" class="form-control" placeholder="5" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Thêm mới bộ công thức</button>
                    <a href="{{ url('admin/pricingRule/list') }}" class="btn btn-secondary ml-2">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script thêm/xóa dòng chi tiết động -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIndex = 1;
        const tableBody = document.querySelector('#rule-details-table tbody');
        const btnAddRow = document.getElementById('btn-add-row');

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