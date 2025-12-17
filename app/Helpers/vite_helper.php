<?php
/**
 * Vite Helper for CodeIgniter 4
 * Helps load Vite assets in development and production
 */

if (!function_exists('vite_assets')) {
    /**
     * Generate Vite asset tags for CSS and JS
     * 
     * @param string|array $entries Entry points (e.g., 'js/app.js', 'css/app.css')
     * @return string HTML tags for assets
     */
    function vite_assets(string|array $entries): string
    {
        $entries = is_array($entries) ? $entries : [$entries];
        $isDev = getenv('CI_ENVIRONMENT') === 'development';
        $devServerUrl = 'http://localhost:5173/assets/dist';
        $manifestPath = FCPATH . 'assets/dist/.vite/manifest.json';
        
        $html = '';
        
        if ($isDev && isViteDevServerRunning($devServerUrl)) {
            // Development mode - load from Vite dev server
            $html .= '<script type="module" src="' . $devServerUrl . '/@vite/client"></script>' . PHP_EOL;
            
            foreach ($entries as $entry) {
                $extension = pathinfo($entry, PATHINFO_EXTENSION);
                if ($extension === 'css') {
                    $html .= '<link rel="stylesheet" href="' . $devServerUrl . '/' . $entry . '">' . PHP_EOL;
                } else {
                    $html .= '<script type="module" src="' . $devServerUrl . '/' . $entry . '"></script>' . PHP_EOL;
                }
            }
        } else {
            // Production mode - load from manifest
            if (!file_exists($manifestPath)) {
                return '<!-- Vite manifest not found. Run: npm run build -->';
            }
            
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
            foreach ($entries as $entry) {
                if (!isset($manifest[$entry])) {
                    $html .= '<!-- Entry not found in manifest: ' . $entry . ' -->' . PHP_EOL;
                    continue;
                }
                
                $asset = $manifest[$entry];
                $file = '/assets/dist/' . $asset['file'];
                
                $extension = pathinfo($asset['file'], PATHINFO_EXTENSION);
                if ($extension === 'css') {
                    $html .= '<link rel="stylesheet" href="' . $file . '">' . PHP_EOL;
                } else {
                    $html .= '<script type="module" src="' . $file . '"></script>' . PHP_EOL;
                }
                
                // Load CSS imports if any
                if (isset($asset['css'])) {
                    foreach ($asset['css'] as $cssFile) {
                        $html .= '<link rel="stylesheet" href="/assets/dist/' . $cssFile . '">' . PHP_EOL;
                    }
                }
            }
        }
        
        return $html;
    }
}

if (!function_exists('isViteDevServerRunning')) {
    /**
     * Check if Vite dev server is running
     */
    function isViteDevServerRunning(string $url): bool
    {
        // Skip check - always use production build for now
        // Uncomment below if you want to use dev server
        return false;
        
        /*
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 400;
        */
    }
}

if (!function_exists('vite_asset')) {
    /**
     * Get a single asset URL from Vite manifest
     */
    function vite_asset(string $entry): string
    {
        $manifestPath = FCPATH . 'assets/dist/.vite/manifest.json';
        
        if (!file_exists($manifestPath)) {
            return $entry;
        }
        
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (!isset($manifest[$entry])) {
            return $entry;
        }
        
        return '/assets/dist/' . $manifest[$entry]['file'];
    }
}
