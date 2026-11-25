<?php
namespace Framework\Assets;

/**
 * Reads the manifest Vite writes to public/build and turns entry points
 * (paths such as resources/assets/js/app.js) into URLs and HTML tags.
 */
class ViteManifest
{
    private array $chunks;

    public function __construct(string $manifestPath, private string $base = '/build/')
    {
        if (! is_file($manifestPath)) {
            throw new \RuntimeException(
                "The Vite manifest does not exist: {$manifestPath}. Run npm run build."
            );
        }
        $chunks = json_decode(file_get_contents($manifestPath), true);
        if (! is_array($chunks)) {
            throw new \RuntimeException("The Vite manifest is not valid JSON: {$manifestPath}");
        }
        $this->chunks = $chunks;
    }

    /**
     * Vite 5+ writes the manifest to build/.vite/manifest.json; older
     * versions wrote build/manifest.json.
     */
    public static function fromPublicPath(string $publicPath, string $base = '/build/'): self
    {
        $buildPath = rtrim($publicPath, '/') . '/build';
        $manifestPath = is_file($buildPath . '/.vite/manifest.json')
            ? $buildPath . '/.vite/manifest.json'
            : $buildPath . '/manifest.json';
        return new self($manifestPath, $base);
    }

    /** The URL of the file Vite built for an entry point. */
    public function url(string $entry): string
    {
        return $this->base . $this->entry($entry)['file'];
    }

    /**
     * The tags a page needs to run a script entry point: a stylesheet link
     * for any CSS it imports, a modulepreload link for each shared chunk it
     * imports, and the module script itself.
     */
    public function scriptTags(string $entry): string
    {
        $chunk = $this->entry($entry);
        $seen = [];
        $imports = $this->importedChunks($this->key($entry), $seen);

        $css = $chunk['css'] ?? [];
        foreach ($imports as $import) {
            array_push($css, ...($this->chunks[$import]['css'] ?? []));
        }

        $tags = [];
        foreach (array_unique($css) as $file) {
            $tags[] = '<link rel="stylesheet" href="' . $this->escape($this->base . $file) . '">';
        }
        foreach ($imports as $import) {
            $tags[] = '<link rel="modulepreload" href="' . $this->escape($this->base . $this->chunks[$import]['file']) . '">';
        }
        $tags[] = '<script type="module" src="' . $this->escape($this->base . $chunk['file']) . '"></script>';
        return implode("\n", $tags);
    }

    private function entry(string $entry): array
    {
        $key = $this->key($entry);
        if (! isset($this->chunks[$key]['file']) || empty($this->chunks[$key]['isEntry'])) {
            throw new \RuntimeException(
                "Unable to locate Vite entry: {$entry}. Add it to vite.entries.js and run npm run build."
            );
        }
        return $this->chunks[$key];
    }

    private function key(string $entry): string
    {
        return ltrim($entry, '/');
    }

    /** Every chunk an entry imports statically, directly or indirectly. */
    private function importedChunks(string $key, array &$seen): array
    {
        $found = [];
        foreach ($this->chunks[$key]['imports'] ?? [] as $import) {
            if (isset($seen[$import]) || ! isset($this->chunks[$import])) {
                continue;
            }
            $seen[$import] = true;
            $found[] = $import;
            array_push($found, ...$this->importedChunks($import, $seen));
        }
        return $found;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
