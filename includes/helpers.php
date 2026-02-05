<?php
/**
 * Helper functions
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper function to check if an icon list value should be hidden
 * Returns true if value is 0, '0', empty, or 'none'
 * Note: The actual filtering is now done by assets/js/icon-list-filter.js
 */
function should_hide_icon_value($value) {
    // Trim whitespace
    $value = trim($value);
    
    // Check for empty or whitespace
    if (empty($value)) {
        return true;
    }
    
    // Check for numeric zero at the start
    if (preg_match('/^0(\s|$)/', $value)) {
        return true;
    }
    
    // Check for 'none' (case-insensitive) at the start
    if (preg_match('/^none(\s|$)/i', $value)) {
        return true;
    }
    
    return false;
}
