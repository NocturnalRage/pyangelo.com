<?php

if (! function_exists('viteManifest')) {
    function viteManifest(): Framework\Assets\ViteManifest
    {
        static $manifest;
        $publicPath = empty($_SERVER['DOCUMENT_ROOT'])
            ? dirname(__DIR__, 2) . '/public'
            : $_SERVER['DOCUMENT_ROOT'];
        return $manifest ??= Framework\Assets\ViteManifest::fromPublicPath($publicPath);
    }
}

if (! function_exists('vite')) {
    /** The URL Vite built for an entry point, such as resources/assets/sass/app.scss. */
    function vite(string $entry): string
    {
        return viteManifest()->url($entry);
    }
}

if (! function_exists('viteScript')) {
    /** The tags that load a script entry point, such as resources/assets/js/app.js. */
    function viteScript(string $entry): string
    {
        return viteManifest()->scriptTags($entry);
    }
}

if (! function_exists('mix')) {
    /**
     * The laravel-mix helper, kept for one release so the first Vite deploy
     * works: publish.sh swaps src/ and views/ one after the other, and the old
     * views call mix() until they are replaced. It reads the old
     * public/mix-manifest.json, which the deploy leaves in place.
     * Remove it, and that manifest, after the first Vite deploy.
     */
    function mix($path)
    {
        static $manifest;
        $publicPath = empty($_SERVER['DOCUMENT_ROOT'])
            ? dirname(__DIR__, 2) . '/public'
            : $_SERVER['DOCUMENT_ROOT'];
        if (! $manifest) {
            $manifestPath = $publicPath . '/mix-manifest.json';
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
        $path = "/{$path}";
        if (! array_key_exists($path, $manifest)) {
            throw new Exception("Unable to locate Mix file: {$path}.");
        }
        return $manifest[$path];
    }
}

function csdd($object) {
  var_dump($object);
  exit;
}
