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

function csdd($object) {
  var_dump($object);
  exit;
}
