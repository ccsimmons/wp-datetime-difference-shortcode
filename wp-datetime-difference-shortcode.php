<?php
/**
 * Plugin Name: WP Datetime Difference Shortcode
 * Description: Provides the [DatetimeDifference] shortcode (replacement for Date Counter).
 * Version:     1.0.1
 * Author:      Chris Simmons <ccsimmons@gmail.com>
* License: GPL-2.0-or-later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-datetime-difference-shortcode
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access.
}

/**
 * Shortcode: [DatetimeDifference startDate="01.07.1989" endDate="now" format="Y"]
 *
 * Notes:
 * - Accepts most DateTime-readable strings.
 * - "01.07.1989" is interpreted as dd.mm.YYYY.
 * - Output is intentionally restricted to numeric values only.
 */
function dds_datetime_difference_shortcode($atts) {

    // WordPress lowercases attribute keys; normalize defensively.
    $atts = (array) $atts;
    $normalized = [];
    foreach ($atts as $k => $v) {
        $normalized[strtolower($k)] = $v;
    }

    $atts = shortcode_atts([
        'startdate' => '',
        'enddate'   => 'now',
        'format'    => 'Y',
    ], $normalized, 'DatetimeDifference');

    $start_raw = trim((string) $atts['startdate']);
    if ($start_raw === '') {
        return '';
    }

    /**
     * Parse start date
     * Prefer dd.mm.YYYY, then fall back to DateTime parser
     */
    $start = null;

    if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $start_raw)) {
        $start = DateTime::createFromFormat('d.m.Y', $start_raw);

        if ($start instanceof DateTime) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count']) || !empty($errors['error_count'])) {
                $start = null;
            }
        }
    }

    if (!$start) {
        try {
            $start = new DateTime($start_raw);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Parse end date
     */
    $end_raw = trim((string) $atts['enddate']);
    try {
        $end = (strtolower($end_raw) === 'now' || $end_raw === '')
            ? new DateTime()
            : new DateTime($end_raw);
    } catch (Exception $e) {
        return '';
    }

    $diff = $start->diff($end);

    /**
     * Restrict format strictly to safe numeric outputs
     */
    $format = strtoupper(trim((string) $atts['format']));
    if (!in_array($format, ['Y', 'M', 'D'], true)) {
        $format = 'Y';
    }

    switch ($format) {
        case 'Y':
            return (string) $diff->y;

        case 'M':
            return (string) (($diff->y * 12) + $diff->m);

        case 'D':
            return ($diff->days === false) ? '' : (string) $diff->days;
    }

    // Defensive fallback (should never be reached)
    return (string) $diff->y;
}
add_shortcode('DatetimeDifference', 'dds_datetime_difference_shortcode');

/**
 * Render the admin "Details" page.
 */
function dds_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $data = get_file_data(__FILE__, [
        'Version' => 'Version',
        'Author'  => 'Author',
        'Name'    => 'Plugin Name',
        'Desc'    => 'Description',
    ]);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html($data['Name'] ?: 'WP Datetime Difference Shortcode') . '</h1>';

    if (!empty($data['Desc'])) {
        echo '<p>' . esc_html($data['Desc']) . '</p>';
    }

    echo '<table class="widefat striped" style="max-width:900px;">';
    echo '<tbody>';
    echo '<tr><th style="width:180px;">Version</th><td>' . esc_html($data['Version'] ?: '—') . '</td></tr>';
    echo '<tr><th>Author</th><td>' . esc_html($data['Author'] ?: '—') . '</td></tr>';
    echo '<tr><th>Shortcode</th><td><code>[DatetimeDifference startDate="01.07.1989" endDate="now" format="Y"]</code></td></tr>';
    echo '<tr><th>Formats</th><td><code>Y</code> = years, <code>M</code> = total months, <code>D</code> = total days</td></tr>';
    echo '</tbody>';
    echo '</table>';

    echo '<h2 style="margin-top:24px;">Examples</h2>';
    echo '<ul style="list-style:disc; padding-left:24px;">';
    echo '<li><code>[DatetimeDifference startDate="01.07.1989" endDate="now" format="Y"]</code> → years since July 1, 1989</li>';
    echo '<li><code>[DatetimeDifference startDate="2020-01-01" endDate="now" format="M"]</code> → total months since Jan 1, 2020</li>';
    echo '<li><code>[DatetimeDifference startDate="2025-01-01" endDate="now" format="D"]</code> → total days since Jan 1, 2025</li>';
    echo '</ul>';

    echo '<p style="margin-top:16px;"><strong>Tip:</strong> The <code>dd.mm.YYYY</code> format (e.g. <code>01.07.1989</code>) is supported.</p>';
    echo '</div>';
}