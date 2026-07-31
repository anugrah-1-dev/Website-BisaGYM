<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\GymPackage;
use App\Models\MemberTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class PublicRegistrationController extends Controller
{
    public function index()
    {
        $packages = GymPackage::where('is_active', true)->get();
        return view('public-registration.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $package = GymPackage::findOrFail($request->package_id);
        $isCouple = $package->max_members >= 2;

        $rules = [
            'name'           => 'required|string|min:3',
            'place_of_birth' => 'required|string',
            'date_of_birth'  => 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d'),
            'gender'         => 'required|in:L,P',
            'nik'            => 'required|string|size:16|unique:members,nik',
            'job'            => 'nullable|string',
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'email'          => 'required|email|unique:members,email',
            'photo_data'     => 'nullable|string',
            'package_id'     => 'required|exists:gym_packages,id',
        ];

        if ($isCouple) {
            $rules['member2_name']           = 'required|string|min:3';
            $rules['member2_place_of_birth'] = 'required|string';
            $rules['member2_date_of_birth']  = 'required|date|before_or_equal:' . Carbon::now()->subYears(5)->format('Y-m-d');
            $rules['member2_gender']         = 'required|in:L,P';
            $rules['member2_nik']            = 'required|string|size:16|unique:members,nik';
            $rules['member2_job']            = 'nullable|string';
            $rules['member2_address']        = 'required|string';
            $rules['member2_phone']          = 'required|string';
            $rules['member2_email']          = 'required|email|unique:members,email';
            $rules['member2_photo_data']     = 'nullable|string';
        }

        $request->validate($rules, [
            'date_of_birth.before_or_equal'         => 'Umur minimal 5 tahun.',
            'member2_date_of_birth.before_or_equal' => 'Umur member ke-2 minimal 5 tahun.',
            'nik.size'                              => 'NIK harus tepat 16 digit angka.',
            'member2_nik.size'                      => 'NIK member ke-2 harus tepat 16 digit angka.',
        ]);

        $now = now();
        $activationDateStr = $request->input('activation_date');
        $activationDate = $activationDateStr ? Carbon::parse($activationDateStr) : $now;

        $expiryDate = $activationDate->copy();
        if ($package->duration_unit === 'hari')       $expiryDate->addDays($package->duration);
        elseif ($package->duration_unit === 'bulan')  $expiryDate->addMonths($package->duration);
        elseif ($package->duration_unit === 'tahun')  $expiryDate->addYears($package->duration);

        // Create Member 1
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
            expiryDate: $expiryDate
        );

        $discountPercentage = (int) $request->input('discount_category', 0);
        $discountAmount = ($package->price * $discountPercentage) / 100;
        $adminFee = $package->admin_fee;
        $finalAmount = ($package->price - $discountAmount) + $adminFee;

        $transactionCode = ($isCouple ? 'CPL-' : 'TRX-') . time() . '-' . rand(100, 999);
        MemberTransaction::create([
            'transaction_code'    => $transactionCode,
            'member_id'           => $member1->id,
            'gym_package_id'      => $package->id,
            'user_id'             => null, // Self-registration
            'amount'              => $finalAmount,
            'discount_percentage' => $discountPercentage,
            'admin_fee'           => $adminFee,
            'transaction_date'    => $now,
            'transaction_type'    => 'new',
            'payment_status'      => 'unpaid',
        ]);

        if ($isCouple) {
            sleep(1);
            $this->createMember(
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
                expiryDate: $expiryDate
            );
        }

        \App\Models\ActivityLog::create([
            'user_id'     => null,
            'action'      => 'CREATE',
            'module'      => 'Pendaftaran Mandiri',
            'description' => "Pendaftaran mandiri member baru: {$member1->name} ({$member1->member_id}) - Paket {$package->name}" . ($isCouple ? " (Couple dengan {$request->member2_name})" : ""),
        ]);

        return redirect()->route('public.registration.index')->with('success', "Pendaftaran Berhasil! Terima kasih {$member1->name}, data pendaftaran Anda telah tersimpan. Silakan konfirmasi ke kasir/admin di depan.");
    }

    private function createMember(
        string $vipId,
        array $data,
        GymPackage $package,
        Carbon $now,
        Carbon $activationDate,
        Carbon $expiryDate
    ): Member {
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
            'status'          => 'pending',
            'extension_count' => 0,
        ]);

        Storage::disk('public')->makeDirectory('qrcodes');
        $qrPath = 'qrcodes/' . $vipId . '.svg';
        QrCode::size(300)->generate($vipId, storage_path('app/public/' . $qrPath));

        return $member;
    }
}
