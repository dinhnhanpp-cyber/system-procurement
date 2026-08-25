@extends('layouts.admin')
@section('content')
    <div class="container-fluid py-5">
    <div class="row">
        <div class="col">
            <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                <div class="card-header">ĐƠN PO HOÀN THÀNH</div>
                <div class="card-body">
                    <h5 class="card-title">1.240</h5>
                    <p class="card-text">Lô nguyên liệu đã kiểm định & nhập kho</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white bg-danger mb-3" style="max-width: 18rem;">
                <div class="card-header">PR NGUYÊN LIỆU CHỜ DUYỆT</div>
                <div class="card-body">
                    <h5 class="card-title">8</h5>
                    <p class="card-text">Yêu cầu nhập nguyên liệu đang xử lý</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-white bg-success mb-3" style="max-width: 18rem;">
                <div class="card-header">TỔNG NGÂN SÁCH THU MUA</div>
                <div class="card-body">
                    <h5 class="card-title">15.8 tỷ</h5>
                    <p class="card-text">Chi phí nhập nguyên liệu phân bón</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white bg-dark mb-3" style="max-width: 18rem;">
                <div class="card-header">LÔ HÀNG TỪ CHỐI</div>
                <div class="card-body">
                    <h5 class="card-title">12</h5>
                    <p class="card-text">Lô nguyên liệu không đạt QC / Hủy đơn</p>
                </div>
            </div>
        </div>
    </div>
    <!-- end analytic  -->
    <div class="card">
        <div class="card-header font-weight-bold">
            DANH SÁCH ĐƠN MUA NGUYÊN LIỆU PHÂN BÓN MỚI NHẤT
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Mã đơn PO</th>
                        <th scope="col">Nhà cung cấp</th>
                        <th scope="col">Tên nguyên liệu</th>
                        <th scope="col">Khối lượng (Tấn)</th>
                        <th scope="col">Tổng giá trị</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Thời gian tạo</th>
                        <th scope="col">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>PO-FB-0121</td>
                        <td>
                            Tập đoàn Hóa chất Vinachem <br>
                            0988859692
                        </td>
                        <td><a href="#">Phân Urê Hạt Đục (Đạm 46% N)</a></td>
                        <td>500</td>
                        <td>5.250.000.000₫</td>
                        <td><span class="badge badge-warning">Kiểm định QC</span></td>
                        <td>17/08/2026 09:30</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>PO-FB-0122</td>
                        <td>
                            Công ty Cổ phần Phân bón Bình Điền <br>
                            0868873382
                        </td>
                        <td><a href="#">Phân Kali Clorua Red Potassium (60% K2O)</a></td>
                        <td>200</td>
                        <td>2.800.000.000₫</td>
                        <td><span class="badge badge-primary">Đang vận chuyển</span></td>
                        <td>17/08/2026 10:15</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>PO-FB-0123</td>
                        <td>
                            Công ty Xuất Nhập Khẩu Hóa Chất Đốc Hóa <br>
                            0234343545
                        </td>
                        <td><a href="#">Phân DAP Đình Vũ (18-46-0)</a></td>
                        <td>300</td>
                        <td>4.950.000.000₫</td>
                        <td><span class="badge badge-success">Đã nhập kho</span></td>
                        <td>16/08/2026 14:00</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>PO-FB-0124</td>
                        <td>
                            Công ty TNHH Khoáng Sản & Chemical Nam Bộ <br>
                            091236768
                        </td>
                        <td><a href="#">Super Lân Long Thành (16% P2O5)</a></td>
                        <td>1.000</td>
                        <td>2.400.000.000₫</td>
                        <td><span class="badge badge-warning">Chờ duyệt PR</span></td>
                        <td>16/08/2026 16:45</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>PO-FB-0125</td>
                        <td>
                            Công ty Hóa Chất & Vi Lượng Agritech <br>
                            0903123456
                        </td>
                        <td><a href="#">Kẽm Sulphate Monohydrate (ZnSO4.H2O 35%)</a></td>
                        <td>50</td>
                        <td>400.000.000₫</td>
                        <td><span class="badge badge-success">Đã hoàn thành</span></td>
                        <td>15/08/2026 11:20</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm rounded-0 text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">Trước</span>
                            <span class="sr-only">Trước</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Sau</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>
@endsection