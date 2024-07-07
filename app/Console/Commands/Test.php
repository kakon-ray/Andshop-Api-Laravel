<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiredtoken:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Expired Token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expirationTime = Carbon::now()->subMinutes(1);

        $deletedToken = DB::table('email_verifications')
            ->where('expired_at', '>', $expirationTime)
            ->delete();

        $deletedUser = DB::table('userbasic_temps')
            ->where('expired_at', '>', $expirationTime)
            ->delete();

        $this->info("Delete Expired Token");
    }
}
