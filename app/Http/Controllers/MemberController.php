<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\GymPackage;
use App\Models\MemberTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Member::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
        }

        $members = $query->latest()->paginate(10);
        
        $stats = [
            'total'    => Member::count(),
            'active'   => Member::where('status', 'active')->count(),
            'inactive' => Member::where('status', '!=', 'active')->count(),
        ];

        return view('members.index', compact('members', 'stats'));
    }

    public function create()
    {
        $packages = GymPackage::with(['discounts' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();
        return view('members.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $package = GymPackage::findOrFail($request->package_id);
        $isCouple = $package->max_members >= 2;

        // Validation rules — member ke-2 wajib jika paket couple
        $rules = [
            'name'          => 'required|string|min:3',
            'place_of_birth'=> 'required|string',
            'date_of_birth' => 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d'),
            'gender'        => 'required|in:L,P',
            'nik'           => 'required|string|size:16|unique:members,nik',
            'job'           => 'nullable|string',
            'address'       => 'required|string',
            'phone'         => 'required|string',
            'email'         => 'required|email|unique:members,email',
            'photo_data'    => 'nullable|string',
            'package_id'    => 'required|exists:gym_packages,id',
            'discount_id'   => 'nullable|exists:discounts,id',
        ];

        if ($isCouple) {
            $rules['member2_name']          = 'required|string|min:3';
            $rules['member2_place_of_birth']= 'required|string';
            $rules['member2_date_of_birth'] = 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d');
            $rules['member2_gender']        = 'required|in:L,P';
            $rules['member2_nik']           = 'required|string|size:16|unique:members,nik';
            $rules['member2_job']           = 'nullable|string';
            $rules['member2_address']       = 'required|string';
            $rules['member2_phone']         = 'required|string';
            $rules['member2_email']         = 'required|email|unique:members,email';
            $rules['member2_photo_data']    = 'nullable|string';
        }

        $request->validate($rules, [
            'date_of_birth.before_or_equal'          => 'Umur member minimal 5 tahun.',
            'member2_date_of_birth.before_or_equal'  => 'Umur member ke-2 minimal 5 tahun.',
            'nik.size'                               => 'NIK harus tepat 16 digit angka.',
            'member2_nik.size'                       => 'NIK member ke-2 harus tepat 16 digit angka.',
        ]);

        // Gunakan tanggal aktivasi dari input, default ke hari ini jika kosong
        $now = now();
        $activationDateStr = $request->input('activation_date');
        $activationDate = $activationDateStr ? \Carbon\Carbon::parse($activationDateStr) : $now;

        $expiryDate = $activationDate->copy();
        if ($package->duration_unit === 'hari')       $expiryDate->addDays($package->duration);
        elseif ($package->duration_unit === 'bulan')  $expiryDate->addMonths($package->duration);
        elseif ($package->duration_unit === 'tahun')  $expiryDate->addYears($package->duration);

        // ── Buat Member Pertama ──
        $member1 = $this->createMember(
            vipId: 'VIP-' . now()->format('Ymd-His') . '-' . rand(1000, 9999),
            data: [
                'name'           => $request->name,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth'  => $request->date_of_birth,
                'gender'         => $request->gender,
                'nik'            => $request->nik,
                'job'            => $request->job,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'photo_data'     => $request->photo_data,
            ],
            package: $package,
            now: $now,
            activationDate: $activationDate,
            expiryDate: $expiryDate,
            status: 'pending',
            lockedPackageId: $package->id,
            lockedPrice: $lockedPrice
        );

        // ── Perhitungan Diskon & Admin Fee (Khusus Registrasi Baru) ──
        $discountPercentage = 0;
        if ($request->filled('discount_id')) {
            $discount = \App\Models\Discount::find($request->discount_id);
            if ($discount) {
                // Verify the discount is valid for this package
                if ($discount->gymPackages()->where('gym_package_id', $package->id)->exists()) {
                    $discountPercentage = $discount->percentage;
                }
            }
        }
        
        $discountAmount = ($package->price * $discountPercentage) / 100;
        $lockedPrice = $package->price - $discountAmount;
        $adminFee = $package->admin_fee;
        $finalAmount = $lockedPrice + $adminFee;

        // ── Transaksi (1 transaksi untuk couple, member_id = member pertama) ──
        $transactionCode = ($isCouple ? 'CPL-' : 'TRX-') . time() . '-' . rand(100, 999);
        MemberTransaction::create([
            'transaction_code'    => $transactionCode,
            'member_id'           => $member1->id,
            'gym_package_id'      => $package->id,
            'user_id'             => Auth::id(),
            'amount'              => $finalAmount,
            'discount_percentage' => $discountPercentage,
            'admin_fee'           => $adminFee,
            'transaction_date'    => $now,
            'transaction_type'    => 'new',
            'payment_status'      => 'unpaid',
        ]);

        $successMsg = 'Member berhasil didaftarkan! VIP ID: ' . $member1->member_id;

        // ── Buat Member Kedua jika couple ──
        if ($isCouple) {
            // Jeda 1 detik agar VIP ID timestamp-nya unik
            sleep(1);
            $member2 = $this->createMember(
                vipId: 'VIP-' . now()->format('Ymd-His') . '-' . rand(1000, 9999),
                data: [
                    'name'           => $request->member2_name,
                    'place_of_birth' => $request->member2_place_of_birth,
                    'date_of_birth'  => $request->member2_date_of_birth,
                    'gender'         => $request->member2_gender,
                    'nik'            => $request->member2_nik,
                    'job'            => $request->member2_job,
                    'address'        => $request->member2_address,
                    'phone'          => $request->member2_phone,
                    'email'          => $request->member2_email,
                    'photo_data'     => $request->member2_photo_data,
                ],
                package: $package,
                now: $now,
                activationDate: $activationDate,
                expiryDate: $expiryDate,
                status: 'pending',
                lockedPackageId: $package->id,
                lockedPrice: $lockedPrice
            );

            $successMsg .= ' & ' . $member2->member_id . ' (Couple)';
        }

        return redirect()->route('members.index')->with('success', $successMsg);
    }

    public function createExisting()
    {
        $packages = GymPackage::with(['discounts' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();
        return view('members.create_existing', compact('packages'));
    }

    public function storeExisting(Request $request)
    {
        $package = GymPackage::findOrFail($request->package_id);
        $isCouple = $package->max_members >= 2;

        $rules = [
            'member_id'       => 'required|string|max:50|unique:members,member_id',
            'name'            => 'required|string|min:3',
            'place_of_birth'  => 'required|string',
            'date_of_birth'   => 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d'),
            'gender'          => 'required|in:L,P',
            'nik'             => 'required|string|size:16|unique:members,nik',
            'job'             => 'nullable|string',
            'address'         => 'required|string',
            'phone'           => 'required|string',
            'email'           => 'required|email|unique:members,email',
            'photo_data'      => 'nullable|string',
            'package_id'      => 'required|exists:gym_packages,id',
            'discount_id'     => 'nullable|exists:discounts,id',
            'activation_date' => 'required|date',
            'expiry_date'     => 'required|date|after_or_equal:activation_date',
            'payment_status'  => 'required|in:paid,unpaid',
        ];

        if ($isCouple) {
            $rules['member2_member_id']     = 'required|string|max:50|unique:members,member_id|different:member_id';
            $rules['member2_name']          = 'required|string|min:3';
            $rules['member2_place_of_birth']= 'required|string';
            $rules['member2_date_of_birth'] = 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d');
            $rules['member2_gender']        = 'required|in:L,P';
            $rules['member2_nik']           = 'required|string|size:16|unique:members,nik';
            $rules['member2_job']           = 'nullable|string';
            $rules['member2_address']       = 'required|string';
            $rules['member2_phone']         = 'required|string';
            $rules['member2_email']         = 'required|email|unique:members,email';
            $rules['member2_photo_data']    = 'nullable|string';
        }

        $request->validate($rules, [
            'member_id.unique'                       => 'ID Member ini sudah terdaftar di sistem.',
            'member2_member_id.unique'               => 'ID Member ke-2 ini sudah terdaftar di sistem.',
            'member2_member_id.different'            => 'ID Member ke-2 tidak boleh sama dengan ID Member pertama.',
            'date_of_birth.before_or_equal'          => 'Umur member minimal 5 tahun.',
            'member2_date_of_birth.before_or_equal'  => 'Umur member ke-2 minimal 5 tahun.',
            'nik.size'                               => 'NIK harus tepat 16 digit angka.',
            'member2_nik.size'                       => 'NIK member ke-2 harus tepat 16 digit angka.',
            'expiry_date.after_or_equal'             => 'Tanggal Expired harus sama atau setelah Tanggal Aktif.',
        ]);

        $now = now();
        $activationDate = Carbon::parse($request->activation_date);
        $expiryDate = Carbon::parse($request->expiry_date);
        $paymentStatus = $request->payment_status;
        $memberStatus = ($paymentStatus === 'paid') ? 'active' : 'pending';

        $discountPercentage = 0;
        if ($request->filled('discount_id')) {
            $discount = \App\Models\Discount::find($request->discount_id);
            if ($discount) {
                if ($discount->gymPackages()->where('gym_package_id', $package->id)->exists()) {
                    $discountPercentage = $discount->percentage;
                }
            }
        }

        $discountAmount = ($package->price * $discountPercentage) / 100;
        $lockedPrice = $package->price - $discountAmount;
        $adminFee = $package->admin_fee;
        $finalAmount = $lockedPrice + $adminFee;

        // ── Buat Member Pertama dengan Manual ID ──
        $member1 = $this->createMember(
            vipId: trim($request->member_id),
            data: [
                'name'           => $request->name,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth'  => $request->date_of_birth,
                'gender'         => $request->gender,
                'nik'            => $request->nik,
                'job'            => $request->job,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'photo_data'     => $request->photo_data,
            ],
            package: $package,
            now: $now,
            activationDate: $activationDate,
            expiryDate: $expiryDate,
            status: $memberStatus,
            lockedPackageId: $package->id,
            lockedPrice: $lockedPrice
        );

        $transactionCode = ($isCouple ? 'CPL-MIG-' : 'TRX-MIG-') . time() . '-' . rand(100, 999);
        MemberTransaction::create([
            'transaction_code'    => $transactionCode,
            'member_id'           => $member1->id,
            'gym_package_id'      => $package->id,
            'user_id'             => Auth::id(),
            'amount'              => $finalAmount,
            'discount_percentage' => $discountPercentage,
            'admin_fee'           => $adminFee,
            'transaction_date'    => $now,
            'transaction_type'    => 'new',
            'payment_status'      => $paymentStatus,
            'payment_method'      => $paymentStatus === 'paid' ? 'cash' : null,
        ]);

        \App\Models\ActivityLog::log('CREATE', 'Manajemen Member', "Registrasi member lama/migrasi ID manual: {$member1->name} ({$member1->member_id}) - Status: " . strtoupper($memberStatus));

        $successMsg = 'Member Lama berhasil didaftarkan! ID: ' . $member1->member_id;

        if ($isCouple) {
            $member2 = $this->createMember(
                vipId: trim($request->member2_member_id),
                data: [
                    'name'           => $request->member2_name,
                    'place_of_birth' => $request->member2_place_of_birth,
                    'date_of_birth'  => $request->member2_date_of_birth,
                    'gender'         => $request->member2_gender,
                    'nik'            => $request->member2_nik,
                    'job'            => $request->member2_job,
                    'address'        => $request->member2_address,
                    'phone'          => $request->member2_phone,
                    'email'          => $request->member2_email,
                    'photo_data'     => $request->member2_photo_data,
                ],
                package: $package,
                now: $now,
                activationDate: $activationDate,
                expiryDate: $expiryDate,
                status: $memberStatus,
                lockedPackageId: $package->id,
                lockedPrice: $lockedPrice
            );

            \App\Models\ActivityLog::log('CREATE', 'Manajemen Member', "Registrasi member lama (Couple 2) ID manual: {$member2->name} ({$member2->member_id})");
            $successMsg .= ' & ' . $member2->member_id . ' (Couple)';
        }

        return redirect()->route('members.index')->with('success', $successMsg);
    }

    /**
     * Helper: buat satu member + generate QR code.
     */
    private function createMember(
        string $vipId,
        array $data,
        GymPackage $package,
        Carbon $now,
        Carbon $activationDate,
        Carbon $expiryDate,
        string $status = 'pending',
        ?int $lockedPackageId = null,
        ?float $lockedPrice = null
    ): Member {
        // Process photo
        $photoPath = null;
        if (!empty($data['photo_data'])) {
            $imageParts = explode(";base64,", $data['photo_data']);
            if (count($imageParts) == 2) {
                $imageType   = strtolower(explode("image/", $imageParts[0])[1] ?? 'jpeg');
                $imageType   = explode(';', $imageType)[0];
                if ($imageType === 'jpeg') $imageType = 'jpg';
                
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName    = 'member_' . time() . '_' . rand(100, 999) . '.' . $imageType;
                $photoPath   = 'members/' . $fileName;
                
                Storage::disk('public')->makeDirectory('members');
                Storage::disk('public')->put($photoPath, $imageBase64);
            }
        }

        $member = Member::create([
            'member_id'       => $vipId,
            'member_type'     => $package->category,
            'name'            => $data['name'],
            'place_of_birth'  => $data['place_of_birth'],
            'date_of_birth'   => $data['date_of_birth'],
            'gender'          => $data['gender'],
            'nik'             => $data['nik'],
            'job'             => $data['job'] ?? null,
            'address'         => $data['address'],
            'phone'           => $data['phone'],
            'email'           => $data['email'],
            'photo_path'      => $photoPath,
            'registration_date'=> $now,
            'activation_date'  => $activationDate,
            'expiry_date'      => $expiryDate,
            'status'          => $status,
            'locked_package_id'=> $lockedPackageId,
            'locked_price'    => $lockedPrice,
            'extension_count' => 0,
        ]);

        // Generate QR Code
        Storage::disk('public')->makeDirectory('qrcodes');
        $qrPath = 'qrcodes/' . $vipId . '.svg';
        QrCode::size(300)->generate($vipId, storage_path('app/public/' . $qrPath));

        return $member;
    }

    public function show(Member $member)
    {
        $activeStart = $member->activation_date ? \Carbon\Carbon::parse($member->activation_date)->startOfDay() : now()->startOfDay();
        $activeEnd   = $member->expiry_date ? \Carbon\Carbon::parse($member->expiry_date)->endOfDay() : now()->endOfDay();

        $attendanceStats = [
            'total'                  => $member->attendances()->count(),
            'during_active_duration' => $member->attendances()->whereBetween('attendance_time', [$activeStart, $activeEnd])->count(),
            'this_month'             => $member->attendances()->whereMonth('attendance_time', now()->month)->whereYear('attendance_time', now()->year)->count(),
            'this_week'              => $member->attendances()->whereBetween('attendance_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
        $recentAttendances = $member->attendances()->latest('attendance_time')->take(10)->get();
        $packages = GymPackage::with(['discounts' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();

        return view('members.show', compact('member', 'attendanceStats', 'recentAttendances', 'packages'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name'           => 'required|string|min:3',
            'phone'          => 'required|string',
            'email'          => 'required|email|unique:members,email,' . $member->id,
            'address'        => 'required|string',
            'job'            => 'nullable|string',
            'nik'            => 'required|string|size:16|unique:members,nik,' . $member->id,
            'gender'         => 'required|in:L,P',
            'place_of_birth' => 'required|string',
            'date_of_birth'  => 'required|date',
            'photo_data'     => 'nullable|string',
        ]);

        $data = $request->only(['name', 'phone', 'email', 'address', 'job', 'nik', 'gender', 'place_of_birth', 'date_of_birth']);

        // Process photo if provided
        if ($request->filled('photo_data')) {
            $imageParts = explode(";base64,", $request->photo_data);
            if (count($imageParts) == 2) {
                $imageType   = strtolower(explode("image/", $imageParts[0])[1] ?? 'jpeg');
                $imageType   = explode(';', $imageType)[0];
                if ($imageType === 'jpeg') $imageType = 'jpg';
                
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName    = 'member_' . time() . '_' . rand(100, 999) . '.' . $imageType;
                $photoPath   = 'members/' . $fileName;
                
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('members');
                \Illuminate\Support\Facades\Storage::disk('public')->put($photoPath, $imageBase64);
                
                // Delete old photo if it exists
                if ($member->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($member->photo_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($member->photo_path);
                }

                $data['photo_path'] = $photoPath;
            }
        }

        $member->update($data);

        \App\Models\ActivityLog::log('UPDATE', 'Manajemen Member', "Memperbarui profil data member: {$member->name} ({$member->member_id})");

        return redirect()->route('members.show', $member)->with('success', 'Data member berhasil diperbarui.');
    }

    public function renewal(Request $request, Member $member)
    {
        $request->validate([
            'package_id'     => 'required|exists:gym_packages,id',
            'discount_id'    => 'nullable|exists:discounts,id',
        ]);

        // Cek tagihan belum lunas
        $hasUnpaid = \App\Models\MemberTransaction::where('member_id', $member->id)
            ->where('payment_status', 'unpaid')
            ->exists();
        
        if ($hasUnpaid) {
            return back()->with('error', 'Member ini masih memiliki tagihan yang BELUM LUNAS. Silakan bayar di Kasir terlebih dahulu.');
        }

        $package = GymPackage::findOrFail($request->package_id);
        $now     = now();

        $renewalAmount = $package->price;
        $discountPercentage = 0;
        
        // Cek Grandfathered Rate / Locked Price
        if ($member->locked_package_id === $package->id && $member->locked_price !== null) {
            $renewalAmount = $member->locked_price;
            if ($package->price > 0 && $package->price > $member->locked_price) {
                $discountPercentage = (($package->price - $member->locked_price) / $package->price) * 100;
            }
        } else {
            // Jika pindah paket, hitung berdasarkan diskon baru yang dipilih
            $discountPercentage = 0;
            if ($request->filled('discount_id')) {
                $discount = \App\Models\Discount::find($request->discount_id);
                if ($discount && $discount->gymPackages()->where('gym_package_id', $package->id)->exists()) {
                    $discountPercentage = $discount->percentage;
                }
            }
            $discountAmount = ($package->price * $discountPercentage) / 100;
            $renewalAmount = $package->price - $discountAmount;
            
            // Update locked price dan package_id member supaya ke depannya pakai harga paket baru ini
            $member->update([
                'locked_package_id' => $package->id,
                'locked_price'      => $renewalAmount
            ]);
        }

        MemberTransaction::create([
            'transaction_code'    => 'RNW-' . time() . '-' . rand(100, 999),
            'member_id'           => $member->id,
            'gym_package_id'      => $package->id,
            'user_id'             => Auth::id(),
            'amount'              => $renewalAmount,
            'discount_percentage' => $discountPercentage,
            'admin_fee'           => 0, // Admin fee biasanya 0 untuk renewal
            'transaction_date'    => $now,
            'transaction_type'    => 'renewal',
            'payment_status'      => 'unpaid',
        ]);

        return redirect()->route('members.show', $member)->with('success', "Tagihan perpanjangan paket berhasil dibuat. Silakan lakukan pembayaran di menu Kasir Member.");
    }

    public function ecard(Member $member)
    {
        return view('members.ecard', compact('member'));
    }

    public function destroy(Member $member)
    {
        // Hapus foto jika ada
        if ($member->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($member->photo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($member->photo_path);
        }

        // Hapus QR code
        $qrPath = 'qrcodes/' . $member->member_id . '.svg';
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($qrPath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($qrPath);
        }

        $name = $member->name;
        $memberId = $member->member_id;
        $member->delete();

        \App\Models\ActivityLog::log('DELETE', 'Manajemen Member', "Menghapus data member: {$name} ({$memberId})");

        return redirect()->route('members.index')->with('success', 'Data member berhasil dihapus!');
    }
}
