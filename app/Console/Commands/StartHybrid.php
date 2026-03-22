<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartHybrid extends Command
{
    protected $signature = 'start:hybrid';

    protected $description = 'Arranca en modo híbrido: Docker DB + Laravel local';

    public function handle(): int
    {
        $this->alert('Arranque híbrido: DB en Docker + API local');

        if (!$this->runStep('Levantando base de datos Docker...', 'docker compose up -d db')) {
            return self::FAILURE;
        }

        if (!$this->waitForDbReady()) {
            return self::FAILURE;
        }

        $localDbEnv = [
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '5432',
        ];

        if (!$this->runStep('Ejecutando migraciones...', 'php artisan migrate --force', $localDbEnv, 6, 2)) {
            return self::FAILURE;
        }

        if (!$this->runStep('Ejecutando seeders...', 'php artisan db:seed --force', $localDbEnv, 3, 2)) {
            return self::FAILURE;
        }

        if (!$this->runStep('Limpiando cachés de Laravel...', 'php artisan optimize:clear', $localDbEnv)) {
            return self::FAILURE;
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

    private function waitForDbReady(int $maxAttempts = 30, int $sleepSeconds = 2): bool
    {
        $this->info('Esperando que PostgreSQL esté listo...');

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $process = Process::fromShellCommandline(
                'docker compose exec -T db sh -lc "pg_isready -U root -d leadchain && psql -U root -d leadchain -c \"select 1\" >/dev/null"',
                base_path()
            );
            $process->setTimeout(null);
            $process->run();

            if ($process->isSuccessful()) {
                return true;
            }

            $this->line("Esperando DB... intento {$attempt}/{$maxAttempts}");
            sleep($sleepSeconds);
        }

        $this->error('Timeout esperando disponibilidad de PostgreSQL en Docker.');
        return false;
    }

    private function runStep(
        string $title,
        string $command,
        array $env = [],
        int $maxAttempts = 1,
        int $sleepSeconds = 0
    ): bool
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $suffix = $maxAttempts > 1 ? " (intento {$attempt}/{$maxAttempts})" : '';
            $this->info($title . $suffix);

            $process = Process::fromShellCommandline($command, base_path(), $env);
            $process->setTimeout(null);

            $process->run(function (string $type, string $buffer): void {
                $this->output->write($buffer);
            });

            if ($process->isSuccessful()) {
                return true;
            }

            if ($attempt < $maxAttempts && $sleepSeconds > 0) {
                $this->warn('Fallo transitorio. Reintentando...');
                sleep($sleepSeconds);
            }
        }

        $this->error("Error ejecutando: {$command}");
        return false;
    }
}
