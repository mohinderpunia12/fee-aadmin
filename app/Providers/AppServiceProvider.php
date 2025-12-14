<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set NODE_PATH and PATH for Browsershot to find Puppeteer and Node.js
        $nodeModulesPath = base_path('node_modules');
        
        // Update PATH to include common Node.js directories
        $currentPath = getenv('PATH') ?: '';
        $nodePaths = [
            '/usr/local/bin',
            '/opt/homebrew/bin',
            '/Users/wiredtechie/.nvm/versions/node/v22.19.0/bin',
        ];
        
        $pathToAdd = [];
        foreach ($nodePaths as $nodePath) {
            if (is_dir($nodePath) && strpos($currentPath, $nodePath) === false) {
                $pathToAdd[] = $nodePath;
            }
        }
        
        if (!empty($pathToAdd)) {
            $newPath = implode(':', $pathToAdd) . ':' . $currentPath;
            putenv('PATH=' . $newPath);
            $_ENV['PATH'] = $newPath;
        }
        
        // Set NODE_PATH for module resolution
        $currentNodePath = getenv('NODE_PATH') ?: '';
        $nodePathEnv = $nodeModulesPath . ($currentNodePath ? ':' . $currentNodePath : '');
        putenv('NODE_PATH=' . $nodePathEnv);
        $_ENV['NODE_PATH'] = $nodePathEnv;
    }
}
