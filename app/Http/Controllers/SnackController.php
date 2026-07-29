<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use Illuminate\Http\Request;

class SnackController extends Controller
{
    public function index()
    {
        $snacks = Snack::orderBy('category')->orderBy('name')->get();
        return view('snacks.index', compact('snacks'));
    }

    public function create()
    {
        return view('snacks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'snack_code' => 'required|string|unique:snacks,snack_code',
            'category' => 'required|string',
            'capital_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $snack = Snack::create($request->all());

        \App\Models\ActivityLog::log('CREATE', 'Kantin', "Menambahkan produk baru: {$snack->name} (Stok: {$snack->stock})");

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(Snack $snack) { return redirect()->route('snacks.edit', $snack); }

    public function edit(Snack $snack)
    {
        return view('snacks.edit', compact('snack'));
    }

    public function update(Request $request, Snack $snack)
    {
        $request->validate([
            'name' => 'required|string',
            'snack_code' => 'required|string|unique:snacks,snack_code,' . $snack->id,
            'category' => 'required|string',
            'capital_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $oldStock = $snack->stock;
        $oldPrice = $snack->selling_price;
        $snack->update($request->all());

        $desc = "Memperbarui produk {$snack->name}";
        if ($oldStock != $snack->stock) $desc .= " (Stok dari $oldStock ke {$snack->stock})";
        if ($oldPrice != $snack->selling_price) $desc .= " (Harga dari Rp " . number_format($oldPrice, 0, ',', '.') . " ke Rp " . number_format($snack->selling_price, 0, ',', '.') . ")";

        \App\Models\ActivityLog::log('UPDATE', 'Kantin', $desc);

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Snack $snack)
    {
        $name = $snack->name;
        $snack->delete();

        \App\Models\ActivityLog::log('DELETE', 'Kantin', "Menghapus produk: {$name}");

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil dihapus!');
    }
}
