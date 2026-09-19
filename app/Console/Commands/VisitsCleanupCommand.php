<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\VisitorSession;
use Illuminate\Console\Command;

class VisitsCleanupCommand extends Command
{
    protected $signature = 'visits:cleanup {--days=90 : Number of days to retain visit history}';
    protected $description = 'Remove old visit logs and inactive visitor sessions';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $deletedVisits = Visit::where('created_at', '<', now()->subDays($days))->delete();
        $deletedSessions = VisitorSession::where('last_activity', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deletedVisits} visits and {$deletedSessions} visitor sessions.");
        return self::SUCCESS;
    }
}