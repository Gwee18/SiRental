<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SiRental</title>

    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #ffffff;
        }

        .header {
            background: #085041;
            color: #ffffff;
            padding: 24px 44px 22px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
        }

        .content {
            padding: 24px 44px 36px;
        }

        .period {
            background: #e8f5f0;
            border: 1px solid #68dbae;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 22px;
            color: #085041;
            font-weight: bold;
        }

        .period-note {
            display: block;
            margin-top: 4px;
            color: #4b766d;
            font-size: 9px;
            font-weight: normal;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 26px;
        }

        .summary-table td {
            width: 25%;
            padding: 0;
            vertical-align: top;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            min-height: 52px;
        }

        .summary-card .label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 13px;
            font-weight: bold;
            color: #00372c;
        }

        .summary-card.green {
            background: #085041;
            border-color: #085041;
        }

        .summary-card.green .label {
            color: #cde5dc;
        }

        .summary-card.green .value {
            color: #ffffff;
        }

        .summary-card.red .value {
            color: #ef4444;
        }

        .table-wrapper {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 36px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #f9fafb;
        }

        .data-table th {
            padding: 9px 8px;
            text-align: left;
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table td {
            padding: 9px 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 9px;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tfoot tr {
            background: #f3f4f6;
        }

        .data-table tfoot td {
            font-weight: bold;
            border-top: 2px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .empty-row {
            text-align: center;
            color: #9ca3af;
            padding: 24px;
        }

        .footer {
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SiRental — Laporan Transaksi</h1>
        <p>Rental Alat Pendakian Surabaya &bull; sirental.ofc@gmail.com</p>
    </div>

    <div class="content">
        <div class="period">
            Periode Pelunasan:
            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F') }}
            {{ $tahun }}
            &nbsp;&bull;&nbsp;
            Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB

            <span class="period-note">
                Pendapatan dihitung dari nominal pembayaran yang benar-benar sudah diterima.
            </span>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-card">
                        <div class="label">Transaksi Lunas</div>
                        <div class="value">{{ $transaksi->count() }}</div>
                    </div>
                </td>

                <td>
                    <div class="summary-card">
                        <div class="label">Pendapatan Sewa</div>
                        <div class="value">
                            Rp {{ number_format($totalSewa, 0, ',', '.') }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="summary-card red">
                        <div class="label">Denda Terkumpul</div>
                        <div class="value">
                            Rp {{ number_format($totalDenda, 0, ',', '.') }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="summary-card green">
                        <div class="label">Total Diterima</div>
                        <div class="value">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Lunas</th>
                        <th class="text-right">Total Sewa</th>
                        <th class="text-right">Denda</th>
                        <th class="text-right">Total Dibayar</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksi as $i => $trx)
                        @php
                            $tanggalLunas = $trx->denda_dibayar_pada
                                ?? $trx->dibayar_pada
                                ?? $trx->updated_at;
                        @endphp

                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="nowrap">{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->customer->nama_lengkap ?? '-' }}</td>
                            <td class="nowrap">
                                {{ $tanggalLunas->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="text-right nowrap">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-right nowrap">
                                {{ $trx->total_denda > 0 ? 'Rp ' . number_format($trx->total_denda, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right nowrap">
                                Rp {{ number_format($trx->total_dibayar, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">
                                Tidak ada transaksi lunas pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($transaksi->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="4">TOTAL</td>
                            <td class="text-right nowrap">
                                Rp {{ number_format($totalSewa, 0, ',', '.') }}
                            </td>
                            <td class="text-right nowrap">
                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                            </td>
                            <td class="text-right nowrap">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="footer">
            Dokumen ini dibuat otomatis oleh sistem SiRental &bull;
            © {{ now()->year }} SiRental. All rights reserved.
        </div>
    </div>
</body>
</html>
