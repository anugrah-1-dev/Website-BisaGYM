<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\GymPackage;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with('gymPackages')->get();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        $packages = GymPackage::where('is_active', true)->get();
        return view('discounts.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'gym_packages' => 'nullable|array',
            'gym_packages.*' => 'exists:gym_packages,id'
        ]);

        $discount = Discount::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('gym_packages')) {
            $discount->gymPackages()->sync($request->gym_packages);
        }

        return redirect()->route('discounts.index')->with('success', 'Promo diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount)
    {
        $packages = GymPackage::where('is_active', true)->get();
        return view('discounts.edit', compact('discount', 'packages'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'gym_packages' => 'nullable|array',
            'gym_packages.*' => 'exists:gym_packages,id'
        ]);

        $discount->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'is_active' => $request->has('is_active'),
        ]);

        $discount->gymPackages()->sync($request->gym_packages ?? []);

        return redirect()->route('discounts.index')->with('success', 'Promo diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'Promo diskon berhasil dihapus.');
    }
}
