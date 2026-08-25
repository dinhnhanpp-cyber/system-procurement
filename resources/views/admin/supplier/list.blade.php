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
                <h5 class="m-0 font-weight-bold text-primary">Danh sách nhà cung cấp</h5>
                <a href="{{ url('admin/supplier/add') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <!-- Form tìm kiếm -->
                <form action="{{ url('admin/supplier/list') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control"
                                    placeholder="Tìm theo tên, mã hoặc quốc gia..." value="{{ request('keyword') }}">
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
                                <th scope="col">Mã NCC</th>
                                <th scope="col">Tên nhà cung cấp</th>
                                <th scope="col">Quốc gia</th>
                                <th scope="col" style="width: 120px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($suppliers->count() > 0)
                                @foreach ($suppliers as $index => $supplier)
                                    <tr>
                                        <td>{{ $suppliers->firstItem() + $index }}</td>
                                        <td><strong>{{ $supplier->code }}</strong></td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->country ?? '---' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.supplier.edit', ['id' => $supplier->id]) }}"
                                                class="btn btn-success btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.supplier.delete', ['id' => $supplier->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?')"
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
                    {{ $suppliers->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection