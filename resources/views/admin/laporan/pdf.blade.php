<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SiRental</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: white; }

        .page { padding: 36px 44px; }

        .header { background: #085041; color: white; padding: 20px 24px; margin-bottom: 20px; border-radius: 8px; }
        .header h1 { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.8; }

        .period { background: #e8f5f0; border: 1px solid #68dbae; padding: 10px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 12px; color: #085041; font-weight: bold; }

        .summary { display: flex; gap: 12px; margin-bottom: 24px; }
        .summary-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; }
        .summary-card .label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .summary-card .value { font-size: 16px; font-weight: bold; color: #00372c; }
        .summary-card.green { background: #085041; border-color: #085041; }
        .summary-card.green .label { color: rgba(255,255,255,0.7); }
        .summary-card.green .value { color: white; }

        .table-wrapper { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 36px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f9fafb; }
        th { padding: 10px 12px; text-align: left; font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb; }
        td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tbody tr:last-child td { border-bottom: none; }
        tfoot tr { background: #f3f4f6; }
        tfoot td { font-weight: bold; font-size: 11px; border-top: 2px solid #e5e7eb; }
        .text-right { text-align: right; }

        .footer { padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <h1>SiRental — Laporan Transaksi</h1>
        <p>Rental Alat Pendakian Surabaya &bull; sirental.ofc@gmail.com</p>
    </div>

    <div class="period">
        Periode: {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}
        &nbsp;&bull;&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

    <div class="summary">
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $transaksi->count() }}</div>
        </div>
        <div class="summary-card green">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Denda</div>
            <div class="value" style="color: #ef4444;">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th class="text-right">Total Sewa</th>
                    <th class="text-right">Denda</th>
                    <th class="text-right">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $i => $trx)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trx->kode_transaksi }}</td>
                    <td>{{ $trx->customer->nama_lengkap ?? '-' }}</td>
                    <td>{{ $trx->created_at->translatedFormat('d M Y') }}</td>
                    <td class="text-right">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $trx->total_denda > 0 ? 'Rp ' . number_format($trx->total_denda, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($trx->total_harga + $trx->total_denda, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#9ca3af; padding:24px;">Tidak ada transaksi selesai di periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($transaksi->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPendapatan + $totalDenda, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem SiRental &bull; © {{ now()->year }} SiRental. All rights reserved.
    </div>

</div>
</body>
</html>