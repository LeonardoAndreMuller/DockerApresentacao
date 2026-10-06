<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Skips when data already exists, so the Docker entrypoint can run it on every start.
     */
    public function run(): void
    {
        if (Produto::query()->exists()) {
            $this->command?->info('Banco já populado, seed ignorado.');

            return;
        }

        $this->call([
            ProdutoSeeder::class,
            PedidoSeeder::class,
        ]);
    }
}
