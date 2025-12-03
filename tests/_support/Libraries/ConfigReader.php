<?php

namespace Tests\Support\Libraries;

use Config\App;

/**
 * Class ConfigReader
 *
 * An extension of BaseConfig that prevents the constructor from
 * loading external values. Used to read actual local values from
 * a config file.
 */
class ConfigReader extends App
{
    public function __construct()
    {
        // Path to the original App.php file
        $appConfigPath = APPPATH . 'Config' . DIRECTORY_SEPARATOR . 'App.php';

        // Read the file content
        $content = file_get_contents($appConfigPath);

        // Use regex to find the public string $baseURL declaration
        // This regex looks for:
        // public string $baseURL = 'some_url';
        // public string $baseURL = "some_url";
        // public string $baseURL = '';
        if (preg_match("/public string \$baseURL\s*=\s*['\"]([^'\"]*)['\"]\s*;/", $content, $matches)) {
            $this->baseURL = $matches[1]; // Set the baseURL from the file
        } else {
            // Fallback if not found (e.g., if baseURL is not directly assigned a string literal)
            // In a real scenario, you might want to throw an exception or log an error
            $this->baseURL = '';
        }
    }
}
