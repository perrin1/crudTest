<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallCrudTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudtest:install {modelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the CRUD test for the specified model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');

        // Création du modèle
        $this->call('make:model', ['name' => $modelName]);

        // Création de la migration
        $this->call('make:migration', ['name' => 'create_' . $modelName . '_table']);

        // Création du contrôleur
        $this->call('make:controller', ['name' => $modelName . 'Controller']);

        // Création des vues (vous devrez ajuster cela selon vos besoins)
        // Création de la vue create
        $this->call('make:view', ['name' => 'crudtest::' . $modelName . '.create']);

        // Création de la vue index
        $this->call('make:view', ['name' => 'crudtest::' . $modelName . '.index']);

        // Création de la vue edit
        $this->call('make:view', ['name' => 'crudtest::' . $modelName . '.edit']);

        // Création de la vue show
        $this->call('make:view', ['name' => 'crudtest::' . $modelName . '.show']);

        // Configuration des routes pour CRUD
      
        $routeContent = <<<PHP
            // Routes spécifiques pour $modelName
            // Route pour afficher le formulaire de création
            Route::get('/{$modelName}/create', [{$modelName}Controller::class, 'create'])->name('{$modelName}.create');
            
            // Route pour afficher un {$modelName} spécifique
            Route::get('/{$modelName}/{{$modelName}}', [{$modelName}Controller::class, 'show'])->name('{$modelName}.show');
            
            // Route pour enregistrer un nouvel {$modelName}
            Route::post('/{$modelName}', [{$modelName}Controller::class, 'store'])->name('{$modelName}.store');
            
            // Route pour afficher le formulaire de modification d'un {$modelName}
            Route::get('/{$modelName}/{{$modelName}}/edit', [{$modelName}Controller::class, 'edit'])->name('{$modelName}.edit');
            
            // Route pour mettre à jour un {$modelName} existant
            Route::put('/{$modelName}/{{$modelName}}', [{$modelName}Controller::class, 'update'])->name('{$modelName}.update');
            
            // Route pour supprimer un {$modelName}
            Route::delete('/{$modelName}/{{$modelName}}', [{$modelName}Controller::class, 'destroy'])->name('{$modelName}.destroy');
            // Fin des routes spécifiques pour $modelName
        PHP;

        // Ajouter les routes au fichier web.php
        file_put_contents(base_path('routes/web.php'), $routeContent, FILE_APPEND);

        $this->info("CRUD test for $modelName has been installed successfully!");
    }
}
