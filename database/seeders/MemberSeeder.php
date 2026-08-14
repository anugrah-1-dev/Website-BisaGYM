<?php

namespace Database\Seeders;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Seed members from gym.data_members.json (MongoDB export).
     *
     * Reads the JSON file using a streaming approach to avoid
     * memory exhaustion on the ~3GB file containing base64 photo data.
     */
    public function run(): void
    {
        $filePath = base_path('gym.data_members.json');

        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan: $filePath");
            return;
        }

        $this->command->info('Membaca data member dari gym.data_members.json ...');

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->command->error("Tidak bisa membuka file: $filePath");
            return;
        }

        $bracketDepth = 0;
        $content = '';
        $inString = false;
        $escape = false;
        $started = false;
        $inserted = 0;
        $skipped = 0;
        $batch = [];
        $batchSize = 50;

        while (!feof($handle)) {
            $chunk = fread($handle, 65536);

            for ($i = 0; $i < strlen($chunk); $i++) {
                $char = $chunk[$i];

                if ($escape) {
                    $content .= $char;
                    $escape = false;
                    continue;
                }

                if ($char === '\\' && $inString) {
                    $content .= $char;
                    $escape = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = !$inString;
                    $content .= $char;
                    continue;
                }

                if ($inString) {
                    $content .= $char;
                    continue;
                }

                if ($char === '{') {
                    if ($bracketDepth === 0) {
                        $content = '{';
                        $started = true;
                    } else {
                        $content .= $char;
                    }
                    $bracketDepth++;
                } elseif ($char === '}') {
                    $bracketDepth--;
                    $content .= $char;

                    if ($bracketDepth === 0 && $started) {
                        $record = json_decode($content, true);

                        if ($record) {
                            $memberData = $this->mapRecord($record);

                            if ($memberData) {
                                $batch[] = $memberData;
                                $inserted++;

                                if (count($batch) >= $batchSize) {
                                    $this->insertBatch($batch);
                                    $batch = [];
                                }
                            } else {
                                $skipped++;
                            }
                        } else {
                            $skipped++;
                        }

                        $content = '';
                        $started = false;
                    }
                } else {
                    if ($started) {
                        $content .= $char;
                    }
                }
            }
        }

        // Insert remaining batch
        if (!empty($batch)) {
            $this->insertBatch($batch);
        }

        fclose($handle);

        $this->command->info("Selesai! $inserted member berhasil di-seed, $skipped di-skip.");
    }

    /**
     * Map a single JSON record to the members table columns.
     */
    private function mapRecord(array $record): ?array
    {
        $memberId = $record['member_id'] ?? null;
        $name = $record['nama_lengkap'] ?? null;

        if (!$memberId || !$name) {
            return null;
        }

        // Map gender: "Laki-laki" -> "L", "Perempuan" -> "P"
        $genderRaw = $record['jenis_kelamin'] ?? 'Laki-laki';
        $gender = ($genderRaw === 'Perempuan') ? 'P' : 'L';

        $statusRaw = $record['status_keanggotaan'] ?? null;
        
        // Parse dates first so we can use expiry_date to determine status
        $registrationDate = $this->parseDate(
            $record['waktu_pendaftaran'] ?? $record['tanggal_daftar_str'] ?? null
        );
        $activationDate = $this->parseDate(
            $record['tanggal_aktivasi_str'] ?? $record['tanggal_daftar_str'] ?? null
        );
        $expiryDate = $this->parseDate(
            $record['tanggal_berlaku_hingga_str'] ?? null
        );
        
        $status = $this->mapStatus($statusRaw, $expiryDate);


        $now = Carbon::now();

        return [
            'member_id'         => $memberId,
            'member_type'       => $record['member_type'] ?? 'Reguler',
            'name'              => $name,
            'place_of_birth'    => $record['tempat_lahir'] ?? null,
            'date_of_birth'     => $record['tanggal_lahir'] ?? null,
            'gender'            => $gender,
            'nik'               => $record['nik'] ?? null,
            'job'               => $record['pekerjaan'] ?? null,
            'address'           => $record['alamat_domisili'] ?? null,
            'phone'             => $record['no_hp'] ?? null,
            'email'             => $record['email'] ?? null,
            'photo_path'        => null,
            'registration_date' => $registrationDate ?? $now,
            'activation_date'   => $activationDate ?? ($registrationDate ? Carbon::parse($registrationDate)->toDateString() : $now->toDateString()),
            'expiry_date'       => $expiryDate ?? ($activationDate ? Carbon::parse($activationDate)->addMonth()->toDateString() : $now->addMonth()->toDateString()),
            'status'            => $status,
            'extension_count'   => 0,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
    }

    /**
     * Map status_keanggotaan to the enum: active, pending, expired.
     * Cek dari status eksplisit JSON, atau fallback ke pengecekan tanggal expired.
     */
    private function mapStatus(?string $statusRaw, ?string $expiryDate): string
    {
        if ($statusRaw) {
            $statusLower = strtolower($statusRaw);
            if (in_array($statusLower, ['tidak aktif', 'non aktif', 'expired', 'non-aktif'])) {
                return 'expired';
            }
            if (in_array($statusLower, ['pending', 'menunggu'])) {
                return 'pending';
            }
        }

        // Jika tidak ada status eksplisit, tentukan dari expiry_date
        if ($expiryDate && Carbon::parse($expiryDate)->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Parse various date formats into a Carbon-friendly string.
     */
    private function parseDate(?string $dateStr): ?string
    {
        if (!$dateStr) {
            return null;
        }

        try {
            return Carbon::parse($dateStr)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert a batch of member records, skipping duplicates by member_id.
     */
    private function insertBatch(array $batch): void
    {
        foreach ($batch as $memberData) {
            try {
                Member::firstOrCreate(
                    ['member_id' => $memberData['member_id']],
                    $memberData
                );
            } catch (\Exception $e) {
                // Skip duplicates or invalid records silently
            }
        }
    }
}
