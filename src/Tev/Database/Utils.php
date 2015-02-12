<?php
namespace Tev\Database;

/**
 * Useful database utility methods.
 */
class Utils
{
    /**
     * Generate Wordpress WPDB placeholders for inserts/updates, based on
     * the types of values in the given array.
     *
     * See: http://codex.wordpress.org/Class_Reference/wpdb#Placeholders
     *
     * @param  array $data Key/value pairs
     * @return array       Placeholders
     */
    public function generatePlaceholders(array $data)
    {
        $placeholders = array();

        foreach ($data as $d) {
            if (is_float($d)) {
                $placeholders[] = '%f';
            } elseif (is_int($d)) {
                $placeholders[] = '%d';
            } else {
                $placeholders[] = '%s';
            }
        }

        return $placeholders;
    }

    /**
     * Generate a slug from the given field.
     *
     * Uses `sanitize_title()` to create a slug. If $unique is provided, this
     * method will check if the slug already exists in $unique. If it does,
     * it will append '-1', '-2' etc to the slug until a unique value is found.
     *
     * @param  string $data   Data to slugify
     * @param  array  $unique Array of values to ensure slug is unique against
     * @return string         Generated slug
     */
    public function createSlug($data, array $unique = array())
    {
        $slug = sanitize_title($data);
        $next = 1;

        while (in_array($slug, $unique)) {
            $slug = sanitize_title($data . ' ' . $next++);
        }

        return $slug;
    }
}
