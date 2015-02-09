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
}
