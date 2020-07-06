<?php

if (! function_exists('mix')) {
    function mix($path)
    {
        static $manifest;
        $publicPath = '/public';
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
          $rootPath = '/srv/http/pyangelo.com/public';
        }
        else {
          $rootPath = $_SERVER['DOCUMENT_ROOT'];
        }
        if (! $manifest) {
            if (! file_exists($manifestPath = ($rootPath . '/mix-manifest.json') )) {
                throw new Exception('The Mix manifest does not exist.');
            }
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
        $path = "/{$path}";
        if (! array_key_exists($path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}. Please check your ".
                'webpack.mix.js output paths and try again.'
            );
        }
        return $manifest[$path];
    }
}

function csdd($object) {
  var_dump($object);
  exit;
}
