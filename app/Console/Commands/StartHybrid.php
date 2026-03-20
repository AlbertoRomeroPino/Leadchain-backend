<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartHybrid extends Command
{
    protected $signature = 'start:hybrid {--seed : Ejecuta seeders tras migrar}';

    protected $description = 'Arranca en modo híbrido: Docker DB + Laravel local';

    public function handle(): int
    {
        $this->alert('Arranque híbrido: DB en Docker + API local');

        if (!$this->runStep('Levantando base de datos Docker...', 'docker compose up -d db')) {
            return self::FAILURE;
        }

        if (!$this->runStep('Limpiando cachés de Laravel...', 'php artisan optimize:clear')) {
            return self::FAILURE;
        }

        if (!$this->runStep('Ejecutando migraciones...', 'php artisan migrate --force')) {
            return self::FAILURE;
        }

        if ($this->option('seed')) {
            if (!$this->runStep('Ejecutando seeders...', 'php artisan db:seed --force')) {
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Modo híbrido listo. Arrancando servidor local en http://127.0.0.1:8000');
        $this->info('Swagger: http://127.0.0.1:8000/api/documentation');
        $this->newLine();

        return (int) $this->call('serve', [
            '--host' => '127.0.0.1',
            '--port' => '8000',
        ]);
    }

    private function runStep(string $title, string $command): bool
    {
        $this->info($title);

        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(null);

        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error("Error ejecutando: {$command}");
            return false;
        }

        return true;
    }
}
