@extends('layouts.admin')
@section('content')
    <div id="content" class="container-fluid">
        <!-- Hiển thị thông báo thành công từ Controller -->
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
                <h5 class="m-0 font-weight-bold text-primary">Danh sách bộ công thức tính giá</h5>
                <a href="{{ url('admin/pricingRule/add') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <!-- Form tìm kiếm -->
                <form action="{{ url('admin/pricingRule/list') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control"
                                    placeholder="Tìm theo tên công thức..." value="{{ request('keyword') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Bảng danh sách -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 50px;">STT</th>
                                <th scope="col">Tên bộ công thức</th>
                                <th scope="col">Số lượng quy tắc con</th>
                                <th scope="col" style="width: 140px;">Trạng thái</th>
                                <th scope="col" style="width: 120px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($pricingRules->count() > 0)
                                @foreach ($pricingRules as $index => $rule)
                                    <tr>
                                        <td>{{ $pricingRules->firstItem() + $index }}</td>
                                        <td><strong>{{ $rule->name }}</strong></td>
                                        <td>
                                            <span class="badge badge-info p-2">
                                                {{ $rule->details_count ?? $rule->details->count() }} quy tắc
                                            </span>
                                        </td>
                                        <td>
                                            @if ($rule->status)
                                                <span class="badge badge-success">Kích hoạt</span>
                                            @else
                                                <span class="badge badge-secondary">Ngưng sử dụng</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.pricingRule.edit', ['id' => $rule->id]) }}"
                                                class="btn btn-success btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pricingRule.delete', ['id' => $rule->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bộ công thức này?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không tìm thấy dữ liệu nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-end">
                    {{ $pricingRules->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection