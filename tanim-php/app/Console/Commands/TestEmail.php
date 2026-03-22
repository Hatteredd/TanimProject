<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Mail::raw('This is a test email from Tanim to verify email configuration is working.', function ($message) {
                $message->to('ricquejp@gmail.com')
                       ->subject('Tanim Email Test - Configuration Working');
            });
            
            $this->info('✅ Test email sent successfully to ricquejp@gmail.com');
            $this->info('Please check your Gmail inbox (and spam folder).');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
        
        return 0;
    }
}
