<div class="card">
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $isEdit = (bool) $sale;
            $oldItems = $isEdit ? $sale->items : collect();
        @endphp

        <form id="saleForm" method="POST" action="{{ $isEdit ? route('sales.update', $sale) : route('sales.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-5 mb-3 position-relative">
                    <label>Customer <span class="text-danger">*</span></label>
                    <input type="text" id="customer_search" class="form-control" autocomplete="off"
                           value="{{ old('customer_id') && !$isEdit ? '' : ($sale?->customer?->business_name ?? '') }}"
                           placeholder="Search by name, code or phone">
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $sale?->customer_id) }}">
                    <div id="customer_results" class="list-group position-absolute w-100"
                         style="z-index:1050;display:none;max-height:220px;overflow:auto"></div>
                    <small id="selected_customer" class="text-success">
                        @if($sale) ✓ Customer selected @endif
                    </small>
                    @error('customer_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label>Sale Date <span class="text-danger">*</span></label>
                    <input type="date" name="sale_date" class="form-control"
                           value="{{ old('sale_date', $sale?->sale_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Payment Status</label>
                    <div class="form-control bg-light">Calculated from Paid Amount</div>
                    <small class="text-muted">Paid / Partial / Pending</small>
                </div>
            </div>

            <div class="card border mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Sale Items</strong>
                    <button type="button" id="addItem" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Add Item
                    </button>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="min-width:260px">Medicine</th>
                                <th style="min-width:260px">Batch</th>
                                <th style="width:100px">Qty</th>
                                <th style="width:140px">Sale Price</th>
                                <th style="width:130px">Discount</th>
                                <th style="width:140px">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <label>Invoice Discount (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100"
                               id="invoice_discount" name="invoice_discount" class="form-control"
                               value="{{ old('invoice_discount', $sale?->invoice_discount ?? 0) }}">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Applied after line discounts.</small>

                    <label class="mt-2">Subtotal</label>
                    <input id="subtotal" class="form-control" readonly value="0.00">

                    <label class="mt-2">Grand Total</label>
                    <input id="grand_total" class="form-control font-weight-bold" readonly value="0.00">

                    <label class="mt-2">Paid Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount"
                           class="form-control" value="{{ old('paid_amount', $sale?->paid_amount ?? 0) }}">

                    <label class="mt-2">Due Amount</label>
                    <input id="due_amount" class="form-control" readonly value="0.00">
                </div>
            </div>

            <button type="submit" class="btn btn-success" id="saveSale">
                {{ $isEdit ? 'Update Sale' : 'Save Sale' }}
            </button>
            <a class="btn btn-secondary" href="{{ $isEdit ? route('sales.show', $sale) : route('sales.index') }}">Cancel</a>
        </form>
    </div>
</div>

@push('js')
<script>
(() => {
    const form = document.getElementById('saleForm');
    const body = document.getElementById('itemsBody');
    const cs = document.getElementById('customer_search');
    const cr = document.getElementById('customer_results');
    const cid = document.getElementById('customer_id');
    const selectedCustomer = document.getElementById('selected_customer');
    let idx = 0;

    const esc = value => String(value ?? '').replace(/[&<>'"]/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    }[c]));

    const debounce = (fn, wait = 250) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), wait);
        };
    };

    async function get(url, query) {
        const response = await fetch(url + '?q=' + encodeURIComponent(query), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error();
        return response.json();
    }

    function positionMedicineResults(box, input) {
        const rect = input.getBoundingClientRect();
        box.style.left = rect.left + 'px';
        box.style.top = (rect.bottom + 4) + 'px';
        box.style.width = Math.max(rect.width, 320) + 'px';
    }

    cs.addEventListener('input', debounce(async () => {
        cid.value = '';
        selectedCustomer.textContent = '';
        const q = cs.value.trim();

        if (!q) {
            cr.style.display = 'none';
            return;
        }

        try {
            const rows = await get('{{ route('sales.customers.search') }}', q);
            cr.innerHTML = rows.length
                ? rows.map(c => `<button type="button" class="list-group-item list-group-item-action customer-option" data-id="${c.id}" data-label="${esc(c.business_name)}">${esc(c.business_name)} <small class="text-muted">${esc(c.customer_code)} ${esc(c.phone)}</small></button>`).join('')
                : '<div class="list-group-item text-muted">No active customer found.</div>';
            cr.style.display = 'block';
        } catch (e) {
            cr.style.display = 'none';
        }
    }, 250));

    cr.addEventListener('click', e => {
        const b = e.target.closest('.customer-option');
        if (!b) return;
        cid.value = b.dataset.id;
        cs.value = b.dataset.label;
        selectedCustomer.textContent = '✓ Customer selected';
        cr.style.display = 'none';
    });

    function addRow(data = null) {
        const i = idx++;

        body.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="position-relative">
                    <input type="text" class="form-control medicine-search" autocomplete="off"
                           placeholder="Search medicine..." value="${esc(data?.medicine_name || '')}">
                    <input type="hidden" name="items[${i}][medicine_id]" class="medicine-id" value="${data?.medicine_id || ''}">
                    <div class="medicine-results list-group"
                         style="position:fixed;z-index:99999;display:none;max-height:280px;overflow-y:auto;overflow-x:hidden;min-width:320px"></div>
                    <small class="selected-medicine text-success">${data ? '✓ Medicine selected' : ''}</small>
                </td>
                <td>
                    <select name="items[${i}][batch_id]" class="form-control batch-select" ${data ? '' : 'disabled'}>
                        <option value="${data?.batch_id || ''}">${esc(data?.batch_no || 'Select batch')}</option>
                    </select>
                    <small class="batch-help text-muted"></small>
                </td>
                <td>
                    <input type="number" name="items[${i}][quantity]" class="form-control qty"
                           min="1" step="1" value="${data?.quantity || 1}" ${data ? '' : 'disabled'}>
                </td>
                <td>
                    <input type="number" name="items[${i}][unit_price]" class="form-control unit-price"
                           min="0" step="0.01" value="${data?.unit_price || 0}" ${data ? '' : 'disabled'}>
                </td>
                <td>
                    <input type="number" name="items[${i}][discount]" class="form-control line-discount"
                           min="0" step="0.01" value="${data?.line_discount || 0}" ${data ? '' : 'disabled'}>
                </td>
                <td><input class="form-control line-total" readonly value="0.00"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">×</button></td>
            </tr>
        `);

        const row = body.lastElementChild;
        if (data) loadBatches(row, data.medicine_id, data.batch_id);
        calculate();
    }

    async function loadBatches(row, medicineId, selectedId = null) {
        const select = row.querySelector('.batch-select');
        select.disabled = true;
        select.innerHTML = '<option>Loading...</option>';

        try {
            let url = '{{ url('/sales/medicines') }}/' + medicineId + '/batches';
            if (selectedId) url += '?selected=' + encodeURIComponent(selectedId);

            const response = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error();

            const batches = await response.json();

            select.innerHTML = '<option value="">Select batch</option>' + batches.map(b =>
                `<option value="${b.id}" data-qty="${b.quantity}" data-price="${b.sale_price}" data-mrp="${b.mrp}">${esc(b.batch_no)} — Stock: ${b.quantity} — Exp: ${esc(b.expiry_date)}</option>`
            ).join('');

            select.disabled = !batches.length;

            if (selectedId && batches.some(b => b.id == selectedId)) {
                select.value = selectedId;
            } else if (batches[0]) {
                select.value = batches[0].id;
            }

            if (select.value) {
                applyBatch(row, select.selectedOptions[0]);
            } else {
                row.querySelector('.batch-help').textContent = 'No available non-expired stock.';
            }
        } catch (e) {
            select.innerHTML = '<option value="">Select batch</option>';
            row.querySelector('.batch-help').textContent = 'Unable to load batches.';
        }
    }

    function applyBatch(row, option) {
        if (!option || !option.value) return;

        const q = row.querySelector('.qty');
        const p = row.querySelector('.unit-price');
        const d = row.querySelector('.line-discount');

        q.disabled = false;
        p.disabled = false;
        d.disabled = false;
        q.max = option.dataset.qty;
        p.max = option.dataset.mrp;

        if (!p.value || p.value === '0') p.value = option.dataset.price;
        calculate();
    }

    body.addEventListener('input', e => {
        if (e.target.classList.contains('medicine-search')) {
            const row = e.target.closest('tr');
            const q = e.target.value.trim();
            const box = row.querySelector('.medicine-results');

            row.querySelector('.medicine-id').value = '';
            row.querySelector('.batch-select').disabled = true;
            row.querySelector('.selected-medicine').textContent = '';

            if (!q) {
                box.style.display = 'none';
                calculate();
                return;
            }

            positionMedicineResults(box, e.target);
            box.style.display = 'block';

            get('{{ route('sales.medicines.search') }}', q)
                .then(rows => {
                    box.innerHTML = rows.length
                        ? rows.map(m => `<button type="button" class="list-group-item list-group-item-action medicine-option" data-id="${m.id}" data-name="${esc(m.name)}"><strong>${esc(m.name)}</strong> <small class="text-muted">${esc(m.product_code || '')}${m.generic_name ? ' — ' + esc(m.generic_name) : ''}</small></button>`).join('')
                        : '<div class="list-group-item text-muted">No active medicine found.</div>';
                    positionMedicineResults(box, e.target);
                    box.style.display = 'block';
                })
                .catch(() => box.style.display = 'none');
        }

        calculate();
    });

    body.addEventListener('click', async e => {
        const medicine = e.target.closest('.medicine-option');

        if (medicine) {
            const row = medicine.closest('tr');
            row.querySelector('.medicine-id').value = medicine.dataset.id;
            row.querySelector('.medicine-search').value = medicine.dataset.name;
            row.querySelector('.selected-medicine').textContent = '✓ Medicine selected';
            row.querySelector('.medicine-results').style.display = 'none';
            await loadBatches(row, medicine.dataset.id);
            return;
        }

        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    body.addEventListener('change', e => {
        if (e.target.classList.contains('batch-select')) {
            applyBatch(e.target.closest('tr'), e.target.selectedOptions[0]);
        }
        calculate();
    });

    function lineDiscount() {
        let total = 0;
        body.querySelectorAll('.line-discount').forEach(x => {
            total += Math.max(0, +x.value || 0);
        });
        return total;
    }

    function calculate() {
        let subtotal = 0;

        body.querySelectorAll('tr').forEach(row => {
            const q = +(row.querySelector('.qty')?.value || 0);
            const p = +(row.querySelector('.unit-price')?.value || 0);
            const d = +(row.querySelector('.line-discount')?.value || 0);
            const total = Math.max(0, q * p - d);

            subtotal += q * p;
            row.querySelector('.line-total').value = total.toFixed(2);
        });

        const lineDiscountAmount = lineDiscount();
        const invoiceDiscountPercent = Math.min(100, Math.max(0,
            +(document.getElementById('invoice_discount').value || 0)
        ));
        const afterLineDiscount = Math.max(0, subtotal - lineDiscountAmount);
        const invoiceDiscountAmount = afterLineDiscount * (invoiceDiscountPercent / 100);
        const grandTotal = Math.max(0, afterLineDiscount - invoiceDiscountAmount);
        const paid = Math.max(0, +(document.getElementById('paid_amount').value || 0));

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
        document.getElementById('due_amount').value = Math.max(0, grandTotal - paid).toFixed(2);
    }

    function repositionMedicineResults() {
        body.querySelectorAll('.medicine-results').forEach(box => {
            if (box.style.display !== 'none') {
                const input = box.closest('td')?.querySelector('.medicine-search');
                if (input) positionMedicineResults(box, input);
            }
        });
    }

    window.addEventListener('resize', repositionMedicineResults);
    window.addEventListener('scroll', repositionMedicineResults, true);

    document.getElementById('addItem').addEventListener('click', () => addRow());
    document.getElementById('invoice_discount').addEventListener('input', calculate);
    document.getElementById('paid_amount').addEventListener('input', calculate);

    form.addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    form.addEventListener('submit', e => {
        if (!cid.value) {
            e.preventDefault();
            alert('Please select a valid customer from the search results.');
            return;
        }

        if (!body.querySelector('tr')) {
            e.preventDefault();
            alert('Add at least one sale item.');
            return;
        }

        let valid = true;
        body.querySelectorAll('tr').forEach(row => {
            if (!row.querySelector('.medicine-id').value || !row.querySelector('.batch-select').value) {
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Select a valid medicine and batch for every item.');
        }
    });

    const oldItems = {{ Illuminate\Support\Js::from(
        $oldItems->map(function ($item) {
            return [
                'medicine_id' => $item->medicine_id,
                'medicine_name' => $item->medicine?->name,
                'batch_id' => $item->batch_id,
                'batch_no' => $item->batch?->batch_no,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_discount' => (float) $item->discount,
            ];
        })->values()
    ) }};

    oldItems.forEach(item => addRow(item));

    const isEdit = {{ $isEdit ? 'true' : 'false' }};
    if (!isEdit) addRow();

    calculate();
})();
</script>
@endpush
