@extends('layouts.admin')

@section('content')
<div id="content" class="container-fluid px-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.priceSheet.addStore') }}" method="POST" id="price-sheet-form">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header font-weight-bold bg-white">
                <i class="fas fa-file-invoice-dollar text-primary mr-1"></i>
                Tạo Bảng Tính Giá Mới (Price Sheet)
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label for="name" class="font-weight-bold">
                                Tên phiếu / bảng giá <span class="text-danger">*</span>
                            </label>
                            <input class="form-control @error('name') is-invalid @enderror"
                                   type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', 'Bảng giá NCC - ' . date('d/m/Y')) }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="supplier_id" class="font-weight-bold">
                                Nhà cung cấp <span class="text-danger">*</span>
                            </label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required>
                                <option value="">-- Chọn Nhà Cung Cấp --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="sheet_date" class="font-weight-bold">
                                Ngày tạo phiếu <span class="text-danger">*</span>
                            </label>
                            <input class="form-control"
                                   type="date"
                                   name="sheet_date"
                                   id="sheet_date"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="font-weight-bold text-uppercase mb-0 style-title">
                    Danh sách sản phẩm tính giá
                </h5>
                <button type="button" class="btn btn-sm btn-success px-3" id="btn-add-item">
                    <i class="fas fa-plus mr-1"></i> Thêm sản phẩm
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle"
                           id="price-sheet-items-table"
                           style="min-width: 1480px;">
                        <thead class="thead-light text-center">
                            <tr class="align-middle">
                                <th style="width: 240px;">Sản phẩm & Đối thủ</th>
                                <th style="width: 85px;">TTL</th>
                                <th style="width: 100px;">FOB ($)</th>
                                <th style="width: 100px;">Logistics</th>
                                <th style="width: 150px;">Cấu hình %</th>
                                <th style="width: 140px;">Phí cố định</th>
                                <th style="width: 160px;">Chi tiết chi phí</th>
                                <th style="width: 130px;" class="bg-warning text-dark">Giá vốn/Tấn</th>
                                <th style="width: 320px;" class="bg-info text-white">
                                    Kịch bản Bán & Lợi Nhuận (Results)
                                </th>
                                <th style="width: 50px;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save mr-1"></i> Lưu phiếu tính giá
                </button>
                <a href="{{ url('admin/priceSheet/list') }}" class="btn btn-secondary ml-2 px-3">
                    Hủy bỏ
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    window.priceSheetConfig = {
        products: @json($products ?? []),
        pricingRules: @json($pricingRules ?? [])
    };
</script>

<script src="{{ asset('adminjs/price-sheet-add.js') }}"></script>
@endsection