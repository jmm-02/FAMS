<?php
// Check if Composer is installed
echo "Checking for Composer...\n";
$composerPath = 'composer';

// Create composer.json if it doesn't exist
if (!file_exists(__DIR__ . '/composer.json')) {
    echo "Creating composer.json...\n";
    $composerJson = [
        "require" => [
            "phpoffice/phpspreadsheet" => "^1.28"
        ]
    ];
    file_put_contents(__DIR__ . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));
    echo "composer.json created.\n";
} else {
    echo "composer.json already exists.\n";
}

// Check if vendor directory exists
if (!is_dir(__DIR__ . '/vendor')) {
    echo "Vendor directory not found. Please run 'composer install' in this directory.\n";
    echo "You can do this by opening a command prompt, navigating to " . __DIR__ . " and running:\n";
    echo "composer install\n";
} else {
    echo "Vendor directory found. Checking for PhpSpreadsheet...\n";
    
    if (is_dir(__DIR__ . '/vendor/phpoffice/phpspreadsheet')) {
        echo "PhpSpreadsheet is installed.\n";
    } else {
        echo "PhpSpreadsheet not found. Please run 'composer require phpoffice/phpspreadsheet' in this directory.\n";
        echo "You can do this by opening a command prompt, navigating to " . __DIR__ . " and running:\n";
        echo "composer require phpoffice/phpspreadsheet\n";
    }
}

echo "\nOnce PhpSpreadsheet is installed, you can use the improved_import.php script to import your Excel files.\n";
?>
