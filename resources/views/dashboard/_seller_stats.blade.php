<div class="stats">
    <div class="stat">
        <small>Total pesanan</small>
        <strong>{{ number_format($totalOrders, 0, ',', '.') }}</strong>
    </div>
    <div class="stat">
        <small>Barang terjual</small>
        <strong>{{ number_format($totalUnits, 0, ',', '.') }}</strong>
    </div>
    <div class="stat">
        <small>Total pembelian (pendapatan toko)</small>
        <strong>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</strong>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <h3 style="margin-top:0">Persentase barang terjual per produk</h3>

    @if ($totalUnits > 0)
        <div style="max-width:420px;margin:0 auto">
            <canvas id="soldChart"></canvas>
        </div>
    @else
        <p>Belum ada barang yang terjual.</p>
    @endif
</div>

<div class="card" style="margin-bottom:20px">
    <h3 style="margin-top:0">Produk toko</h3>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Terjual</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name }}</td>
                <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->stock }}</td>
                <td>{{ $p->sold }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center">Belum ada produk</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <h3 style="margin-top:0">Riwayat pembelian terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($recentItems as $it)
            <tr>
                <td>{{ $it->created_at->format('d M Y H:i') }}</td>
                <td>{{ $it->order->user->name ?? '-' }}</td>
                <td>{{ $it->product_name }}</td>
                <td>{{ $it->quantity }}</td>
                <td>Rp{{ number_format($it->subtotal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center">Belum ada pembelian</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
@if ($totalUnits > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($chartLabels);
    const values = @json($chartValues);
    const palette = ['#059669', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0',
                     '#047857', '#065f46', '#0d9488', '#2dd4bf', '#99f6e4'];

    new Chart(document.getElementById('soldChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = ((ctx.parsed / total) * 100).toFixed(1);
                            return ctx.label + ': ' + ctx.parsed + ' terjual (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endif
@endpush