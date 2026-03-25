<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promote:user {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote a user to the administrator role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->error("Admin role not found in the database. Please run seeders.");
            return 1;
        }

        $user->role_id = $adminRole->id;
        $user->save();

        $this->info("User '{$user->name}' ({$email}) has been promoted to Administrator.");
        
        return 0;
    }
}
