<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        table { border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #eeeeee; font-weight: bold; }
    </style>
</head>
<body>
    <h3>Laporan Peminjaman Alat</h3>
    <p>
        @if(!empty($filters['start_date']) && !empty($filters['end_date']))
            Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }} |
        @endif
        Status: {{ !empty($filters['status']) ? ucfirst($filters['status']) : 'Semua' }} |
        Dicetak: {{ now()->format('d-m-Y H:i') }} WIB
    </p>

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
                    <td>{{ $laporan->tgl_pinjam?->format('d-m-Y H:i') }}</td>
                    <td>{{ $laporan->tgl_kembali_plan?->format('d-m-Y H:i') }}</td>
                    <td>
                        @foreach($laporan->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }}x)@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>{{ $laporan->status }}</td>
                    <td>{{ $laporan->pengembalian?->tgl_kembali?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $laporan->pengembalian->denda ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>Total data: {{ $laporans->count() }}</p>
</body>
</html>
