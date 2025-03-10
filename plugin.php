<?php
/**
 * Plugin Name: Links Per Page
 * Plugin URI: https://github.com/toineenzo/YOURLS-Links-Per-Page
 * Description: Show a custom number of displayed links per page with a configurable admin page.
 * Version: 1.0
 * Author: Toine Rademacher (toineenzo)
 * Author URI: https://github.com/toineenzo
 */

// Use the saved option (default is 50) when determining how many links to display
yourls_add_filter( 'admin_view_per_page', 'lpp_custom_number_of_displayed_links' );
function lpp_custom_number_of_displayed_links() {
    return yourls_get_option( 'links_per_page', 50 );
}

// Register the plugin admin page
yourls_add_action( 'plugins_loaded', 'lpp_register_admin_page' );
function lpp_register_admin_page() {
    // The first parameter is the page slug that will appear in the URL (e.g., plugins.php?page=lpp_config)
    yourls_register_plugin_page( 'lpp_config', 'Links Per Page', 'lpp_admin_page' );
}

// Callback function for the admin page
function lpp_admin_page() {
    $message = '';
    
    // Check if the form has been submitted
    if( isset( $_POST['links_per_page'] ) ) {
        // Convert the input to an integer and validate it
        $per_page = intval( $_POST['links_per_page'] );
        if( $per_page < 1 ) {
            $per_page = 50; // fallback to default if an invalid number is entered
        }
        // Retrieve the current setting
        $current = intval( yourls_get_option( 'links_per_page', 50 ) );
        
        // Only attempt update if the value has changed
        if( $per_page !== $current ) {
            // Update the option with the new value and capture success/failure
            $updated = yourls_update_option( 'links_per_page', $per_page );
            if( $updated ) {
                $message = '<div class="updated"><p style="color: green;">Updated links per page to ' . $per_page . '.</p></div>';
            } else {
                $message = '<div class="error"><p style="color: red;">Error: Could not update links per page.</p></div>';
            }
        }
    }

    // Retrieve the current setting (again, for form value)
    $current = yourls_get_option( 'links_per_page', 50 );

    // Display the configuration form
    echo '<h2>Configure Links Per Page</h2>';
    echo '<p>By default or when nothing is entered, the number of links per page will be 50. Please only enter a number between 1-999.</p>';
    echo '<form method="post">';
    echo '<label for="links_per_page">Links per page:</label> ';
    echo '<input type="number" name="links_per_page" id="links_per_page" value="' . $current . '" min="1" max="999" step="1" />';
    echo '<input type="submit" value="Save" />';
    echo '</form>';

    // Output the message below the form if it exists
    if( !empty( $message ) ) {
        echo $message;
    }
}
?>
