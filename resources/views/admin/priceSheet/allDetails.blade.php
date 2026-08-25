@extends('layouts.admin')

@section('content')
<div id="content" class="container-fluid px-4 py-3">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="font-weight-bold text-uppercase mb-0 text-primary">
                <i class="fas fa-table mr-2"></i>Tất Cả Chi Tiết Sản Phẩm Bảng Giá
            </h5>
            <div>
                <a href="{{ url('admin/priceSheet/list') }}" class="btn btn-sm btn-secondary px-3 mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách phiếu
                </a>
                <a href="{{ url('admin/priceSheet/add') }}" class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-plus mr-1"></i> Tạo phiếu mới
                </a>
                
                <!-- Nút Xuất Excel Tất Cả (Theo Lọc) -->
                <a href="{{ route('admin.priceSheet.exportExcel', ['keyword' => request('keyword')]) }}" class="btn btn-sm btn-outline-success ms-2">
                    <i class="fas fa-file-excel me-1"></i> Xuất Tất Cả
                </a>

                <!-- Nút Xuất Excel Các Mục Đã Tích (Chỉ gửi ID được chọn xuyên trang) -->
                <button type="button" id="btnExportSelected" class="btn btn-sm btn-success ms-1" disabled>
                    <i class="fas fa-check-square me-1"></i> Xuất Mục Đã Chọn (<span id="selectedCount">0</span>)
                </button>

                <!-- Nút Xóa bộ nhớ tích nếu muốn chọn lại từ đầu -->
                <button type="button" id="btnClearSelected" class="btn btn-sm btn-outline-danger ms-1 d-none" title="Bỏ chọn tất cả các trang">
                    <i class="fas fa-times"></i> Bỏ chọn tất cả
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Form Tìm kiếm -->
            <form action="{{ route('admin.priceSheet.allDetails') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                   name="keyword" 
                                   class="form-control" 
                                   placeholder="Tìm tên phiếu, NCC, sản phẩm..." 
                                   value="{{ request('keyword') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Bảng dữ liệu -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 text-nowrap" style="font-size: 0.8rem;">
                    <thead class="thead-light text-center">
                        <tr>
                            <!-- Checkbox Chọn tất cả của Trang hiện tại -->
                            <th rowspan="2" class="align-middle text-center" style="width: 40px;">
                                <input type="checkbox" id="checkAll" class="form-check-input ms-0">
                            </th>
                            <th rowspan="2" class="align-middle">STT</th>
                            <th rowspan="2" class="align-middle">Bảng Giá</th>
                            <th rowspan="2" class="align-middle">Ngày</th>
                            <th rowspan="2" class="align-middle">NCC</th>
                            <th rowspan="2" class="align-middle">Sản Phẩm</th>
                            
                            <!-- Nhóm Đầu vào & Giá tiền -->
                            <th colspan="4" class="table-primary text-primary">ĐẦU VÀO & GIÁ TIỀN</th>
                            
                            <!-- Nhóm Thuế & Phí Phần Trăm -->
                            <th colspan="4" class="table-warning text-dark">THUẾ & PHÍ DỊCH VỤ (%)</th>
                            
                            <!-- Nhóm Phí Logistics & Điều hành -->
                            <th colspan="2" class="table-info text-dark">LOGISTICS & ĐIỀU HÀNH ($)</th>
                            
                            <!-- Nhóm Tổng Chi Phí & Giá Vốn -->
                            <th colspan="4" class="table-secondary text-dark">TỔNG TÍNH TOÁN ($)</th>

                            <!-- Nhóm Giá Đối Thủ -->
                            <th colspan="2" class="table-danger text-dark">ĐỐI THỦ ($)</th>
                            
                            <!-- Nhóm Kịch Bản Lợi Nhuận -->
                            <th colspan="3" class="table-success text-success">LỢI NHUẬN KHUYẾN NGHỊ ($)</th>
                            
                            <th rowspan="2" class="align-middle">Thao Tác</th>
                        </tr>
                        <tr>
                            <!-- Đầu vào -->
                            <th>TTL</th>
                            <th>FOB ($)</th>
                            <th>Thành Tiền ($)</th>
                            <th>Logistics ($)</th>

                            <!-- Thuế & Phí % -->
                            <th>NK (%)</th>
                            <th>VAT (%)</th>
                            <th>DV (%)</th>
                            <th>Kho (%)</th>

                            <!-- Phí cố định -->
                            <th>LCC</th>
                            <th>Operation</th>

                            <!-- Tổng tính toán -->
                            <th>Thuế ($)</th>
                            <th>DV ($)</th>
                            <th>Kho ($)</th>
                            <th>Tổng Chi Phí</th>

                            <!-- Đối thủ -->
                            <th>Giá Gốc</th>
                            <th>Giá CK</th>

                            <!-- Lợi nhuận -->
                            <th class="table-success">LN 5%</th>
                            <th class="table-success">LN 10%</th>
                            <th class="table-success">LN 15%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $index => $item)
                            @php
                                $priceAmount = ($item->fob ?? 0) * ($item->ttl ?? 0);
                                
                                $res5  = $item->results->first(fn($r) => round($r->margin_percent) == 5);
                                $res10 = $item->results->first(fn($r) => round($r->margin_percent) == 10);
                                $res15 = $item->results->first(fn($r) => round($r->margin_percent) == 15);
                            @endphp
                            <tr>
                                <!-- Checkbox từng item -->
                                <td class="text-center align-middle">
                                    <input type="checkbox" value="{{ $item->id }}" class="form-check-input item-checkbox ms-0">
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $items->firstItem() + $index }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.priceSheet.detail', $item->sheet_id) }}" class="font-weight-bold text-dark">
                                        {{ $item->sheet->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ optional($item->sheet)->sheet_date ? \Carbon\Carbon::parse($item->sheet->sheet_date)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info px-2 py-1">
                                        {{ $item->sheet->supplier->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="font-weight-bold text-primary">
                                    {{ $item->product->short_name ?? $item->product->name ?? 'N/A' }}
                                </td>

                                <!-- Dữ liệu Nhập tay -->
                                <td class="text-center font-weight-bold">{{ number_format($item->ttl ?? 0) }}</td>
                                <td class="text-right">$ {{ number_format($item->fob ?? 0, 2) }}</td>
                                <td class="text-right font-weight-bold text-primary">$ {{ number_format($priceAmount, 2) }}</td>
                                <td class="text-right">$ {{ number_format($item->logistics ?? 0, 2) }}</td>

                                <!-- Snapshot Tỷ Lệ Chi Phí (%) -->
                                <td class="text-center">{{ number_format($item->import_tax ?? 0, 1) }}%</td>
                                <td class="text-center">{{ number_format($item->vat ?? 0, 1) }}%</td>
                                <td class="text-center">{{ number_format($item->service_percent ?? 0, 1) }}%</td>
                                <td class="text-center">{{ number_format($item->warehouse_percent ?? 0, 1) }}%</td>

                                <!-- Snapshot Chi Phí Cố Định ($) -->
                                <td class="text-right">$ {{ number_format($item->lcc ?? 0, 2) }}</td>
                                <td class="text-right">$ {{ number_format($item->operation ?? 0, 2) }}</td>

                                <!-- Kết Quả Tính Toán Tổng -->
                                <td class="text-right">$ {{ number_format($item->tax_amount ?? 0, 2) }}</td>
                                <td class="text-right">$ {{ number_format($item->service_amount ?? 0, 2) }}</td>
                                <td class="text-right">$ {{ number_format($item->warehouse_amount ?? 0, 2) }}</td>
                                <td class="text-right font-weight-bold text-danger">$ {{ number_format($item->total_amount ?? 0, 2) }}</td>

                                <!-- Giá Đối Thủ -->
                                <td class="text-right text-muted">$ {{ number_format($item->competitor_price ?? 0, 2) }}</td>
                                <td class="text-right text-muted">$ {{ number_format($item->competitor_discounted_price ?? 0, 2) }}</td>

                                <!-- Mốc Lợi Nhuận Kịch Bản -->
                                <td class="text-right font-weight-bold text-success bg-light">
                                    {{ $res5 ? '$ ' . number_format($res5->profit, 2) : '-' }}
                                </td>
                                <td class="text-right font-weight-bold text-success bg-light">
                                    {{ $res10 ? '$ ' . number_format($res10->profit, 2) : '-' }}
                                </td>
                                <td class="text-right font-weight-bold text-success bg-light">
                                    {{ $res15 ? '$ ' . number_format($res15->profit, 2) : '-' }}
                                </td>

                                <!-- Thao tác -->
                                <td class="text-center">
                                    <a href="{{ route('admin.priceSheet.edit', $item->sheet_id) }}" 
                                       class="btn btn-sm btn-outline-warning border-0" 
                                       title="Chỉnh sửa bảng giá">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="22" class="text-center text-muted py-4">
                                    Chưa có dữ liệu sản phẩm nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang -->
        @if ($items->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end py-2">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Script Lưu trữ ID đã chọn qua các trang dùng SessionStorage -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const STORAGE_KEY = 'selected_price_sheet_item_ids';
    const checkAll = document.getElementById('checkAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const btnExportSelected = document.getElementById('btnExportSelected');
    const btnClearSelected = document.getElementById('btnClearSelected');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Lấy danh sách ID đã chọn từ bộ nhớ trình duyệt
    function getSelectedIds() {
        return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
    }

    // Lưu lại danh sách ID vào bộ nhớ trình duyệt
    function saveSelectedIds(ids) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
        updateUI();
    }

    // Cập nhật Giao diện nút bấm và số lượng
    function updateUI() {
        const selectedIds = getSelectedIds();
        const count = selectedIds.length;

        selectedCountSpan.textContent = count;
        btnExportSelected.disabled = count === 0;

        if (count > 0) {
            btnClearSelected.classList.remove('d-none');
        } else {
            btnClearSelected.classList.add('d-none');
        }

        // Kiểm tra xem tất cả các ô ở TRANG HIỆN TẠI có được chọn hết không
        if (itemCheckboxes.length > 0) {
            const allCheckedInCurrentPage = Array.from(itemCheckboxes).every(cb => selectedIds.includes(cb.value));
            checkAll.checked = allCheckedInCurrentPage;
        }
    }

    // Khôi phục trạng thái Checkbox khi load lại trang
    function restoreCheckboxState() {
        const selectedIds = getSelectedIds();
        itemCheckboxes.forEach(cb => {
            if (selectedIds.includes(cb.value)) {
                cb.checked = true;
            }
        });
        updateUI();
    }

    // Sự kiện khi click ô "Chọn tất cả" trên trang hiện tại
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            let selectedIds = getSelectedIds();
            itemCheckboxes.forEach(cb => {
                cb.checked = checkAll.checked;
                const id = cb.value;
                if (checkAll.checked) {
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(item => item !== id);
                }
            });
            saveSelectedIds(selectedIds);
        });
    }

    // Sự kiện khi tích từng ô lẻ
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            let selectedIds = getSelectedIds();
            const id = this.value;

            if (this.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }

            saveSelectedIds(selectedIds);
        });
    });

    // Bấm nút "Bỏ chọn tất cả"
    if (btnClearSelected) {
        btnClearSelected.addEventListener('click', function () {
            sessionStorage.removeItem(STORAGE_KEY);
            itemCheckboxes.forEach(cb => cb.checked = false);
            if (checkAll) checkAll.checked = false;
            updateUI();
        });
    }

    // Bấm nút "Xuất Mục Đã Chọn"
    if (btnExportSelected) {
        btnExportSelected.addEventListener('click', function () {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) return;

            // Dựng URL kèm query string ids[]
            const baseUrl = "{{ route('admin.priceSheet.exportExcel') }}";
            const queryParams = selectedIds.map(id => `ids[]=${encodeURIComponent(id)}`).join('&');
            
            // Tiến hành tải xuống file Excel
            window.location.href = `${baseUrl}?${queryParams}`;
        });
    }

    // Chạy hàm khôi phục khi load xong DOM
    restoreCheckboxState();
});
</script>
@endsection