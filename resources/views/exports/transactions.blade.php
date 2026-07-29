<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <tr>
            <!-- Empty row for Logo spacing (Rows 1-4) -->
            <td colspan="5"></td>
        </tr>
        <tr><td colspan="5"></td></tr>
        <tr><td colspan="5"></td></tr>
        <tr><td colspan="5"></td></tr>
        
        <tr>
            <td colspan="5" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN TRANSAKSI {{ strtoupper($type) }}
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center; font-size: 12px; font-style: italic;">
                Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            </td>
        </tr>
        <tr><td colspan="5"></td></tr>

        @if($type === 'member')
        <tr>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 20px;">No</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 150px;">Tanggal</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 200px;">Nama Member</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 200px;">Paket</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 150px;">Total (Rp)</th>
        </tr>
        @foreach($transactions as $idx => $trx)
        <tr>
            <td style="text-align: center; border: 1px solid #000000;">{{ $idx + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d-m-Y H:i') }}</td>
            <td style="border: 1px solid #000000;">{{ $trx->member->name ?? '-' }} ({{ $trx->member->member_id ?? '' }})</td>
            <td style="border: 1px solid #000000;">{{ $trx->package->name ?? '-' }}</td>
            <td style="text-align: right; border: 1px solid #000000;">{{ $trx->amount }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="font-weight: bold; text-align: right; border: 1px solid #000000;">TOTAL PENDAPATAN</td>
            <td style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #E0FF00;">{{ $transactions->where('payment_status', 'paid')->sum('amount') }}</td>
        </tr>
        @else
        <tr>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 20px;">No</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 150px;">Tanggal</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 300px;">Item Terjual</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 150px;">Kasir</th>
            <th style="font-weight: bold; background-color: #2F3640; color: #ffffff; text-align: center; border: 1px solid #000000; width: 150px;">Total (Rp)</th>
        </tr>
        @foreach($transactions as $idx => $trx)
        <tr>
            <td style="text-align: center; border: 1px solid #000000;">{{ $idx + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d-m-Y H:i') }}</td>
            <td style="border: 1px solid #000000;">
                @foreach($trx->details as $d)
                    {{ $d->snack->name ?? '?' }} (x{{ $d->quantity }})
                @endforeach
            </td>
            <td style="border: 1px solid #000000;">{{ $trx->user->name ?? '-' }}</td>
            <td style="text-align: right; border: 1px solid #000000;">{{ $trx->total_amount }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="font-weight: bold; text-align: right; border: 1px solid #000000;">TOTAL PENDAPATAN</td>
            <td style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #E0FF00;">{{ $transactions->sum('total_amount') }}</td>
        </tr>
        @endif
    </table>
</body>
</html>
