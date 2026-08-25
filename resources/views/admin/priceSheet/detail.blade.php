@extends('layouts.admin')

@section('content')
<div id="content" class="container-fluid px-4 py-3">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold text-uppercase mb-0 text-primary">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Chi Tiết Bảng Tính Giá
        </h4>
        <div>
            <a href="{{ route('admin.priceSheet.edit', $priceSheet->id) }}" class="btn btn-sm btn-warning px-3">
                <i class="fas fa-edit mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ url('admin/priceSheet/list') }}" class="btn btn-sm btn-secondary px-3 ml-1">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Thông tin chung -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header font-weight-bold bg-white">
            <i class="fas fa-info-circle text-primary mr-1"></i> Thông Tin Chung
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <label class="font-weight-bold text-muted mb-1">Tên phiếu / bảng giá:</label>
                    <p class="font-weight-bold text-dark h6 mb-0">{{ $priceSheet->name }}</p>
                </div>
                <div class="col-md-4">
                    <label class="font-weight-bold text-muted mb-1">Nhà cung cấp:</label>
                    <p class="mb-0">
                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.9rem;">
                            {{ $priceSheet->supplier->name ?? 'N/A' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold text-muted mb-1">Ngày tạo phiếu:</label>
                    <p class="font-weight-bold text-dark mb-0">
                        {{ \Carbon\Carbon::parse($priceSheet->sheet_date)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="font-weight-bold text-uppercase mb-0 style-title">
                Danh sách sản phẩm tính giá
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle" style="min-width: 1480px;">
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($priceSheet->items as $item)
                            <tr>
                                <!-- Sản phẩm & Giá đối thủ -->
                                <td class="align-middle">
                                    <div class="font-weight-bold text-primary mb-1">
                                        {{ $item->product->short_name ?? ($item->product->name ?? 'N/A') }}
                                    </div>
                                    <div class="small text-muted mb-1">
                                        Giá đối thủ: <b class="text-dark">$ {{ number_format($item->competitor_price ?? 0, 2) }}</b>
                                    </div>
                                    <div class="bg-light p-1 border rounded d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                                        <span class="text-muted">Sau CK:</span>
                                        <b class="text-primary">$ {{ number_format($item->competitor_discounted_price ?? 0, 2) }}</b>
                                    </div>
                                </td>

                                <!-- TTL -->
                                <td class="text-center align-middle font-weight-bold">
                                    {{ number_format($item->ttl ?? 0, 2) }}
                                </td>

                                <!-- FOB -->
                                <td class="text-right align-middle">
                                    $ {{ number_format($item->fob ?? 0, 2) }}
                                </td>

                                <!-- Logistics -->
                                <td class="text-right align-middle">
                                    $ {{ number_format($item->logistics ?? 0, 2) }}
                                </td>

                                <!-- Cấu hình % -->
                                <td class="align-middle px-2" style="font-size: 0.82rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">NK%:</span>
                                        <b>{{ number_format($item->import_tax ?? 0, 1) }}%</b>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">VAT%:</span>
                                        <b>{{ number_format($item->vat ?? 0, 1) }}%</b>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Serv%:</span>
                                        <b>{{ number_format($item->service_percent ?? 0, 1) }}%</b>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Kho%:</span>
                                        <b>{{ number_format($item->warehouse_percent ?? 0, 1) }}%</b>
                                    </div>
                                </td>

                                <!-- Phí cố định -->
                                <td class="align-middle px-2" style="font-size: 0.82rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">LCC:</span>
                                        <b>$ {{ number_format($item->lcc ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">V.Hành:</span>
                                        <b>$ {{ number_format($item->operation ?? 0, 2) }}</b>
                                    </div>
                                </td>

                                <!-- Chi tiết chi phí tính toán -->
                                <td class="small align-middle px-2" style="font-size: 0.8rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>G.Tiền:</span>
                                        <b class="text-dark">$ {{ number_format(($item->fob ?? 0) * ($item->ttl ?? 0), 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Thuế:</span>
                                        <b class="text-dark">$ {{ number_format($item->tax_amount ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Serv:</span>
                                        <b class="text-dark">$ {{ number_format($item->service_amount ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Kho:</span>
                                        <b class="text-dark">$ {{ number_format($item->warehouse_amount ?? 0, 2) }}</b>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 border-top">
                                        <span class="font-weight-bold">T.Tiền:</span>
                                        <b class="text-dark">$ {{ number_format($item->total_amount ?? 0, 2) }}</b>
                                    </div>
                                </td>

                                <!-- Giá vốn / Tấn -->
                                <td class="text-right font-weight-bold text-danger bg-light align-middle px-2" style="font-size: 1.05rem;">
                                    $ {{ number_format($item->cost_per_ton ?? 0, 2) }}
                                </td>

                                <!-- Kịch bản Bán & Lợi Nhuận -->
                                <td class="align-middle p-1 bg-light">
                                    <table class="table table-sm table-bordered m-0 bg-white shadow-sm">
                                        <thead class="thead-dark text-center" style="font-size: 0.72rem;">
                                            <tr>
                                                <th>Cấu hình %</th>
                                                <th>Giá Bán</th>
                                                <th>Lợi Nhuận</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 0.8rem;">
                                            @forelse ($item->results as $result)
                                                <tr>
                                                    <td class="text-center align-middle font-weight-bold p-1">
                                                        {{ number_format($result->margin_percent ?? 0, 1) }}%
                                                    </td>
                                                    <td class="text-right align-middle font-weight-bold text-dark p-1">
                                                        $ {{ number_format($result->selling_price ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-right align-middle font-weight-bold text-success p-1">
                                                        $ {{ number_format($result->profit ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted p-1">Không có dữ liệu</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Không có sản phẩm nào trong phiếu tính giá này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection