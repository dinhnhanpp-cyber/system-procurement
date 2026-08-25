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
            Cập nhật nhà cung cấp
        </div>
        <div class="card-body">
            <form action="{{ route('admin.supplier.editStore', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Mã nhà cung cấp -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Mã nhà cung cấp <span class="text-danger">*</span></label>
                            <input class="form-control @error('code') is-invalid @enderror" type="text" name="code" id="code" value="{{ old('code', $supplier->code) }}" placeholder="Ví dụ: NCC1">
                            @error('code')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Tên nhà cung cấp -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Tên nhà cung cấp <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" placeholder="Nhập tên nhà cung cấp">
                            @error('name')
                                <small class="text-danger font-italic">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quốc gia -->
                <div class="form-group">
                    <label for="country">Quốc gia</label>
                    <input class="form-control @error('country') is-invalid @enderror" type="text" name="country" id="country" value="{{ old('country', $supplier->country) }}" placeholder="Nhập quốc gia (Ví dụ: China, Indonesia...)">
                    @error('country')
                        <small class="text-danger font-italic">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ url('admin/supplier/list') }}" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>
@endsection