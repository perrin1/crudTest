<?php

// Charger l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Initialiser l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

// Utiliser le noyau artisan pour exécuter des commandes
use Illuminate\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Str;

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Récupérer le nom du modèle à partir des arguments de la commande
$modelName = $argv[1] ?? null;

if (!$modelName) {
    echo "Veuillez spécifier le nom du modèle.\n";
    exit(1);
}

// Définir les commandes à exécuter lors de l'installation
$commands = [
    "make:model $modelName",
    "make:migration create_" . strtolower(Str::plural($modelName)) . "_table --create=" . strtolower(Str::plural($modelName)),
    "make:controller {$modelName}Controller",
    // Ajoutez d'autres commandes selon vos besoins
];

// Exécuter les commandes
foreach ($commands as $command) {
    $kernel->call($command);
}

// Créer les vues
$stubPath = base_path('vendor/crudtest/stubs/');
$viewsPath = resource_path('views/' . strtolower(Str::plural($modelName)));

// Vérifier si le répertoire des stubs existe, sinon le créer
if (!file_exists($stubPath)) {
    mkdir($stubPath, 0755, true);
}

// Vérifier si le répertoire des vues existe, sinon le créer
if (!file_exists($viewsPath)) {
    mkdir($viewsPath, 0755, true);
}

// Chemin complet vers le fichier create.blade.php dans les stubs
$createStubPath = $stubPath . 'create.blade.php';

// Vérifier si le fichier create.blade.php existe dans les stubs
if (!file_exists($createStubPath)) {
    // Si le fichier n'existe pas, créez-le avec un contenu par défaut
    file_put_contents($createStubPath, '');
}

// Copier le fichier create.blade.php vers le répertoire des vues
copy($createStubPath, $viewsPath . '/create.blade.php');


// Chemin complet vers le fichier index.blade.php dans les stubs
$indexStubPath = $stubPath . 'index.blade.php';

// Vérifier si le fichier index.blade.php existe dans les stubs
if (!file_exists($indexStubPath)) {
    // Si le fichier n'existe pas, créez-le avec un contenu par défaut
    file_put_contents($indexStubPath, '');
}

// Copier le fichier index.blade.php vers le répertoire des vues
copy($indexStubPath, $viewsPath . '/index.blade.php');


// Chemin complet vers le fichier edit.blade.php dans les stubs
$editStubPath = $stubPath . 'edit.blade.php';

// Vérifier si le fichier edit.blade.php existe dans les stubs
if (!file_exists($editStubPath)) {
    // Si le fichier n'existe pas, créez-le avec un contenu par défaut
    file_put_contents($editStubPath, '');
}

// Copier le fichier edit.blade.php vers le répertoire des vues
copy($editStubPath, $viewsPath . '/edit.blade.php');


// Ajouter les routes

// Nom du contrôleur
$controllerName = "{$modelName}Controller";

// Ajouter les routes manuellement pour chaque action du contrôleur
$routeContent = <<<PHP
// Routes spécifiques pour {$modelName}
// Route pour afficher le formulaire de création
Route::get('/{$modelName}/create', [{$controllerName}::class, 'create'])->name('{$modelName}.create');

// Route pour afficher un {$modelName} spécifique
Route::get('/{$modelName}/{{$modelName}}', [{$controllerName}::class, 'show'])->name('{$modelName}.show');

// Route pour enregistrer un nouvel {$modelName}
Route::post('/{$modelName}', [{$controllerName}::class, 'store'])->name('{$modelName}.store');

// Route pour afficher le formulaire de modification d'un {$modelName}
Route::get('/{$modelName}/{{$modelName}}/edit', [{$controllerName}::class, 'edit'])->name('{$modelName}.edit');

// Route pour mettre à jour un {$modelName} existant
Route::put('/{$modelName}/{{$modelName}}', [{$controllerName}::class, 'update'])->name('{$modelName}.update');

// Route pour supprimer un {$modelName}
Route::delete('/{$modelName}/{{$modelName}}', [{$controllerName}::class, 'destroy'])->name('{$modelName}.destroy');
// Fin des routes spécifiques pour {$modelName}
PHP;

// Ajouter les routes au fichier web.php
file_put_contents(base_path('routes/web.php'), $routeContent, FILE_APPEND);


// Afficher un message de confirmation
echo "L'installation du package crudtest pour $modelName a été effectuée avec succès !\n";


