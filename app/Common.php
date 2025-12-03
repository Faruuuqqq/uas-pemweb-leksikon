<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

// Helper untuk highlight teks
if (!function_exists('highlight_keyword')) {
    function highlight_keyword($text, $keyword) {
        if (!$keyword) return $text;
        // Highlight dengan background kuning neo-brutalist
        return preg_replace(
            '/(' . preg_quote($keyword, '/') . ')/i', 
            '<span style="background-color: #ffeb3b; border: 1px solid #000; padding: 0 2px;">$1</span>', 
            $text
        );
    }
}