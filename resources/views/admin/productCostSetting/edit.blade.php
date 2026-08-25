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
            Cập nhật cấu hình chi phí sản phẩm
        </div>
        <div class="card-body">
            <form action="{{ route('admin.productCostSetting.editStore', $setting->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Chọn sản phẩm -->
                <div class="form-group">
                    <label for="product_id">Sản phẩm <span class="text-danger">*</span></label>
                    <select class="form-control @error('product_id') is-invalid @enderror" name="product_id" id="product_id">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $setting->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->short_name }} (Mã NB: {{ $product->internal_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <small class="text-danger font-italic">{{ $message }}</small>
                    @enderror
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">1. Tỷ lệ % Chi phí</h6>
                <div class="row">
                    <!-- Thuế nhập khẩu -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="import_tax">Thuế NK (%)</label>
                            <input class="form-control @error('import_tax') is-invalid @enderror" type="number" step="0.01" name="import_tax" id="import_tax" value="{{ old('import_tax', $setting->import_tax) }}" placeholder="Ví dụ: 5.00">
                            @error('import_tax')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Thuế VAT -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vat">Thuế VAT (%)</label>
                            <input class="form-control @error('vat') is-invalid @enderror" type="number" step="0.01" name="vat" id="vat" value="{{ old('vat', $setting->vat) }}" placeholder="Ví dụ: 5.00">
                            @error('vat')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Service -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="service_percent">Service (%)</label>
                            <input class="form-control @error('service_percent') is-invalid @enderror" type="number" step="0.01" name="service_percent" id="service_percent" value="{{ old('service_percent', $setting->service_percent) }}" placeholder="Ví dụ: 3.00">
                            @error('service_percent')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Kho -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="warehouse_percent">Kho (%)</label>
                            <input class="form-control @error('warehouse_percent') is-invalid @enderror" type="number" step="0.01" name="warehouse_percent" id="warehouse_percent" value="{{ old('warehouse_percent', $setting->warehouse_percent) }}" placeholder="Ví dụ: 1.00">
                            @error('warehouse_percent')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary mb-3">2. Phí Local Charges ($)</h6>
                <div class="row">
                    <!-- THC -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="thc">THC ($) <span class="text-danger">*</span></label>
                            <input class="form-control cost-input @error('thc') is-invalid @enderror" type="number" step="0.01" name="thc" id="thc" value="{{ old('thc', $setting->thc) }}" placeholder="Ví dụ: 145.00">
                            @error('thc')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- D/O -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="do">D/O ($) <span class="text-danger">*</span></label>
                            <input class="form-control cost-input @error('do') is-invalid @enderror" type="number" step="0.01" name="do" id="do" value="{{ old('do', $setting->do) }}" placeholder="Ví dụ: 45.00">
                            @error('do')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- CIC -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cic">CIC ($) <span class="text-danger">*</span></label>
                            <input class="form-control cost-input @error('cic') is-invalid @enderror" type="number" step="0.01" name="cic" id="cic" value="{{ old('cic', $setting->cic) }}" placeholder="Ví dụ: 50.00">
                            @error('cic')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- CLEANING -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cleaning">CLEANING ($) <span class="text-danger">*</span></label>
                            <input class="form-control cost-input @error('cleaning') is-invalid @enderror" type="number" step="0.01" name="cleaning" id="cleaning" value="{{ old('cleaning', $setting->cleaning) }}" placeholder="Ví dụ: 10.00">
                            @error('cleaning')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Hiển thị LCC tự động tính (Readonly) -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group p-3 bg-light border rounded">
                            <label class="font-weight-bold text-success">LCC Tự động tính: (THC + D/O + CIC + CLEANING) * 1.08</label>
                            <input type="text" id="lcc_display" class="form-control font-weight-bold text-success" readonly value="$ 0.00">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ url('admin/productCostSetting/list') }}" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const costInputs = document.querySelectorAll('.cost-input');
        
        function calculateLCC() {
            let thc = parseFloat(document.getElementById('thc').value) || 0;
            let doVal = parseFloat(document.getElementById('do').value) || 0;
            let cic = parseFloat(document.getElementById('cic').value) || 0;
            let cleaning = parseFloat(document.getElementById('cleaning').value) || 0;

            let totalLCC = (thc + doVal + cic + cleaning) * 1.08;
            document.getElementById('lcc_display').value = '$ ' + totalLCC.toFixed(2);
        }

        costInputs.forEach(input => {
            input.addEventListener('input', calculateLCC);
        });

        calculateLCC(); // Chạy tính nhẩm 1 lần khi load trang để bind dữ liệu cũ
    });
</script>
@endsection