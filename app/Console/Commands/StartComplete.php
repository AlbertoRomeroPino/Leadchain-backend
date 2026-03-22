<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartComplete extends Command
{
    protected $signature = 'start:complete';

    protected $description = 'Arranca en modo completo Docker (app + db)';

    public function handle(): int
    {
        $this->alert('Arranque completo Docker: app + db');

        if (!$this->runStep('Levantando contenedores Docker...', 'docker compose up -d --build')) {
            return self::FAILURE;
        }

        if (!$this->waitForAppReady()) {
            return self::FAILURE;
        }

        if (!$this->runStep(
            'Ejecutando migraciones en contenedor app...',
            'docker compose exec -T app sh -lc "cd /var/www/html && php artisan migrate --force"'
        )) {
            return self::FAILURE;
        }

        if (!$this->runStep(
            'Ejecutando seeders en contenedor app...',
            'docker compose exec -T app sh -lc "cd /var/www/html && php artisan db:seed --force"'
        )) {
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Modo completo levantado.');
        $this->line('API/raíz: http://localhost:8000');
        $this->line('Swagger: http://localhost:8000/api/documentation');

        return self::SUCCESS;
    }

    private function waitForAppReady(int $maxAttempts = 60, int $sleepSeconds = 2): bool
    {
        $this->info('Esperando inicialización de la app Docker...');

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $process = Process::fromShellCommandline(
                'docker compose exec -T app sh -lc "cd /var/www/html && [ -f artisan ] && [ -f vendor/autoload.php ]"',
                base_path()
            );
            $process->setTimeout(null);
            $process->run();

            if ($process->isSuccessful()) {
                return true;
            }

            $this->line("Esperando app... intento {$attempt}/{$maxAttempts}");
            sleep($sleepSeconds);
        }

        $this->error('Timeout esperando que la app Docker termine de inicializar.');

        return false;
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
