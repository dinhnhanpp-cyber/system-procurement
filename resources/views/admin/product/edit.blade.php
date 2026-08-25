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
            Cập nhật sản phẩm
        </div>
        <div class="card-body">
            <form action="{{ route('admin.product.editStore', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Mã nội bộ -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="internal_code">Mã nội bộ <span class="text-danger">*</span></label>
                            <input class="form-control @error('internal_code') is-invalid @enderror" type="text" name="internal_code" id="internal_code" value="{{ old('internal_code', $product->internal_code) }}" placeholder="Ví dụ: URE-001">
                            @error('internal_code')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Tên dễ nhớ -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="short_name">Tên dễ nhớ <span class="text-danger">*</span></label>
                            <input class="form-control @error('short_name') is-invalid @enderror" type="text" name="short_name" id="short_name" value="{{ old('short_name', $product->short_name) }}" placeholder="Ví dụ: Ure">
                            @error('short_name')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Tên quốc tế -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="international_name">Tên quốc tế<span class="text-danger">*</span></label>
                            <input class="form-control @error('international_name') is-invalid @enderror" type="text" name="international_name" id="international_name" value="{{ old('international_name', $product->international_name) }}" placeholder="Ví dụ: Urea">
                            @error('international_name')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Mã quốc tế -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="international_code">Mã quốc tế(HS CODE)<span class="text-danger">*</span></label>
                            <input class="form-control @error('international_code') is-invalid @enderror" type="text" name="international_code" id="international_code" value="{{ old('international_code', $product->international_code) }}" placeholder="Ví dụ: UREA">
                            @error('international_code')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Loại sản phẩm (Khóa ngoại) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                <option value="">-- Chọn loại sản phẩm --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Nhà cung cấp (Khóa ngoại) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id">Nhà cung cấp</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror" name="supplier_id" id="supplier_id">
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} ({{ $supplier->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Đơn vị tính -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit">Đơn vị tính (ĐVT) <span class="text-danger">*</span></label>
                            <input class="form-control @error('unit') is-invalid @enderror" type="text" name="unit" id="unit" value="{{ old('unit', $product->unit) }}" placeholder="Ví dụ: Tấn, Kg, Bao...">
                            @error('unit')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="d-block">Trạng thái</label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', $product->status) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_active">Hoạt động</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status', $product->status) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_inactive">Ngưng hoạt động</label>
                            </div>
                            @error('status')
                                <div class="d-block">
                                    <small class="text-danger font-italic">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ url('admin/product/list') }}" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>
@endsection