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
                <h5 class="m-0 font-weight-bold text-primary">Danh sách loại sản phẩm</h5>
                <a href="{{ url('admin/productCategory/add') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <!-- Form tìm kiếm -->
                <form action="{{ url('admin/productCategory/list') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control"
                                    placeholder="Tìm theo tên hoặc mã..." value="{{ request('keyword') }}">
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
                                <th scope="col">Mã loại</th>
                                <th scope="col">Tên loại sản phẩm</th>
                                <th scope="col">Mô tả</th>
                                <th scope="col" style="width: 140px;">Trạng thái</th>
                                <th scope="col" style="width: 120px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($categories->count() > 0)
                                @foreach ($categories as $index => $category)
                                    <tr>
                                        <td>{{ $categories->firstItem() + $index }}</td>
                                        <td><strong>{{ $category->code }}</strong></td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ Str::limit($category->description, 50) }}</td>
                                        <td>
                                            @if ($category->status)
                                                <span class="badge badge-success">Đang sử dụng</span>
                                            @else
                                                <span class="badge badge-secondary">Ngưng sử dụng</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.productCategory.edit', ['id' => $category->id]) }}"
                                                class="btn btn-success btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.productCategory.delete', ['id' => $category->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa loại sản phẩm này?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không tìm thấy dữ liệu nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-end">
                    {{ $categories->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
