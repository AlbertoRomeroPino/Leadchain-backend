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

        $process = Process::fromShellCommandline('docker compose up -d --build', base_path());
        $process->setTimeout(null);

        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error('Error levantando Docker en modo completo.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Modo completo levantado.');
        $this->line('API/raíz: http://localhost:8000');
        $this->line('Swagger: http://localhost:8000/api/documentation');

        return self::SUCCESS;
    }
}
