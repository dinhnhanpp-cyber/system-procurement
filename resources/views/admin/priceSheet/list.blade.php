@extends('layouts.admin')

@section('content')
<div id="content" class="container-fluid px-4 py-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="font-weight-bold text-uppercase mb-0 text-primary">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Danh Sách Bảng Tính Giá
            </h5>
            <a href="{{ route('admin.priceSheet.allDetails') }}" class="btn btn-sm btn-outline-primary mr-2 px-3">
            <i class="fas fa-list-alt mr-1"></i> Xem tất cả chi tiết
        </a>
            <a href="{{ url('admin/priceSheet/add') }}" class="btn btn-sm btn-primary px-3">
                <i class="fas fa-plus mr-1"></i> Tạo phiếu mới
            </a>
        </div>

        <div class="card-body">
            <!-- Bộ lọc & Tìm kiếm -->
            <form action="{{ url('admin/priceSheet/list') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                   name="keyword" 
                                   class="form-control" 
                                   placeholder="Tìm tên phiếu, nhà cung cấp..." 
                                   value="{{ request('keyword') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Bảng dữ liệu -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 60px;">STT</th>
                            <th>Tên Bảng Giá / Phiếu</th>
                            <th>Nhà Cung Cấp</th>
                            <th style="width: 130px;">Số Sản Phẩm</th>
                            <th style="width: 130px;">Ngày Tạo</th>
                            <th style="width: 150px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($priceSheets as $index => $sheet)
                            <tr>
                                <td class="text-center font-weight-bold">
                                    {{ $priceSheets->firstItem() + $index }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.priceSheet.detail', $sheet->id) }}" class="font-weight-bold text-dark">
                                        {{ $sheet->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1">
                                        {{ $sheet->supplier->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-secondary">
                                        {{ $sheet->items_count ?? 0 }} sản phẩm
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($sheet->sheet_date)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.priceSheet.detail', $sheet->id) }}" 
                                       class="btn btn-sm btn-outline-info border-0" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.priceSheet.edit', $sheet->id) }}" 
                                       class="btn btn-sm btn-outline-warning border-0" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.priceSheet.delete', $sheet->id) }}" 
                                          method="POST" 
                                          class="d-inline-block" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa bảng giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger border-0" 
                                                title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có bảng tính giá nào được tạo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang -->
        @if ($priceSheets->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end py-2">
                {{ $priceSheets->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection