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
            <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Cấu hình chi phí sản phẩm</h5>
                <a href="{{ url('admin/productCostSetting/add') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <!-- Form tìm kiếm -->
                <form action="{{ url('admin/productCostSetting/list') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control form-control-sm"
                                    placeholder="Tìm theo tên sản phẩm, mã nội bộ..." value="{{ request('keyword') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Bảng danh sách thu nhỏ cỡ chữ -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mb-0" style="font-size: 0.875rem;">
                        <thead class="thead-dark text-center text-nowrap">
                            <tr>
                                <th scope="col" style="width: 40px;">STT</th>
                                <th scope="col">Mã nội bộ</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Thuế NK</th>
                                <th scope="col">VAT</th>
                                <th scope="col">THC ($)</th>
                                <th scope="col">D/O ($)</th>
                                <th scope="col">CIC ($)</th>
                                <th scope="col">CLEANING ($)</th>
                                <th scope="col" class="text-success">Tổng LCC ($)</th>
                                <th scope="col" style="width: 80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($settings->count() > 0)
                                @foreach ($settings as $index => $item)
                                    <tr>
                                        <td class="text-center align-middle">{{ $settings->firstItem() + $index }}</td>
                                        <td class="text-center align-middle"><strong>{{ $item->product->internal_code ?? 'N/A' }}</strong></td>
                                        <td class="align-middle text-nowrap">{{ $item->product->short_name ?? 'N/A' }}</td>
                                        <td class="text-center align-middle">{{ number_format($item->import_tax, 2) }}%</td>
                                        <td class="text-center align-middle">{{ number_format($item->vat, 2) }}%</td>
                                        <td class="text-right align-middle">{{ $item->formatted_thc }}</td>
                                        <td class="text-right align-middle">{{ $item->formatted_do }}</td>
                                        <td class="text-right align-middle">{{ $item->formatted_cic }}</td>
                                        <td class="text-right align-middle">{{ $item->formatted_cleaning }}</td>
                                        <td class="text-right align-middle font-weight-bold text-success">{{ $item->formatted_lcc }}</td>
                                        <td class="text-center align-middle text-nowrap">
                                            <a href="{{ route('admin.productCostSetting.edit', ['id' => $item->id]) }}"
                                                class="btn btn-success btn-sm px-2 py-1" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.productCostSetting.delete', ['id' => $item->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa cấu hình chi phí này?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted align-middle py-3">Không tìm thấy dữ liệu nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $settings->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection