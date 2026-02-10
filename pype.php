<?php
// Script to create Core and Assets folder structure and run setup commands

// Define the folder structure
$coreFolders = [
    'Controller',
    'Models',
    'Helper',
    'Middleware'
];

$assetFolders = [
    'css',
    'js',
    'images'
];

// Create Core folder and its subfolders
if (!file_exists('Core')) {
    mkdir('Core', 0755, true);
    echo "Core folder created successfully.\n";

    foreach ($coreFolders as $folder) {
        $path = 'Core/' . $folder;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            echo "Created: $path\n";
        }
    }
} else {
    echo "Core folder already exists.\n";
}

// Create Assets folder and its subfolders
if (!file_exists('assets')) {
    mkdir('assets', 0755, true);
    echo "Assets folder created successfully.\n";

    foreach ($assetFolders as $folder) {
        $path = 'Assets/' . $folder;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            echo "Created: $path\n";
        }
    }
} else {
    echo "Assets folder already exists.\n";
}

// Run composer install
echo "Running composer install...\n";
$result = shell_exec('composer install 2>&1');
echo $result;

// Copy .env.example to .env if .env doesn't exist
if (!file_exists('.env')) {
    if (copy('.env.example', '.env')) {
        echo "Copied .env.example to .env\n";
    } else {
        echo "Failed to copy .env.example to .env\n";
    }
} else {
    echo ".env file already exists.\n";
}

// Move contents from PYPE-PHP-V2 to current directory and remove the folder
if (is_dir('PYPE-PHP-V2')) {
    $sourceDir = 'PYPE-PHP-V2';
    $iterator = new DirectoryIterator($sourceDir);

    foreach ($iterator as $item) {
        if ($item->isDot()) continue;

        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $item->getFilename();
        $destPath = getcwd() . DIRECTORY_SEPARATOR . $item->getFilename();

        // Only move if destination doesn't already exist
        if (!file_exists($destPath)) {
            if (is_dir($sourcePath)) {
                // Recursively move directory
                $success = rename($sourcePath, $destPath);
            } else {
                // Move file
                $success = rename($sourcePath, $destPath);
            }

            if ($success) {
                echo "Moved: {$item->getFilename()}\n";
            } else {
                echo "Failed to move: {$item->getFilename()}\n";
            }
        } else {
            echo "Skipped (already exists): {$item->getFilename()}\n";
        }
    }

    // Remove the now-empty PYPE-PHP-V2 directory
    if (is_dir('PYPE-PHP-V2')) {
        rmdir_recursive('PYPE-PHP-V2');
        echo "Deleted PYPE-PHP-V2 folder\n";
    }
} else {
    echo "PYPE-PHP-V2 folder does not exist.\n";
}

// Remove the .git folder to prevent Git change indicators for users
if (is_dir('.git')) {
    rmdir_recursive('.git');
    echo "Removed .git folder to prevent Git change indicators\n";
}

echo "Setup completed!\n";

// Recursive function to delete directory with Windows compatibility
function rmdir_recursive($dir) {
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            // On Windows, we may need to change file attributes before deletion
            if (DIRECTORY_SEPARATOR === '\\') {
                @chmod($file->getPathname(), 0777);
            }
            rmdir($file->getPathname());
        } else {
            // On Windows, we may need to change file attributes before deletion
            if (DIRECTORY_SEPARATOR === '\\') {
                @chmod($file->getPathname(), 0777);
            }
            unlink($file->getPathname());
        }
    }

    rmdir($dir);
}
