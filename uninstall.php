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


// Supprimer le modèle, la migration et le contrôleur
unlink(app_path('Models/' . $modelName . '.php')); // Supprimer le fichier du modèle
$migrationFiles = glob(database_path('migrations/*_create_' . strtolower(Str::plural($modelName)) . '_table.php'));

foreach ($migrationFiles as $migrationFile) {
    unlink($migrationFile);
}

unlink(app_path('Http/Controllers/' . $modelName . 'Controller.php')); // Supprimer le contrôleur

// Supprimer les fichiers de vue
$viewsPath = resource_path('views/' . strtolower(Str::plural($modelName)));
if (file_exists($viewsPath)) {
    // Supprimer tous les fichiers dans le répertoire des vues
    $files = scandir($viewsPath);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            unlink($viewsPath . '/' . $file);
        }
    }
    // Supprimer le répertoire des vues
    rmdir($viewsPath);
}

// Supprimer les routes du fichier web.php

$routesContent = file_get_contents(base_path('routes/web.php'));

// Diviser le contenu en lignes
$lines = explode("\n", $routesContent);

// Créer un tableau pour stocker les nouvelles lignes
$newLines = [];

// Indicateur pour savoir si nous sommes dans la section des routes spécifiques au modèle
$inModelRoutes = false;

// Parcourir chaque ligne
foreach ($lines as $line) {
    // Vérifier si nous avons atteint le début des routes spécifiques pour le modèle
    if (strpos($line, "// Routes spécifiques pour $modelName") !== false) {
        $inModelRoutes = true;
        continue;
    }
    //  php install.php Toto
    //  php uninstall.php Toto
    //     php artisan crudreally:install NomDuModele
    // php artisan crudreally:uninstall NomDuModele

    // composer remove nom-du-package


    // Vérifier si nous avons atteint la fin des routes spécifiques pour le modèle
    if ($inModelRoutes && strpos($line, "// Fin des routes spécifiques pour $modelName") !== false) {
        $inModelRoutes = false;
        continue;
    }

    // Ne conserver que les lignes qui ne sont pas dans la section des routes spécifiques pour le modèle
    if (!$inModelRoutes) {
        $newLines[] = $line;
    }
}

// Concaténer les lignes en une seule chaîne
$newContent = implode("\n", $newLines);


// var_dump($newContent);

// // Écrire le nouveau contenu dans le fichier web.php
// file_put_contents(base_path('routes/web.php'), $newContent);
// // Écrire le contenu modifié dans le fichier web.php
file_put_contents(base_path('routes/web.php'), $newContent);



echo "La désinstallation du package CRUDReally pour $modelName a été effectuée avec succès !\n";