<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed {--processes=0} {--current_process=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed milion rows into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processes = (int) $this->option('processes');
        $current_process = (int) $this->option('current_process');

        if ($processes) {
            return $this->spawn($processes);
        }

        for ($i = 0; $i < 10000; $i++) {
            try {
                $this->create($current_process, $i);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
        }
    }

    public function spawn($processes)
    {
        $start = microtime(true);

        Process::pool(function (Pool $pool) use ($processes) {
            for ($i = 0; $i < $processes; $i++) {
                $pool->command('php artisan app:seed --current_process=' . $i)->timeout(60 * 5);
                echo '.';
            }
        })->start()->wait();

        echo "Time: " . round((microtime(true) - $start), 10) . "s\n";
    }

    protected function create($current_process, $block)
    {
        // User::factory()->count(10)->create();

        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'name' => fake()->name,
                'email' => 'user' . $current_process . $block . $i . '@test.test',
                'email_verified_at' => now(),
                'created_at' => now(),
                'password' => 'password',
                'remember_token' => Str::random(10),
            ];
        }

        User::insert($data);

        // $data = [
        //     'name' => fake()->name,
        //     'email' => fake()->unique()->safeEmail,
        //     'email_verified_at' => now()->subHours(rand(0, 9999))->subMinutes(rand(0, 9999)),
        //     'created_at' => now()->subHours(rand(0, 9999))->subMinutes(rand(0, 9999)),
        //     'password' => 'password',
        //     'remember_token' => Str::random(10),
        // ];
        // User::create($data);
    }
}
