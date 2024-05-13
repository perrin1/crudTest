<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class UninstallCrudTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudtest:uninstall {modelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the CRUD Test for the specified model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');

        // Supprimer le modèle
        unlink(app_path('Models/' . $modelName . '.php'));

        // Supprimer les fichiers de migration
        $migrationFiles = glob(database_path('migrations/*_create_' . strtolower(Str::plural($modelName)) . '_table.php'));
        foreach ($migrationFiles as $migrationFile) {
            unlink($migrationFile);
        }

        // Supprimer le contrôleur
        unlink(app_path('Http/Controllers/' . $modelName . 'Controller.php'));

        // Supprimer les fichiers de vue
        $viewsPath = resource_path('views/' . strtolower(Str::plural($modelName)));
        if (file_exists($viewsPath)) {
            $files = scandir($viewsPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    unlink($viewsPath . '/' . $file);
                }
            }
            rmdir($viewsPath);
        }

      // Suppression des routes spécifiques pour le modèle du fichier web.php
      $routesContent = file_get_contents(base_path('routes/web.php'));
      $lines = explode("\n", $routesContent);
      $newLines = [];
      $inModelRoutes = false;
      foreach ($lines as $line) {
          if (strpos($line, "// Routes spécifiques pour $modelName") !== false) {
              $inModelRoutes = true;
              continue;
          }
          if ($inModelRoutes && strpos($line, "// Fin des routes spécifiques pour $modelName") !== false) {
              $inModelRoutes = false;
              continue;
          }
          if (!$inModelRoutes) {
              $newLines[] = $line;
          }
      }
      $newContent = implode("\n", $newLines);
      file_put_contents(base_path('routes/web.php'), $newContent);

      $this->info("La désinstallation du package CRUDReally pour $modelName a été effectuée avec succès !");
    }
}
