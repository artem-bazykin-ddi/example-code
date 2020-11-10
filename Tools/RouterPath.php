<?php
declare(strict_types=1);

namespace App\Tools;

/**
 * Class RouterPath
 * @package App\Tools
 */
class RouterPath
{
    /**
     * Dynamic app routes
     *
     * @param string $folder
     *
     * @return void
     */
    public function includeRoutes(string $folder): void
    {
        $directory = $folder;
        $handle = opendir($directory);
        $directoryList = [$directory];

        while (false !== ($filename = readdir($handle))) {
            if ($filename !== '.' && $filename !== '..' && is_dir($directory.$filename)) {
                $directoryList[] = $directory . $filename . DIRECTORY_SEPARATOR;
            }
        }

        foreach ($directoryList as &$directory) {
            foreach (glob($directory.'*.php') as &$filename) {
                require "{$filename}";
            }
        }
    }
}
