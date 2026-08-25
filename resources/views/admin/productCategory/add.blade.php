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
            Thêm loại sản phẩm
        </div>
        <div class="card-body">
            <form action="{{ route('admin.productCategory.addStore') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Tên loại sản phẩm -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Tên loại sản phẩm <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nhập tên loại sản phẩm">
                            @error('name')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Mã loại sản phẩm -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Mã loại sản phẩm <span class="text-danger">*</span></label>
                            <input class="form-control @error('code') is-invalid @enderror" type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Ví dụ: CAT_01">
                            @error('code')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="4" placeholder="Nhập mô tả loại sản phẩm...">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="text-danger font-italic">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Trạng thái -->
                <div class="form-group">
                    <label class="d-block">Trạng thái</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_active">
                            Đang sử dụng
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status') === '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_inactive">
                            Ngưng sử dụng
                        </label>
                    </div>
                    @error('status')
                        <div class="d-block">
                            <small class="text-danger font-italic">{{ $message }}</small>
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
            </form>
        </div>
    </div>
</div>
@endsection