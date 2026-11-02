{{-- Formulir Baris Item Dinamis & Terkalkulasi Otomatis --}}
@php
    $rows = old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => '']]);
@endphp

<div class="card border border-light-subtle rounded-3 overflow-hidden mb-3">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0" id="items-table">
            <thead>
                <tr>
                    <th class="ps-3" style="min-width: 280px;">Produk & Ketersediaan Stok</th>
                    <th width="140" class="text-center">Kuantitas</th>
                    @if ($withCost)
                        <th width="170" class="text-end">Harga Beli Satuan</th>
                    @else
                        <th width="160" class="text-end">Harga Jual Satuan</th>
                    @endif
                    <th width="170" class="text-end">Subtotal Estimasi</th>
                    <th width="60" class="text-center pe-3"></th>
                </tr>
            </thead>
            <tbody id="items-body">
            @foreach ($rows as $i => $row)
                <tr class="item-row">
                    <td class="ps-3">
                        <select name="items[{{ $i }}][product_id]" class="form-select product-select" required>
                            <option value="" data-price="0" data-stock="0">-- Pilih Produk --</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}"
                                        data-price="{{ $withCost ? $p->purchase_price : $p->selling_price }}"
                                        data-stock="{{ $p->stock }}"
                                        @selected(($row['product_id'] ?? '') == $p->id)>
                                    {{ $p->sku }} &mdash; {{ $p->name }}
                                    (Stok: {{ $p->stock }} | Rp {{ number_format($withCost ? $p->purchase_price : $p->selling_price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" min="1" name="items[{{ $i }}][quantity]"
                                   value="{{ $row['quantity'] ?? 1 }}"
                                   class="form-control text-center qty-input" required>
                        </div>
                    </td>
                    @if ($withCost)
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" min="0" step="0.01" name="items[{{ $i }}][unit_cost]"
                                       value="{{ $row['unit_cost'] ?? '' }}"
                                       class="form-control text-end cost-input font-mono border-start-0" required
                                       placeholder="0">
                            </div>
                        </td>
                    @else
                        <td class="text-end font-mono price-display">
                            Rp 0
                        </td>
                    @endif
                    <td class="text-end font-mono fw-bold subtotal-display text-dark">
                        Rp 0
                    </td>
                    <td class="text-center pe-3">
                        <button type="button" class="btn btn-sm btn-outline-danger p-1 rounded-circle remove-row" title="Hapus baris" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-x-lg" style="font-size: 0.75rem;"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="bg-light border-top">
                <tr>
                    <td colspan="{{ $withCost ? 3 : 2 }}" class="ps-3 py-3">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="add-row">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Barang
                        </button>
                    </td>
                    <td class="text-end fw-bold py-3 fs-6">Total Transaksi:</td>
                    <td class="text-end pe-3 py-3 font-mono fw-bold fs-5 text-primary" id="grand-total-display">
                        Rp 0
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const isWithCost = {{ $withCost ? 'true' : 'false' }};
    const body = document.getElementById('items-body');

    function formatRupiah(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num || 0);
    }

    function calculateRowSubtotal(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const subtotalEl = row.querySelector('.subtotal-display');

        const qty = parseFloat(qtyInput?.value) || 0;
        let price = 0;

        if (isWithCost) {
            const costInput = row.querySelector('.cost-input');
            price = parseFloat(costInput?.value) || 0;
        } else {
            const option = select?.selectedOptions[0];
            price = parseFloat(option?.dataset.price) || 0;
            const priceEl = row.querySelector('.price-display');
            if (priceEl) priceEl.innerText = formatRupiah(price);
        }

        const subtotal = qty * price;
        if (subtotalEl) subtotalEl.innerText = formatRupiah(subtotal);
        return subtotal;
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#items-body .item-row').forEach(row => {
            total += calculateRowSubtotal(row);
        });
        const grandTotalEl = document.getElementById('grand-total-display');
        if (grandTotalEl) grandTotalEl.innerText = formatRupiah(total);
    }

    // Event Delegations
    body.addEventListener('change', e => {
        if (e.target.classList.contains('product-select')) {
            const option = e.target.selectedOptions[0];
            const defaultPrice = option?.dataset.price || 0;
            const row = e.target.closest('tr');
            if (isWithCost) {
                const costInput = row.querySelector('.cost-input');
                if (costInput && (!costInput.value || costInput.value == 0)) {
                    costInput.value = defaultPrice;
                }
            }
            calculateGrandTotal();
        }
    });

    body.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('cost-input')) {
            calculateGrandTotal();
        }
    });

    // Tambah Baris
    let index = body.rows.length;
    document.getElementById('add-row').addEventListener('click', () => {
        const clone = body.rows[0].cloneNode(true);
        clone.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace(/items\[\d+\]/, 'items[' + index + ']');
            if (el.tagName === 'SELECT') {
                el.value = '';
            } else if (el.classList.contains('qty-input')) {
                el.value = 1;
            } else {
                el.value = '';
            }
        });
        const subtotalEl = clone.querySelector('.subtotal-display');
        if (subtotalEl) subtotalEl.innerText = 'Rp 0';
        const priceEl = clone.querySelector('.price-display');
        if (priceEl) priceEl.innerText = 'Rp 0';

        body.appendChild(clone);
        index++;
        calculateGrandTotal();
    });

    // Hapus Baris
    body.addEventListener('click', e => {
        const removeBtn = e.target.closest('.remove-row');
        if (removeBtn && body.rows.length > 1) {
            removeBtn.closest('tr').remove();
            calculateGrandTotal();
        }
    });

    // Initial Calculation
    document.addEventListener('DOMContentLoaded', () => {
        calculateGrandTotal();
    });
</script>
@endpush
