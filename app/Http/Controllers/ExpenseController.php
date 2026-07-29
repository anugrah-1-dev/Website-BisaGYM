<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();
        return view('expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $expense = Expense::create([
            'date' => $request->date,
            'category' => 'Operasional', // User wanted it simple
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        \App\Models\ActivityLog::log('CREATE', 'Pengeluaran Operasional', "Mencatat pengeluaran: Rp " . number_format($expense->amount, 0, ',', '.') . " untuk {$expense->description}");

        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan!');
    }

    public function destroy(Expense $expense)
    {
        $desc = "Menghapus data pengeluaran: Rp " . number_format($expense->amount, 0, ',', '.') . " untuk {$expense->description}";
        $expense->delete();
        
        \App\Models\ActivityLog::log('DELETE', 'Pengeluaran Operasional', $desc);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}
