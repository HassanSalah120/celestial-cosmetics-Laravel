<?php

namespace App\Helpers;

class GridJsHelper
{
    /**
     * Create a Grid.js HTML content object.
     *
     * @param string $html
     * @return array
     */
    public static function html($html)
    {
        return [
            '_html' => $html
        ];
    }
}

if (!function_exists('gridjs_html')) {
    /**
     * Helper function to create Grid.js HTML content.
     *
     * @param string $html
     * @return array
     */
    function gridjs_html($html)
    {
        return GridJsHelper::html($html);
    }
} 