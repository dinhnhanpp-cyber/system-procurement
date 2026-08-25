<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/solid.min.css">
    <link rel="stylesheet" href="{{ asset('admincss/style.css') }}">
    <title>Hệ Thống Quản Lý Thu Mua</title>
</head>

<body>
    <div id="warpper" class="nav-fixed">
        <nav class="topnav shadow navbar-light bg-white d-flex">
            <div class="navbar-brand"><a href="{{ url('/') }}">PROCUREMENT SYSTEM</a></div>
            <div class="nav-right">
                <div class="btn-group mr-auto">
                    <button type="button" class="btn dropdown" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="plus-icon fas fa-plus-circle"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Tạo yêu cầu mua hàng (PR)</a>
                        <a class="dropdown-item" href="#">Tạo đơn đặt hàng (PO)</a>
                        <a class="dropdown-item" href="#">Thêm nhà cung cấp</a>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Thông tin tài khoản</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <!-- end nav -->

        <div id="page-body" class="d-flex">
            <div id="sidebar" class="bg-white">
                <ul id="sidebar-menu">
                    <!-- Dashboard -->
                    <li class="nav-link active">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            Dashboard
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                    </li>
                    <!-- Quản lý loại sản phẩm-->
                    <li class="nav-link">
                        <a href="{{ url('admin/productCategory/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            Loại sản phẩm
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/productCategory/add') }}">Tạo loại sản phẩm</a></li>
                            <li><a href="{{ url('admin/productCategory/list') }}">Danh sách loại</a></li>
                        </ul>
                    </li>
                    <!-- Quản lý Nhà cung cấp -->
                    <li class="nav-link">
                        <a href="{{ url('admin/supplier/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            Nhà cung cấp(NCC)
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/supplier/add') }}">Tạo nhà cung cấp</a></li>
                            <li><a href="{{ url('admin/supplier/list') }}">Danh sách NCC</a></li>
                        </ul>
                    </li>
                    <!-- Quản lý sản phẩm -->
                    <li class="nav-link">
                        <a href="{{ url('admin/product/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            Sản phẩm
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/product/add') }}">Tạo sản phẩm</a></li>
                            <li><a href="{{ url('admin/product/list') }}">Danh sách sản phẩm</a></li>
                        </ul>
                    </li>
                    <li class="nav-link">
                        <a href="{{ url('admin/productCostSetting/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-calculator"></i>
                            </div>
                            Cấu hình chi phí
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/productCostSetting/add') }}">Thiết lập chi phí SP</a></li>
                            <li><a href="{{ url('admin/productCostSetting/list') }}">Danh sách cấu hình chi phí</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-link">
                        <a href="{{ url('admin/pricingRule/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-calculator"></i>
                            </div>
                            Công thức tính giá
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/pricingRule/add') }}">Thêm bộ công thức</a></li>
                            <li><a href="{{ url('admin/pricingRule/list') }}">Danh sách bộ công thức</a></li>
                        </ul>
                    </li>
                     <li class="nav-link">
                        <a href="{{ url('admin/priceSheet/list') }}">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-calculator"></i>
                            </div>
                           Tính giá
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="{{ url('admin/priceSheet/add') }}">Thêm SP tính giá</a></li>
                            <li><a href="{{ url('admin/priceSheet/list') }}">Danh SP tính giá</a></li>
                        </ul>
                    </li>
                    <!-- Đơn đặt hàng (PO) -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            Đơn đặt hàng (PO)
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Tạo đơn PO</a></li>
                            <li><a href="#">Danh sách đơn PO</a></li>
                            <li><a href="#">Theo dõi tiến độ</a></li>
                        </ul>
                    </li>

                    <!-- Quản lý Nhà cung cấp -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-truck"></i>
                            </div>
                            Nhà cung cấp (NCC)
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Thêm NCC mới</a></li>
                            <li><a href="#">Danh sách NCC</a></li>
                            <li><a href="#">Đánh giá NCC</a></li>
                        </ul>
                    </li>

                    <!-- Quản lý Vật tư / Hàng hóa -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-boxes"></i>
                            </div>
                            Danh mục Vật tư
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Thêm vật tư mới</a></li>
                            <li><a href="#">Danh sách vật tư</a></li>
                            <li><a href="#">Nhóm hàng hóa</a></li>
                        </ul>
                    </li>

                    <!-- Quản lý Kho & Nhập hàng -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            Kho & Nhập hàng
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Phiếu nhập kho (GRN)</a></li>
                            <li><a href="#">Kiểm định chất lượng</a></li>
                        </ul>
                    </li>

                    <!-- Báo cáo & Thống kê -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            Báo cáo & Chi phí
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Báo cáo chi tiêu thu mua</a></li>
                            <li><a href="#">Thống kê công nợ NCC</a></li>
                        </ul>
                    </li>

                    <!-- Phân quyền & Hệ thống -->
                    <li class="nav-link">
                        <a href="#">
                            <div class="nav-link-icon d-inline-flex">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            Hệ thống & Phân quyền
                        </a>
                        <i class="arrow fas fa-angle-right"></i>
                        <ul class="sub-menu">
                            <li><a href="#">Danh sách người dùng</a></li>
                            <li><a href="#">Phân quyền duyệt (Workflow)</a></li>
                            <li><a href="#">Danh sách vai trò</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div id="wp-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="{{ asset('adminjs/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
