<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Activity;

class TransferTicketsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:transfer {from : Aktarılacak kullanıcının ID\'si veya E-postası} {to : Hedef kullanıcının ID\'si veya E-postası}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bir personelin tüm taleplerini ve faaliyetlerini başka bir personele aktarır.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fromInput = $this->argument('from');
        $toInput = $this->argument('to');

        // Find source user
        $fromUser = User::where('id', $fromInput)
            ->orWhere('email', $fromInput)
            ->first();

        if (!$fromUser) {
            $this->error("Kaynak kullanıcı (Aktarılacak kişi) bulunamadı: {$fromInput}");
            return Command::FAILURE;
        }

        // Find target user
        $toUser = User::where('id', $toInput)
            ->orWhere('email', $toInput)
            ->first();

        if (!$toUser) {
            $this->error("Hedef kullanıcı (Aktarılacak kişi) bulunamadı: {$toInput}");
            return Command::FAILURE;
        }

        if ($fromUser->id === $toUser->id) {
            $this->error("Kaynak ve hedef kullanıcı aynı olamaz!");
            return Command::FAILURE;
        }

        $this->info("--------------------------------------------------");
        $this->info("Kaynak Kullanıcı: #{$fromUser->id} - {$fromUser->name} ({$fromUser->email})");
        $this->info("Hedef Kullanıcı : #{$toUser->id} - {$toUser->name} ({$toUser->email})");
        $this->info("--------------------------------------------------");

        if (!$this->confirm("Tüm talepleri ve faaliyetleri aktarmak istediğinize emin misiniz?", true)) {
            $this->warn("İşlem iptal edildi.");
            return Command::SUCCESS;
        }

        // 1. Transfer assigned tickets
        $assignedCount = Ticket::where('assigned_to', $fromUser->id)
            ->update(['assigned_to' => $toUser->id]);

        // 2. Transfer resolved tickets
        $resolvedCount = Ticket::where('resolved_by', $fromUser->id)
            ->update(['resolved_by' => $toUser->id]);

        // 3. Transfer activities
        $activitiesCount = Activity::where('user_id', $fromUser->id)
            ->update(['user_id' => $toUser->id]);

        $this->info("Aktarım tamamlandı:");
        $this->line("- Atanan Talepler: {$assignedCount} adet aktarıldı.");
        $this->line("- Çözülen Talepler: {$resolvedCount} adet aktarıldı.");
        $this->line("- Faaliyetler: {$activitiesCount} adet aktarıldı.");

        // Clean cache
        \Illuminate\Support\Facades\Cache::forget('general_settings');

        // Ask to delete the old user
        if ($this->confirm("Aktarım yapılan eski kullanıcıyı (#{$fromUser->id} - {$fromUser->name}) sistemden tamamen silmek ister misiniz?", false)) {
            $fromUser->delete();
            $this->info("Eski kullanıcı sistemden başarıyla silindi.");
        }

        return Command::SUCCESS;
    }
}
