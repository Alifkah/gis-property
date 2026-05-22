<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /** @var string */
    protected $signature = 'admin:make {email : Email pengguna yang akan dijadikan admin}';

    /** @var string */
    protected $description = 'Jadikan pengguna sebagai administrator berdasarkan email';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("Pengguna dengan email [{$email}] tidak ditemukan.");

            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->warn("Pengguna [{$user->name}] sudah menjadi admin.");

            return self::SUCCESS;
        }

        $user->update(['is_admin' => true]);

        $this->info("Berhasil! [{$user->name}] ({$email}) sekarang adalah administrator.");

        return self::SUCCESS;
    }
}
