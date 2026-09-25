<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bitacora;

class CleanupBitacora extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitacora:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete bitacora records older than 1 year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // "ELIMINAR REGISTROS MAYORES A 1 AÑO SI Y SOLO SI MAYORES A UN AÑO"
        $deleted = Bitacora::where('created_at', '<', now()->subYear())->delete();

        $this->info("Deleted $deleted old bitacora records.");
    }
}
