<?php

namespace App\Http\Controllers;

use App\Models\GymPackage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class GymPackageController extends Controller
{
    public function index()
    {
        $memberPackages = GymPackage::whereIn('category', ['member', 'couple'])->get();
        $nonMemberPackages = GymPackage::where('category', 'non-member')->get();
        return view('packages.index', compact('memberPackages', 'nonMemberPackages'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'admin_fee'           => 'required|numeric|min:0',
            'duration'            => 'required|integer|min:1',
            'duration_unit'       => 'required|in:hari,minggu,bulan,tahun',
            'category'            => 'required|in:member,non-member,couple',
            'max_members'         => 'required|integer|min:1|max:10',
        ]);

        $gymPackage = GymPackage::create([
            'name'                => $request->name,
            'price'               => $request->price,
            'admin_fee'           => $request->admin_fee,
            'duration'            => $request->duration,
            'duration_unit'       => $request->duration_unit,
            'category'            => $request->category,
            'max_members'         => $request->max_members,
            'is_active'           => $request->has('is_active'),
        ]);

        ActivityLog::log('CREATE', 'Gym Package', "Menambahkan paket gym baru: {$gymPackage->name}");

        return redirect()->route('gym-packages.index')->with('success', 'Paket baru berhasil ditambahkan!');
    }

    public function storeNonMember(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $pkg = GymPackage::create([
            'name'          => $request->name,
            'price'         => $request->price,
            'category'      => 'non-member',
            'duration'      => 1,
            'duration_unit' => 'hari',
            'admin_fee'     => 0,
            'max_members'   => 1,
            'is_active'     => $request->has('is_active'),
        ]);

        ActivityLog::log('CREATE', 'Paket Gym', "Menambahkan tarif kunjungan harian: {$pkg->name} (Rp " . number_format($pkg->price, 0, ',', '.') . ")");

        return redirect()->route('gym-packages.index', ['tab' => 'non-member'])
            ->with('success', 'Tarif kunjungan harian berhasil ditambahkan!');
    }

    public function edit(GymPackage $gymPackage)
    {
        return view('packages.edit', compact('gymPackage'));
    }

    public function update(Request $request, GymPackage $gymPackage)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'admin_fee'           => 'required|numeric|min:0',
            'duration'            => 'required|integer|min:1',
            'duration_unit'       => 'required|in:hari,minggu,bulan,tahun',
            'category'            => 'required|in:member,non-member,couple',
            'max_members'         => 'required|integer|min:1|max:10',
        ]);

        $oldPrice = $gymPackage->price;

        $gymPackage->update([
            'name'                => $request->name,
            'price'               => $request->price,
            'admin_fee'           => $request->admin_fee,
            'duration'            => $request->duration,
            'duration_unit'       => $request->duration_unit,
            'category'            => $request->category,
            'max_members'         => $request->max_members,
            'is_active'           => $request->has('is_active'),
        ]);

        ActivityLog::log('UPDATE', 'Paket Gym', "Mengubah paket gym {$gymPackage->name}" . ($oldPrice != $gymPackage->price ? " (Harga diubah dari Rp " . number_format($oldPrice, 0, ',', '.') . " ke Rp " . number_format($gymPackage->price, 0, ',', '.') . ")" : ""));

        return redirect()->route('gym-packages.index')->with('success', 'Paket berhasil diperbarui!');
    }

    public function updateNonMember(Request $request, GymPackage $gymPackage)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $oldPrice = $gymPackage->price;

        $gymPackage->update([
            'name'      => $request->name,
            'price'     => $request->price,
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::log('UPDATE', 'Paket Gym', "Mengubah tarif kunjungan harian {$gymPackage->name}" . ($oldPrice != $gymPackage->price ? " (Rp " . number_format($oldPrice, 0, ',', '.') . " → Rp " . number_format($gymPackage->price, 0, ',', '.') . ")" : ""));

        return redirect()->route('gym-packages.index', ['tab' => 'non-member'])
            ->with('success', 'Tarif kunjungan harian berhasil diperbarui!');
    }

    public function destroy(GymPackage $gymPackage)
    {
        $name     = $gymPackage->name;
        $category = $gymPackage->category;
        $gymPackage->delete();

        ActivityLog::log('DELETE', 'Paket Gym', "Menghapus paket gym: {$name}");

        $tab = $category === 'non-member' ? 'non-member' : 'member';
        return redirect()->route('gym-packages.index', ['tab' => $tab])
            ->with('success', 'Paket berhasil dihapus!');
    }
}
