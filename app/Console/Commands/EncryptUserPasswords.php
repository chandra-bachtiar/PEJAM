<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Hash;

class EncryptUserPasswords extends Command
{
    protected $signature = 'password:encrypt';
    protected $description = 'Encrypt all user passwords to bcrypt';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            if (password_needs_rehash($user->password, PASSWORD_BCRYPT)) {
                $user->password = bcrypt($user->password);
            }

            $user->save();
        }

        $this->info('All user passwords have been encrypted to bcrypt.');
    }
}
