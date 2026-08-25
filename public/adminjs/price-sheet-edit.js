document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;

    const tableBody = document.querySelector('#price-sheet-items-table tbody');
    const btnAddItem = document.getElementById('btn-add-item');
    const form = document.getElementById('price-sheet-form');

    const productsList = window.priceSheetConfig?.products || [];
    const pricingRules = window.priceSheetConfig?.pricingRules || [];
    const existingItems = window.priceSheetConfig?.existingItems || [];

    let profitDetails = [];
    let discountDetail = null;

    function formatUSD(value) {
        const number = parseFloat(value) || 0;
        return '$ ' + number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function getCurrencyValue(input) {
        if (!input) return 0;
        let value = String(input.value || '').replace(/\$/g, '').replace(/,/g, '').trim();
        if (!value) return 0;
        if (value.includes('.')) return parseFloat(value) || 0;
        const digits = value.replace(/\D/g, '');
        if (!digits) return 0;
        if (digits.length <= 4) return parseInt(digits, 10) || 0;
        return parseInt(digits, 10) / 100;
    }

    function updateCurrencyHidden(input) {
        const row = input.closest('tr.item-row');
        if (!row) return;
        const value = getCurrencyValue(input);
        let hiddenInput = null;

        if (input.classList.contains('competitor-price-input')) hiddenInput = row.querySelector('.competitor-price-hidden');
        else if (input.classList.contains('fob-input')) hiddenInput = row.querySelector('.fob-hidden');
        else if (input.classList.contains('logistics-input')) hiddenInput = row.querySelector('.logistics-hidden');
        else if (input.classList.contains('lcc-input')) hiddenInput = row.querySelector('.lcc-hidden');
        else if (input.classList.contains('operation-input')) hiddenInput = row.querySelector('.operation-hidden');

        if (hiddenInput) hiddenInput.value = value.toFixed(2);
    }

    function setCurrencyValue(input, value) {
        const number = parseFloat(value) || 0;
        input.value = formatUSD(number);
        updateCurrencyHidden(input);
    }

    pricingRules.forEach(rule => {
        if (!rule.details || !Array.isArray(rule.details)) return;
        rule.details.forEach(detail => {
            if (detail.type === 'profit') profitDetails.push(detail);
            else if (detail.type === 'discount' && !discountDetail) discountDetail = detail;
        });
    });

    if (profitDetails.length === 0) {
        profitDetails = [
            { id: null, name: 'Giá bán 5%', value: 5 },
            { id: null, name: 'Giá bán 10%', value: 10 },
            { id: null, name: 'Giá bán 15%', value: 15 }
        ];
    }

    const discountPercent = discountDetail ? parseFloat(discountDetail.value) : 10;
    const discountLabel = discountDetail ? (discountDetail.name || `Chiết khấu ${discountPercent}%`) : `Chiết khấu ${discountPercent}%`;

    // Cập nhật createRow để nạp dữ liệu cũ
    function createRow(index, itemData = null) {
        let productOptions = '<option value="">-- Chọn sản phẩm --</option>';
        const selectedProductId = itemData ? itemData.product_id : '';

        productsList.forEach(product => {
            const isSelected = String(product.id) === String(selectedProductId) ? 'selected' : '';
            productOptions += `<option value="${product.id}" ${isSelected}>${product.short_name}</option>`;
        });

        // Kết quả lợi nhuận
        const resultsRowsHtml = profitDetails.map((detail, rIdx) => {
            // Tìm result tương ứng nếu đang edit
            let existingResult = null;
            if (itemData && itemData.results && Array.isArray(itemData.results)) {
                existingResult = itemData.results.find(r => String(r.pricing_rule_detail_id) === String(detail.id));
            }

            const marginVal = existingResult ? existingResult.margin_percent : detail.value;

            return `
                <tr>
                    <td style="width: 42%;" class="align-middle p-1">
                        <small class="d-block font-weight-bold text-secondary mb-1" style="font-size: 0.75rem;">
                            ${detail.name || 'Mốc ' + detail.value + '%'}
                        </small>
                        <div class="input-group input-group-sm">
                            <input type="number"
                                   step="0.1"
                                   name="items[${index}][results][${rIdx}][margin_percent]"
                                   class="form-control margin-percent-input input-calc px-1 text-center"
                                   value="${marginVal}">
                            <div class="input-group-append">
                                <span class="input-group-text px-1">%</span>
                            </div>
                        </div>
                        <input type="hidden"
                               name="items[${index}][results][${rIdx}][pricing_rule_detail_id]"
                               value="${detail.id || ''}">
                    </td>
                    <td class="text-right align-middle p-1">
                        <span class="selling-price-text font-weight-bold text-dark">$ 0.00</span>
                        <input type="hidden" name="items[${index}][results][${rIdx}][selling_price]" class="selling-price-hidden">
                    </td>
                    <td class="text-right align-middle p-1 text-success">
                        <span class="profit-text font-weight-bold">$ 0.00</span>
                        <input type="hidden" name="items[${index}][results][${rIdx}][profit]" class="profit-hidden">
                    </td>
                </tr>
            `;
        }).join('');

        // Lấy giá trị mặc định hoặc từ dữ liệu cũ
        const competitorPrice = itemData ? parseFloat(itemData.competitor_price || 0) : 0;
        const ttl = itemData ? itemData.ttl : 0;
        const fob = itemData ? parseFloat(itemData.fob || 0) : 0;
        const logistics = itemData ? parseFloat(itemData.logistics || 0) : 0;
        const importTax = itemData ? itemData.import_tax : 0;
        const vat = itemData ? itemData.vat : 0;
        const servicePercent = itemData ? itemData.service_percent : 3;
        const warehousePercent = itemData ? itemData.warehouse_percent : 1;
        const lcc = itemData ? parseFloat(itemData.lcc || 0) : 0;
        const operation = itemData ? parseFloat(itemData.operation || 0) : 0;
        const itemIdHidden = itemData && itemData.id ? `<input type="hidden" name="items[${index}][id]" value="${itemData.id}">` : '';

        return `
            <tr id="item-row-${index}" class="item-row" data-index="${index}">
                <td class="align-middle">
                    ${itemIdHidden}
                    <select name="items[${index}][product_id]" class="form-control form-control-sm product-select font-weight-bold" required>
                        ${productOptions}
                    </select>

                    <input type="hidden" name="items[${index}][competitor_price]" class="competitor-price-hidden" value="${competitorPrice.toFixed(2)}">
                    <input type="text" inputmode="decimal" autocomplete="off" class="form-control form-control-sm mt-1 input-calc competitor-price-input currency-input text-right" placeholder="$ 0.00" value="${formatUSD(competitorPrice)}">

                    <div class="mt-1 bg-light p-1 border rounded d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                        <span class="text-muted mr-1">${discountLabel}:</span>
                        <b class="text-primary competitor-discounted-text">$ 0.00</b>
                        <input type="hidden" name="items[${index}][competitor_discounted_price]" class="competitor-discounted-hidden" value="0.00">
                        <input type="hidden" class="discount-percent-val" value="${discountPercent}">
                    </div>
                </td>

                <td class="align-middle" style="min-width: 110px;">
                    <input type="number" step="0.01" name="items[${index}][ttl]" class="form-control form-control-sm input-calc ttl-input text-center" value="${ttl}" required>
                </td>

                <td class="align-middle">
                    <input type="hidden" name="items[${index}][fob]" class="fob-hidden" value="${fob.toFixed(2)}">
                    <input type="text" inputmode="decimal" autocomplete="off" class="form-control form-control-sm input-calc fob-input currency-input text-right" value="${formatUSD(fob)}" required>
                </td>

                <td class="align-middle">
                    <input type="hidden" name="items[${index}][logistics]" class="logistics-hidden" value="${logistics.toFixed(2)}">
                    <input type="text" inputmode="decimal" autocomplete="off" class="form-control form-control-sm input-calc logistics-input currency-input text-right" value="${formatUSD(logistics)}">
                </td>

                <td class="align-middle px-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted mr-1">NK%:</small>
                        <input type="number" step="0.1" name="items[${index}][import_tax]" class="form-control form-control-sm input-calc import-tax-input px-1 text-center" style="width: 55px;" value="${importTax}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted mr-1">VAT%:</small>
                        <input type="number" step="0.1" name="items[${index}][vat]" class="form-control form-control-sm input-calc vat-input px-1 text-center" style="width: 55px;" value="${vat}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted mr-1">Serv%:</small>
                        <input type="number" step="0.1" name="items[${index}][service_percent]" class="form-control form-control-sm input-calc service-input px-1 text-center" style="width: 55px;" value="${servicePercent}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted mr-1">Kho%:</small>
                        <input type="number" step="0.1" name="items[${index}][warehouse_percent]" class="form-control form-control-sm input-calc warehouse-input px-1 text-center" style="width: 55px;" value="${warehousePercent}">
                    </div>
                </td>

                <td class="align-middle px-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted mr-1">LCC:</small>
                        <input type="hidden" name="items[${index}][lcc]" class="lcc-hidden" value="${lcc.toFixed(2)}">
                        <input type="text" inputmode="decimal" autocomplete="off" class="form-control form-control-sm input-calc lcc-input currency-input px-1 text-right" style="width: 75px;" value="${formatUSD(lcc)}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted mr-1">V.Hành:</small>
                        <input type="hidden" name="items[${index}][operation]" class="operation-hidden" value="${operation.toFixed(2)}">
                        <input type="text" inputmode="decimal" autocomplete="off" class="form-control form-control-sm input-calc operation-input currency-input px-1 text-right" style="width: 75px;" value="${formatUSD(operation)}">
                    </div>
                </td>

                <td class="small align-middle px-2" style="font-size: 0.8rem;">
                    <div class="d-flex justify-content-between mb-1"><span>G.Tiền:</span><b class="price-amount-text text-dark">$ 0.00</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Thuế:</span><b class="tax-amount-text text-dark">$ 0.00</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Serv:</span><b class="service-amount-text text-dark">$ 0.00</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Kho:</span><b class="warehouse-amount-text text-dark">$ 0.00</b></div>
                    <div class="d-flex justify-content-between pt-1 border-top"><span class="font-weight-bold">T.Tiền:</span><b class="total-amount-text text-dark">$ 0.00</b></div>
                </td>

                <td class="text-right font-weight-bold text-danger bg-light align-middle px-2">
                    <span class="cost-per-ton-text style-cost" style="font-size: 1.05rem;">$ 0.00</span>
                </td>

                <td class="align-middle p-1 bg-light">
                    <table class="table table-sm table-bordered m-0 bg-white shadow-sm">
                        <thead class="thead-dark text-center" style="font-size: 0.72rem;">
                            <tr><th>Cấu hình %</th><th>Giá Bán</th><th>Lợi Nhuận</th></tr>
                        </thead>
                        <tbody class="results-tbody" style="font-size: 0.8rem;">
                            ${resultsRowsHtml}
                        </tbody>
                    </table>
                </td>

                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row border-0">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function addNewRow(itemData = null) {
        tableBody.insertAdjacentHTML('beforeend', createRow(itemIndex, itemData));
        const insertedRow = document.getElementById(`item-row-${itemIndex}`);
        calculateRow(insertedRow);
        itemIndex++;
    }

    // Khởi tạo dòng: Nếu có dữ liệu cũ thì render từng dòng, nếu không tạo 1 dòng trống
    if (existingItems && existingItems.length > 0) {
        existingItems.forEach(item => addNewRow(item));
    } else {
        addNewRow();
    }

    btnAddItem.addEventListener('click', () => addNewRow());

    // Giữ nguyên các Event Listener (click, change, input, focusin, focusout, submit) và hàm calculateRow() như file Add
    tableBody.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-row');
        if (!removeBtn) return;
        if (tableBody.querySelectorAll('tr.item-row').length > 1) {
            removeBtn.closest('tr.item-row').remove();
        } else {
            alert('Phiếu tính giá phải có ít nhất 1 sản phẩm!');
        }
    });

    tableBody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('product-select')) return;
        const row = e.target.closest('tr.item-row');
        const productId = e.target.value;
        if (!productId) return;

        fetch(`/admin/products/${productId}/cost-settings`)
            .then(res => res.json())
            .then(data => {
                if (!data) return;
                row.querySelector('.import-tax-input').value = data.import_tax || 0;
                row.querySelector('.vat-input').value = data.vat || 0;
                row.querySelector('.service-input').value = data.service_percent ?? 3;
                row.querySelector('.warehouse-input').value = data.warehouse_percent ?? 1;
                setCurrencyValue(row.querySelector('.lcc-input'), data.lcc || 0);
                setCurrencyValue(row.querySelector('.operation-input'), data.operation || 0);
                calculateRow(row);
            })
            .catch(err => console.log('Chưa có cấu hình SP:', err));
    });

    tableBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('currency-input')) {
            let value = e.target.value.replace(/\$/g, '').replace(/,/g, '').replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
            e.target.value = value;
            updateCurrencyHidden(e.target);
            calculateRow(e.target.closest('tr.item-row'));
            return;
        }
        if (e.target.classList.contains('input-calc')) {
            calculateRow(e.target.closest('tr.item-row'));
        }
    });

    tableBody.addEventListener('focusin', function (e) {
        if (!e.target.classList.contains('currency-input')) return;
        const value = getCurrencyValue(e.target);
        e.target.value = value > 0 ? value.toString() : '';
    });

    tableBody.addEventListener('focusout', function (e) {
        if (!e.target.classList.contains('currency-input')) return;
        const value = getCurrencyValue(e.target);
        e.target.value = formatUSD(value);
        updateCurrencyHidden(e.target);
        calculateRow(e.target.closest('tr.item-row'));
    });

    form.addEventListener('submit', function () {
        document.querySelectorAll('.currency-input').forEach(input => {
            const value = getCurrencyValue(input);
            input.value = formatUSD(value);
            updateCurrencyHidden(input);
        });
    });

    function calculateRow(row) {
        if (!row) return;

        const competitorPrice = getCurrencyValue(row.querySelector('.competitor-price-input'));
        const discountPercent = parseFloat(row.querySelector('.discount-percent-val').value) || 0;
        const competitorDiscountedPrice = competitorPrice * (1 - discountPercent / 100);

        row.querySelector('.competitor-discounted-text').innerText = formatUSD(competitorDiscountedPrice);
        row.querySelector('.competitor-discounted-hidden').value = competitorDiscountedPrice.toFixed(2);

        const ttl = parseFloat(row.querySelector('.ttl-input').value) || 0;
        const fob = getCurrencyValue(row.querySelector('.fob-input'));
        const logistics = getCurrencyValue(row.querySelector('.logistics-input'));

        const importTaxPercent = (parseFloat(row.querySelector('.import-tax-input').value) || 0) / 100;
        const vatPercent = (parseFloat(row.querySelector('.vat-input').value) || 0) / 100;
        const servicePercent = (parseFloat(row.querySelector('.service-input').value) || 0) / 100;
        const warehousePercent = (parseFloat(row.querySelector('.warehouse-input').value) || 0) / 100;

        const lcc = getCurrencyValue(row.querySelector('.lcc-input'));
        const operation = getCurrencyValue(row.querySelector('.operation-input'));

        const priceAmount = fob * ttl;
        const importTaxAmount = (priceAmount + logistics) * importTaxPercent;
        const vatAmount = (priceAmount + logistics + importTaxAmount) * vatPercent;
        const totalTaxAmount = importTaxAmount + vatAmount;
        const serviceAmount = priceAmount * servicePercent;
        const warehouseAmount = priceAmount * warehousePercent;

        const totalAmount = priceAmount + totalTaxAmount + serviceAmount + warehouseAmount + lcc + logistics + operation;
        const costPerTon = ttl > 0 ? totalAmount / ttl : 0;

        row.querySelector('.price-amount-text').innerText = formatUSD(priceAmount);
        row.querySelector('.tax-amount-text').innerText = formatUSD(totalTaxAmount);
        row.querySelector('.service-amount-text').innerText = formatUSD(serviceAmount);
        row.querySelector('.warehouse-amount-text').innerText = formatUSD(warehouseAmount);
        row.querySelector('.total-amount-text').innerText = formatUSD(totalAmount);
        row.querySelector('.cost-per-ton-text').innerText = formatUSD(costPerTon);

        const resultRows = row.querySelectorAll('.results-tbody tr');
        resultRows.forEach(rRow => {
            const marginPercent = parseFloat(rRow.querySelector('.margin-percent-input').value) || 0;
            let sellingPrice = 0;
            if (marginPercent < 100) {
                sellingPrice = costPerTon / (1 - marginPercent / 100);
            }
            const profit = sellingPrice - costPerTon;

            rRow.querySelector('.selling-price-text').innerText = formatUSD(sellingPrice);
            rRow.querySelector('.selling-price-hidden').value = sellingPrice.toFixed(2);
            rRow.querySelector('.profit-text').innerText = formatUSD(profit);
            rRow.querySelector('.profit-hidden').value = profit.toFixed(2);
        });
    }
});