<?php

if (!function_exists('highlight_keyword')) {
    /**
     * Highlights the given keyword in the text with a neo-brutalist style.
     *
     * @param string $text The original text.
     * @param string $keyword The keyword to highlight.
     * @return string The text with the keyword highlighted.
     */
    function highlight_keyword($text, $keyword)
    {
        if (!$keyword) {
            return $text;
        }
        // Use a more robust regex to handle special characters in keyword
        $escapedKeyword = preg_quote($keyword, '/');
        // Highlight with neo-brutalist yellow background
        return preg_replace(
            '/(' . $escapedKeyword . ')/i',
            '<span style="background-color: #ffeb3b; border: 1px solid #000; padding: 0 2px;">$1</span>',
            $text
        );
    }
}
