<?php

namespace App\Exports;

use App\Models\PriceSheetItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PriceSheetDetailsExport implements 
    FromQuery, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles, 
    WithColumnFormatting, 
    WithEvents, 
    WithCustomStartCell
{
    protected $keyword;
    protected $ids;
    protected $rowNumber = 0;

    /**
     * Khởi tạo Export
     * @param string|null $keyword
     * @param array $ids Danh sách các ID được chọn từ nhiều trang
     */
    public function __construct($keyword = null, $ids = [])
    {
        $this->keyword = $keyword;
        $this->ids = is_array($ids) ? $ids : [];
    }

    /**
     * Dữ liệu bắt đầu ghi từ dòng 3 (vì Dòng 1 & 2 dành cho Header 2 tầng)
     */
    public function startCell(): string
    {
        return 'A3';
    }

    public function query()
    {
        $keyword = $this->keyword;

        $query = PriceSheetItem::with([
            'sheet.supplier', 
            'product', 
            'results.pricingRuleDetail'
        ]);

        // ƯU TIÊN: Nếu có truyền danh sách ID được chọn (xuyên trang)
        if (!empty($this->ids)) {
            return $query->whereIn('price_sheet_items.id', $this->ids)
                         ->orderBy('price_sheet_items.id', 'desc');
        }

        // BÌNH THƯỜNG: Nếu không chọn ID cụ thể, thực hiện lọc theo Keyword
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('sheet', function ($sq) use ($keyword) {
                    $sq->where('price_sheets.name', 'like', "%{$keyword}%");
                })
                ->orWhereHas('sheet.supplier', function ($ssq) use ($keyword) {
                    $ssq->where('suppliers.name', 'like', "%{$keyword}%")
                       ->orWhere('suppliers.code', 'like', "%{$keyword}%");
                })
                ->orWhereHas('product', function ($pq) use ($keyword) {
                    $pq->where('products.short_name', 'like', "%{$keyword}%")
                       ->orWhere('products.name', 'like', "%{$keyword}%");
                });
            });
        }

        return $query->orderBy('price_sheet_items.id', 'desc');
    }

    public function map($item): array
    {
        $this->rowNumber++;
        $priceAmount = ($item->fob ?? 0) * ($item->ttl ?? 0);

        $res5  = $item->results->first(fn($r) => round($r->margin_percent) == 5);
        $res10 = $item->results->first(fn($r) => round($r->margin_percent) == 10);
        $res15 = $item->results->first(fn($r) => round($r->margin_percent) == 15);

        return [
            $this->rowNumber, // Cột STT
            $item->sheet->name ?? 'N/A',
            optional($item->sheet)->sheet_date ? \Carbon\Carbon::parse($item->sheet->sheet_date)->format('d/m/Y') : 'N/A',
            $item->sheet->supplier->code ?? 'N/A',
            $item->product->short_name ?? $item->product->name ?? 'N/A',
            
            // Đầu vào & Giá tiền
            (float) ($item->ttl ?? 0),
            (float) ($item->fob ?? 0),
            (float) $priceAmount,
            (float) ($item->logistics ?? 0),

            // Thuế & Phí (%)
            (float) (($item->import_tax ?? 0) / 100),
            (float) (($item->vat ?? 0) / 100),
            (float) (($item->service_percent ?? 0) / 100),
            (float) (($item->warehouse_percent ?? 0) / 100),

            // Logistics & Điều hành
            (float) ($item->lcc ?? 0),
            (float) ($item->operation ?? 0),

            // Tổng chi phí
            (float) ($item->tax_amount ?? 0),
            (float) ($item->service_amount ?? 0),
            (float) ($item->warehouse_amount ?? 0),
            (float) ($item->total_amount ?? 0),

            // Đối thủ
            (float) ($item->competitor_price ?? 0),
            (float) ($item->competitor_discounted_price ?? 0),

            // Lợi nhuận
            $res5 ? (float) $res5->profit : null,
            $res10 ? (float) $res10->profit : null,
            $res15 ? (float) $res15->profit : null,
        ];
    }

    /**
     * Định dạng kiểu dữ liệu cho từng cột (Tiền tệ, %, Số)
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // TTL (Số lượng)
            'G' => '"$"#,##0.00', // FOB
            'H' => '"$"#,##0.00', // Thành Tiền
            'I' => '"$"#,##0.00', // Logistics
            'J' => '0.0%',        // NK %
            'K' => '0.0%',        // VAT %
            'L' => '0.0%',        // DV %
            'M' => '0.0%',        // Kho %
            'N' => '"$"#,##0.00', // LCC
            'O' => '"$"#,##0.00', // Operation
            'P' => '"$"#,##0.00', // Thuế
            'Q' => '"$"#,##0.00', // Phí DV
            'R' => '"$"#,##0.00', // Phí Kho
            'S' => '"$"#,##0.00', // Tổng Chi Phí
            'T' => '"$"#,##0.00', // Giá Gốc Đối Thủ
            'U' => '"$"#,##0.00', // Giá CK Đối Thủ
            'V' => '"$"#,##0.00', // LN 5%
            'W' => '"$"#,##0.00', // LN 10%
            'X' => '"$"#,##0.00', // LN 15%
        ];
    }

    /**
     * Đăng ký Event dựng Header 2 tầng và Style từng nhóm cột
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Viết Nội dung Header Dòng 1 & Dòng 2
                $sheet->setCellValue('A1', 'STT');
                $sheet->setCellValue('B1', 'Bảng Giá');
                $sheet->setCellValue('C1', 'Ngày');
                $sheet->setCellValue('D1', 'NCC');
                $sheet->setCellValue('E1', 'Sản Phẩm');

                $sheet->setCellValue('F1', 'ĐẦU VÀO & GIÁ TIỀN');
                $sheet->setCellValue('J1', 'THUẾ & PHÍ DỊCH VỤ (%)');
                $sheet->setCellValue('N1', 'LOGISTICS & ĐIỀU HÀNH ($)');
                $sheet->setCellValue('P1', 'TỔNG TÍNH TOÁN ($)');
                $sheet->setCellValue('T1', 'ĐỐI THỦ ($)');
                $sheet->setCellValue('V1', 'LỢI NHUẬN KHUYẾN NGHỊ ($)');

                // Dòng 2 Sub-headers
                $sheet->setCellValue('F2', 'TTL');
                $sheet->setCellValue('G2', 'FOB ($)');
                $sheet->setCellValue('H2', 'Thành Tiền ($)');
                $sheet->setCellValue('I2', 'Logistics ($)');

                $sheet->setCellValue('J2', 'NK (%)');
                $sheet->setCellValue('K2', 'VAT (%)');
                $sheet->setCellValue('L2', 'DV (%)');
                $sheet->setCellValue('M2', 'Kho (%)');

                $sheet->setCellValue('N2', 'LCC');
                $sheet->setCellValue('O2', 'Operation');

                $sheet->setCellValue('P2', 'Thuế ($)');
                $sheet->setCellValue('Q2', 'DV ($)');
                $sheet->setCellValue('R2', 'Kho ($)');
                $sheet->setCellValue('S2', 'Tổng Chi Phí');

                $sheet->setCellValue('T2', 'Giá Gốc');
                $sheet->setCellValue('U2', 'Giá CK');

                $sheet->setCellValue('V2', 'LN 5%');
                $sheet->setCellValue('W2', 'LN 10%');
                $sheet->setCellValue('X2', 'LN 15%');

                // 2. Merge Cells cho Header
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');

                $sheet->mergeCells('F1:I1'); // Đầu vào & giá tiền
                $sheet->mergeCells('J1:M1'); // Thuế & Phí %
                $sheet->mergeCells('N1:O1'); // Logistics & Điều hành
                $sheet->mergeCells('P1:S1'); // Tổng tính toán
                $sheet->mergeCells('T1:U1'); // Đối thủ
                $sheet->mergeCells('V1:X1'); // Lợi nhuận

                // 3. Tô màu Background Header tương ứng với các class Bootstrap trong Blade
                $this->setGroupHeaderStyle($sheet, 'A1:E2', 'F2F2F2', '000000'); // Mặc định xám nhẹ
                $this->setGroupHeaderStyle($sheet, 'F1:I2', 'CFE2FF', '084298'); // table-primary (Xanh dương)
                $this->setGroupHeaderStyle($sheet, 'J1:M2', 'FFF3CD', '664D03'); // table-warning (Vàng)
                $this->setGroupHeaderStyle($sheet, 'N1:O2', 'CFF4FC', '055160'); // table-info (Xanh nhạt)
                $this->setGroupHeaderStyle($sheet, 'P1:S2', 'E2E3E5', '41464B'); // table-secondary (Xám)
                $this->setGroupHeaderStyle($sheet, 'T1:U2', 'F8D7DA', '842029'); // table-danger (Đỏ)
                $this->setGroupHeaderStyle($sheet, 'V1:X2', 'D1E7DD', '0F5132'); // table-success (Xanh lá)

                // 4. Kẻ viền bảng (Border) và căn chỉnh cho toàn bộ ô dữ liệu
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:X{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'D3D3D3'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Căn giữa cột STT, Ngày, NCC, %
                $sheet->getStyle("A3:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J3:M{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    /**
     * Helper tô màu Header
     */
    private function setGroupHeaderStyle($sheet, $range, $bgColor, $textColor)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => $textColor],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $bgColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}