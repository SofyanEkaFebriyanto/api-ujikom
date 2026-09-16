<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h2 { text-align: center; margin: 0 0 4px 0; }
        .subtitle { text-align: center; color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #eee; }
        .footer { margin-top: 10px; text-align: right; color: #555; }
    </style>
</head>
<body>
    <h2>Laporan Peminjaman Alat</h2>
    <div class="subtitle">
        @if(!empty($filters['start_date']) && !empty($filters['end_date']))
            Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }} &nbsp;|&nbsp;
        @endif
        Status: {{ !empty($filters['status']) ? ucfirst($filters['status']) : 'Semua' }} &nbsp;|&nbsp;
        Dicetak: {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Detail Alat</th>
                <th>Status</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $index => $laporan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $laporan->user->name ?? '-' }}</td>
                    <td>{{ $laporan->tgl_pinjam }}</td>
                    <td>{{ $laporan->tgl_kembali_plan }}</td>
                    <td>
                        @foreach($laporan->detailPinjam as $detail)
                            <div>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }}x)</div>
                        @endforeach
                    </td>
                    <td>{{ $laporan->status }}</td>
                    <td>{{ $laporan->pengembalian->tgl_kembali ?? '-' }}</td>
                    <td>Rp {{ number_format($laporan->pengembalian->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Total data: {{ $laporans->count() }}</div>
</body>
</html>
