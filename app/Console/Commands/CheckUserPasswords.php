<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CheckUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix missing user passwords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        $this->info('Checking user passwords and showing login info...');
        
        foreach ($users as $user) {
            $this->info("User: {$user->name} ({$user->email})");
            
            // Test common passwords
            $passwords = ['password', 'password123', '123456', 'admin'];
            $found = false;
            
            foreach ($passwords as $pwd) {
                if (password_verify($pwd, $user->password)) {
                    $this->info("  Password: {$pwd}");
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $this->warn("  Password: Unknown/custom password set");
            }
        }
        
        $this->info('Done! Use the passwords shown above to login.');
        return Command::SUCCESS;
    }
}
