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
                <h5 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h5>
                <a href="{{ url('admin/product/add') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <!-- Form tìm kiếm -->
                <form action="{{ url('admin/product/list') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control"
                                    placeholder="Tìm theo tên, mã nội bộ, mã quốc tế..." value="{{ request('keyword') }}">
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
                                <th scope="col">Mã nội bộ</th>
                                <th scope="col">Tên dễ nhớ</th>
                                <th scope="col">Tên quốc tế</th>
                                <th scope="col">Mã quốc tế</th>
                                <th scope="col">Nhóm SP</th>
                                <th scope="col">Nhà cung cấp</th>
                                <th scope="col" class="text-center">ĐVT</th>
                                <th scope="col" class="text-center">Trạng thái</th>
                                <th scope="col" style="width: 100px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($products->count() > 0)
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ $products->firstItem() + $index }}</td>
                                        <td><strong>{{ $product->internal_code }}</strong></td>
                                        <td>{{ $product->short_name }}</td>
                                        <td>{{ $product->international_name }}</td>
                                        <td>{{ $product->international_code }}</td>
                                        <td>
                                            <span class="badge badge-info p-2">
                                                {{ $product->category->name ?? '---' }}
                                            </span>
                                        </td>
                                        <td>{{ $product->supplier->name ?? '---' }}</td>
                                        <td class="text-center">{{ $product->unit }}</td>
                                        <td class="text-center">
                                            @if ($product->status)
                                                <span class="badge badge-success p-1">Hoạt động</span>
                                            @else
                                                <span class="badge badge-secondary p-1">Ngưng hoạt động</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                                                class="btn btn-success btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product.delete', ['id' => $product->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Không tìm thấy dữ liệu nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $products->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection