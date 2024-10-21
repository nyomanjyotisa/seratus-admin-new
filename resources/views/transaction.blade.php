<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report for {{ $month }}/{{ $year }}</title>
    <style>
        @font-face {
            font-family: 'Arial';
            src: url('path/to/arial.ttf') format('truetype');
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
        }
        th {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Laporan Transaksi {{ $month }}/{{ $year }}</h1>

    <p>Total Pemasukan: <strong>Rp{{ number_format($total_pemasukan, 0, '.', ',') }}</strong></p>
    <p>Total Pengeluaran: <strong>Rp{{ number_format($total_pengeluaran, 0, '.', ',') }}</strong></p>
    <p>Laba: <strong>Rp{{ number_format($laba, 0, '.', ',') }}</strong></p>
    <p>Persentase Laba: <strong>{{ number_format($persentase_laba_total, 2, '.', ',') }}%</strong></p>

    <h3>Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Unique Code</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Laba</th>
                <th>Sumber</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td> 
                    <td>{{ $transaction->unique_code ?: '-' }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>
                        Penjualan - Rp{{ number_format($transaction->sales_total, 0, '.', ',') }}<br>
                        Produksi - Rp{{ number_format($transaction->productions_total, 0, '.', ',') }}<br>
                        Pengeluaran lain - Rp{{ number_format($transaction->expenses_total, 0, '.', ',') }}<br>
                        Pemasukan lain - Rp{{ number_format($transaction->other_incomes_total, 0, '.', ',') }}
                    </td>
                    <td>
                        Rp{{ number_format($transaction->total) }}<br>
                        <p style="margin: 0; color: {{ ($transaction->persentase_laba > 60 || $transaction->persentase_laba < 30) ? 'red' : 'black' }}">
                            {{ $transaction->persentase_laba ?? '-' }}%
                        </p>
                    </td>
                    <td>{{ $transaction->source }}</td>
                    <td>{{ $transaction->date }}</td>
                </tr>
                <tr class="nested">
                    <td colspan="7">
                        <strong>Detail Transaksi:</strong>
                        <ul>
                            <li>Data Transaksi Penjualan:
                                <ul>
                                    @foreach ($transaction->sales as $sale)
                                        <li>Rp {{ number_format($sale->amount, 0, '.', ',') }} ({{ $sale->description }})</li>
                                    @endforeach
                                </ul>
                            </li>
                            <br>
                            <li>Data Transaksi Produksi:
                                <ul>
                                    @foreach ($transaction->productions as $production)
                                        <li>Rp {{ number_format($production->amount, 0, '.', ',') }} ({{ $production->description }})</li>
                                    @endforeach
                                </ul>
                            </li>
                            <br>
                            <li>Data Pengeluaran Lainnya:
                                <ul>
                                    @foreach ($transaction->expenses as $expense)
                                        <li>Rp {{ number_format($expense->amount, 0, '.', ',') }} ({{ $expense->description }})</li>
                                    @endforeach
                                </ul>
                            </li>
                            <br>
                            <li>Data Pendapatan Lainnya:
                                <ul>
                                    @foreach ($transaction->otherIncomes as $income)
                                        <li>Rp {{ number_format($income->amount, 0, '.', ',') }} ({{ $income->description }})</li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Pengeluaran Lainnya</h3>
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
            <tr>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->date }}</td>
                <td>Rp{{ number_format($expense->amount, 0, '.', ',') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Tidak ada data pengeluaran lainnya</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Pendapatan Lainnya</h3>
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($otherIncomes as $income)
            <tr>
                <td>{{ $income->description }}</td>
                <td>{{ $income->date }}</td>
                <td>Rp{{ number_format($income->amount, 0, '.', ',') }}</td>    
            </tr>
            @empty
            <tr>
                <td colspan="3">Tidak ada data pendapatan lainnya</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>