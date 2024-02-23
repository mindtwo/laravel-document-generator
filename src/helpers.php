<?php

// TODO add document helper

if (! function_exists('rsearch')) {
    /**
     * List all files in folder and sub folders using glob
     *
     * @param  string  $folder
     * @param  string  $regPattern
     * @return array
     */
    function rsearch(string $folder, string $regPattern)
    {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $regPattern, RegexIterator::GET_MATCH);
        $fileList = [];
        foreach ($files as $file) {
            $fileList = array_merge($fileList, $file);
        }

        return $fileList;
    }
}

if (! function_exists('extract_namespace')) {
    function extract_namespace($file)
    {
        $ns = null;
        $handle = fopen($file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return $ns;
    }
}
