<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireMemberships extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'members:expire';

    /**
     * The console command description.
     */
    protected $description = 'Otomatis mengubah status member menjadi Expired jika sudah melewati tanggal expiry_date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        // Ambil semua member aktif yang expiry_date-nya sudah lewat hari ini
        $expiredMembers = Member::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->get();

        $count = $expiredMembers->count();

        if ($count === 0) {
            $this->info('[members:expire] Tidak ada member yang perlu di-expire hari ini.');
            return Command::SUCCESS;
        }

        foreach ($expiredMembers as $member) {
            $member->update(['status' => 'expired']);

            ActivityLog::log(
                'UPDATE',
                'Scheduler',
                "Auto-expire: Status member {$member->name} ({$member->member_id}) diubah menjadi Expired. Expiry date: {$member->expiry_date}"
            );

            $this->line("  ✓ Expired: [{$member->member_id}] {$member->name} (expired: {$member->expiry_date})");
        }

        $this->info("[members:expire] Selesai. Total {$count} member berhasil di-set Expired.");

        return Command::SUCCESS;
    }
}
