<?php

// =============================================================================
// Time Difference
// 
// Released under the GNU General Public Licence v2
// http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
// CodeMirror library is released under a MIT-style license
// http://codemirror.net/LICENSE
// 
// Please refer all questions/requests to: mdjekic@gmail.com
//
// This is an add-on for WordPress
// http://wordpress.org/
// =============================================================================

// =============================================================================
// This piece of software is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.
// =============================================================================

/*
  Plugin Name: Time Difference
  Plugin URI: http://milos.djekic.net/my-software/time-difference
  Description: Display time difference between now and a certain date/time in pages, posts and text widgets.
  Version: 1.0
  Author: Miloš Đekić
  Author URI: http://milos.djekic.net
 */

// Time Since version
define("TIME-DIFFERENCE", '1.0');

/**
 * Calculates and formats the time difference
 *
 * @param int $timestamp
 * @param string $units
 *
 * @return string
 */
function time_since_diff($timestamp, $units)
{
    // get time elapsed
    $elapsed_time = abs(time() - $timestamp);

    // determine elapsed number
    switch ($units) {
        case 'years':
            $elapsed = $elapsed_time / (60 * 60 * 24 * 30 * 356);
            break;
        case 'months':
            $elapsed = $elapsed_time / (60 * 60 * 24 * 30);
            break;
        case 'days':
            $elapsed = $elapsed_time / (60 * 60 * 24);
            break;
        case 'hours':
            $elapsed = $elapsed_time / (60 * 60);
            break;
        case 'minutes':
            $elapsed = $elapsed_time / 60;
            break;
        default:
            return "Error: invalid unit";
    }

    // round the value
    $elapsed = floor($elapsed);

    // return appropriate value
    return $elapsed . " " . (($elapsed == 1) ? substr($units, 0, -1) : $units);
}

/**
 * Replaces a time tag entry with a proper text value
 *
 * @param string $data
 *
 * @return string
 */
function time_since_replace($data)
{
    // clear tags and split
    $data = str_replace('[time]', '', $data);
    $data = str_replace('[/time]', '', $data);
    $data = explode(',', $data);

    // make sure data has the appropriate length
    if (count($data) != 2) return 'Error: invalid structure';

    // get date
    $date = strtotime($data[0]);

    // check date appropriate
    if ($date === false) return 'Error: invalid date';

    // get units
    $units = $data[1];

    // calculate time difference
    return time_since_diff($date, $units);
}

/**
 * Renders HTML content to find tags and replace
 * them with proper text values
 *
 * @param $content
 * @return mixed
 */
function time_since_render($content)
{
    // define regex for searching for inline tweets
    $regex = '/\[time\](.*?)\[\/time\]/ism';

    // search for inline tweets
    $count = preg_match_all($regex, $content, $matches);

    // do nothing if there were no matches
    if ($count == 0) return $content;

    // handle all matches
    if (isset($matches[0])) foreach ($matches[0] as $matches_data) {
        // calculate time elapsed
        $content = str_replace($matches_data, time_since_replace($matches_data), $content);
    }

    // return content
    return $content;
}

// add rendering filter for posts/pages
add_filter('the_content', 'time_since_render');

// add rendering filter for text widgets content
add_filter('widget_text', 'time_since_render');

?>