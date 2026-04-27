<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EstructuraProyecto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra la estructura del proyecto en forma de árbol filtrando varias carpetas';

    /**
     * Lista de carpetas y archivos a excluir del árbol para enfocarnos en lo relevante del proyecto
     * (puedes ajustar estas listas según tus necesidades)
     */
    private $excludeFolders = [
        'bootstrap',
        'image',
        'public',
        'resources',
        'storage',
        'test',
        'vendor',
        '.git',
        'Providers',
        'factories',
        'Unit',
        ];
    private $excludeFiles = [
        '.editorconfig',
        '.gitattributes',
        '.phpunit.result.cache',
        'artisan',
        'composer.json',
        'composer.lock',
        'echo',
        'git',
        'phpunit.xml',
        'vite.config.js',
        'gitignore',
        'app.php',
        'cache.php',
        'database.php',
        'filesystems.php',
        'logging.php',
        'mail.php',
        'queue.php',
        'services.php',
        'session.php',
        'sanctum.php',
        '2026_03_09_000004_create_cache_table.php',
        '2026_03_09_000005_create_jobs_table.php',
        '2026_03_09_000009_create_personal_access_tokens_table.php',
        '.gitignore',
        'TestCase.php',
    ];

    public function handle()
    {
        $this->info("Estructura del proyecto:");
        $this->listDirectory(base_path());
    }

    /**
     * Función recursiva para dibujar el árbol
     */
    private function listDirectory($dir, $prefix = '')
    {
        // 1. Obtenemos los archivos y carpetas, quitando "." y "..".
        //"." significa el directorio actual y ".." el directorio padre
        $items = array_diff(scandir($dir), ['.', '..']);

        // 2. Filtramos los items según nuestras listas negras
        $items = array_filter($items, function ($item) use ($dir) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                return !in_array($item, $this->excludeFolders);
            }
            return !in_array($item, $this->excludeFiles);
        });

        // Reindexamos para saber cuál es el último elemento
        $items = array_values($items);
        $count = count($items);

        foreach ($items as $index => $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            $isLast = ($index === $count - 1);

            // Dibujamos el conector visual
            $connector = $isLast ? '└── ' : '├── ';
            $this->line($prefix . $connector . $item);

            // 3. Si es una carpeta, entramos recursivamente
            if (is_dir($path)) {
                // Ajustamos el prefijo para los hijos
                $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
                $this->listDirectory($path, $newPrefix);
            }
        }
    }
}
