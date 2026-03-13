<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ResetAndTest extends Command
{
    // Nombre del comando
    protected $signature = 'retest';

    // Descripción
    protected $description = 'Resetea la DB, ejecuta Seeders y lanza los tests';

    public function handle()
    {
        $this->alert('Iniciando ciclo completo: Reset + Seed + Test');

        // 1. Resetear y Seedear (Usamos el comando nativo de Laravel)
        $this->info('Step 1: Reseteando base de datos y ejecutando seeders...');
        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->newLine();

        // 2. Ejecutar Tests
        $this->info('Step 2: Lanzando suite de pruebas...');

        // Usamos Process para que la salida de los tests se vea "viva" en la terminal
        // 'php' 'artisan' 'test' funciona en Windows y Linux
        $process = new Process(['php', 'artisan', 'test']);
        $process->setTimeout(null); // Evita que el comando muera si los tests tardan mucho

        // Esta función anónima permite ver el progreso en tiempo real
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        // 3. Resultado final
        if ($process->isSuccessful()) {
            $this->newLine();
            $this->success('¡Todo perfecto! Base de datos lista y tests pasados.');
        } else {
            $this->newLine();
            $this->error('Los tests han fallado. Revisa los errores arriba.');
        }
    }
}
