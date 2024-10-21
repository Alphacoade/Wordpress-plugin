<?php
// edit-form.php

// Include the WordPress functions
require_once('../../../wp-load.php');

// Get the WordPress admin header
get_header();

global $wpdb;
$table_name = $wpdb->prefix . 'sample_form_data';

// Get the ID of the row to be edited
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get the current data of the row
$row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

// Display the form with the current data
echo '<h1>Edit Registration</h1>';
echo '<form method="post">';
echo '<label for="firstName">First Name *</label>';
echo '<input type="text" id="firstName" name="firstName" value="' . esc_attr($row->first_name) . '" required/>';
// Add the rest of the fields...
echo '<input type="submit" name="update" value="Update" />';
echo '</form>';

// Update the data if the form is submitted
if (isset($_POST['update'])) {
    $wpdb->update(
        $table_name,
        array(
            'first_name' => sanitize_text_field($_POST['firstName']),
            // Add the rest of the fields...
        ),
        array('id' => $id)
    );
    echo '<p>Data updated successfully.</p>';
}

// Get the WordPress admin footer
get_footer();