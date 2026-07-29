<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\MemberTransaction;
use App\Models\SnackTransaction;

class TransactionsExport implements FromView, ShouldAutoSize, WithDrawings
{
    protected $type;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($type, $dateFrom, $dateTo)
    {
        $this->type = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        if ($this->type === 'snack') {
            $transactions = SnackTransaction::with(['user', 'details.snack'])
                ->whereBetween('transaction_date', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
                ->orderBy('transaction_date')
                ->get();
        } else {
            $transactions = MemberTransaction::with(['member', 'package', 'user'])
                ->whereBetween('transaction_date', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
                ->orderBy('transaction_date')
                ->get();
        }

        return view('exports.transactions', [
            'type' => $this->type,
            'transactions' => $transactions,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo
        ]);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('BisaGym Logo');
        // Let's assume there's a logo in public folder. I will use a dummy or standard icon path.
        // I will check if public/logo.png exists, if not, I'll skip it or use a default image.
        // Wait, Drawing requires a real file path. Let's just create a simple image if it doesn't exist, or use a known one.
        // Actually, if we don't know the exact logo path, it will crash. I'll use public/favicon.ico or similar if logo is missing.
        // Let's create a placeholder or use drawing if exists.
        
        $logoPath = public_path('logo.png');
        if (!file_exists($logoPath)) {
            // we will create a dummy logo using gd just to not crash, or return empty array.
            return [];
        }

        $drawing->setPath($logoPath);
        $drawing->setHeight(70);
        $drawing->setCoordinates('B2');

        return [$drawing];
    }
}
