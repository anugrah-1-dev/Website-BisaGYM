<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $vip_id = $request->get('vip_id');
        $member = null;
        $unpaidTransaction = null;

        if ($vip_id) {
            $member = \App\Models\Member::where('member_id', $vip_id)->first();
            if ($member) {
                // Cari transaksi unpaid untuk member ini
                $unpaidTransaction = \App\Models\MemberTransaction::with('package')
                    ->where('member_id', $member->id)
                    ->where('payment_status', 'unpaid')
                    ->latest()
                    ->first();
            }
        }

        return view('cashier.member', compact('vip_id', 'member', 'unpaidTransaction'));
    }

    public function pay(Request $request, \App\Models\MemberTransaction $transaction)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris,debit,gratis',
        ]);

        if ($transaction->payment_status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $updateData = [
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'transaction_date' => now(), 
        ];

        if ($request->payment_method === 'gratis') {
            $updateData['amount'] = 0;
        }

        $transaction->update($updateData);

        $member = $transaction->member;

        if ($transaction->transaction_type === 'renewal') {
            $package = $transaction->package;
            $now = now();
            $baseDate = ($member->status === 'active' && \Carbon\Carbon::parse($member->expiry_date)->isFuture())
                ? \Carbon\Carbon::parse($member->expiry_date)
                : $now;

            $newExpiry = $baseDate->copy();
            if ($package->duration_unit === 'hari')       $newExpiry->addDays($package->duration);
            elseif ($package->duration_unit === 'bulan')  $newExpiry->addMonths($package->duration);
            elseif ($package->duration_unit === 'tahun')  $newExpiry->addYears($package->duration);

            $member->update([
                'expiry_date'     => $newExpiry,
                'status'          => 'active',
                'extension_count' => $member->extension_count + 1,
            ]);

            // Auto-extend linked member if it is a couple package
            if ($package->max_members >= 2 && $member->linked_member_id) {
                $linkedMember = $member->linkedMember;
                if ($linkedMember) {
                    $linkedBaseDate = ($linkedMember->status === 'active' && \Carbon\Carbon::parse($linkedMember->expiry_date)->isFuture())
                        ? \Carbon\Carbon::parse($linkedMember->expiry_date)
                        : $now;
                    
                    $linkedNewExpiry = $linkedBaseDate->copy();
                    if ($package->duration_unit === 'hari')       $linkedNewExpiry->addDays($package->duration);
                    elseif ($package->duration_unit === 'bulan')  $linkedNewExpiry->addMonths($package->duration);
                    elseif ($package->duration_unit === 'tahun')  $linkedNewExpiry->addYears($package->duration);
                    
                    $linkedMember->update([
                        'expiry_date'     => $linkedNewExpiry,
                        'status'          => 'active',
                        'extension_count' => $linkedMember->extension_count + 1,
                    ]);
                }
            }
        } else {
            // Pendaftaran baru
            // Aktifkan member jika hari ini sudah mencapai/melewati tanggal aktivasi
            if (now()->startOfDay()->greaterThanOrEqualTo(\Carbon\Carbon::parse($member->activation_date)->startOfDay())) {
                $member->update(['status' => 'active']);
            }

            // Auto-activate linked member for new registration if couple package
            $package = $transaction->package;
            if ($package && $package->max_members >= 2 && $member->linked_member_id) {
                $linkedMember = $member->linkedMember;
                if ($linkedMember && now()->startOfDay()->greaterThanOrEqualTo(\Carbon\Carbon::parse($linkedMember->activation_date)->startOfDay())) {
                    $linkedMember->update(['status' => 'active']);
                }
            }
        }

        \App\Models\ActivityLog::log('UPDATE', 'Kasir', "Memproses pembayaran member: {$member->name} (Rp " . number_format($transaction->amount, 0, ',', '.') . ") - LUNAS");

        return redirect()->route('cashier.member')->with('success', 'Pembayaran sebesar Rp ' . number_format($transaction->amount, 0, ',', '.') . ' berhasil diproses (LUNAS).');
    }

    public function payNonMember(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'payment_method' => 'required|in:cash,transfer,qris,debit,gratis',
        ]);

        $package = \App\Models\GymPackage::where('category', 'non-member')->where('is_active', true)->first();
        if (!$package) {
            return back()->with('error', 'Paket Non-Member tidak ditemukan atau belum aktif.');
        }

        $now = now();
        $vipId = 'NM-' . $now->format('Ymd-His') . '-' . rand(1000, 9999);

        // 1. Buat Member (Tipe: Non Member)
        $member = \App\Models\Member::create([
            'member_id'         => $vipId,
            'member_type'       => 'Non Member',
            'name'              => $request->name,
            'phone'             => $request->phone,
            'gender'            => 'L', // default (nullable jika disesuaikan)
            'registration_date' => $now,
            'activation_date'   => $now,
            'expiry_date'       => $now, // Harian (berakhir hari ini)
            'status'            => 'active',
        ]);

        $amount = ($request->payment_method === 'gratis') ? 0 : $package->price;

        // 2. Buat Transaksi Lunas
        \App\Models\MemberTransaction::create([
            'transaction_code'    => 'TRX-' . time() . '-' . rand(100, 999),
            'member_id'           => $member->id,
            'gym_package_id'      => $package->id,
            'user_id'             => \Illuminate\Support\Facades\Auth::id(),
            'amount'              => $amount,
            'discount_percentage' => 0,
            'admin_fee'           => 0,
            'transaction_date'    => $now,
            'transaction_type'    => 'new',
            'payment_status'      => 'paid',
            'payment_method'      => $request->payment_method,
        ]);

        // 3. Otomatis Absen Masuk
        \App\Models\MemberAttendance::create([
            'member_id'       => $member->id,
            'user_id'         => \Illuminate\Support\Facades\Auth::id(),
            'attendance_time' => $now,
        ]);

        \App\Models\ActivityLog::log('CREATE', 'Kasir', "Memproses pembayaran Non-Member: {$request->name} (Rp " . number_format($package->price, 0, ',', '.') . ")");

        return redirect()->route('cashier.member')->with('success', 'Pembayaran Non-Member Rp ' . number_format($package->price, 0, ',', '.') . ' berhasil. Pelanggan bisa langsung latihan!');
    }
}
