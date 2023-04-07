<?php
/*
Plugin Name: Feedback 
Description: A simple feedback form plugin for WordPress.
Version: 1.0
Author: Hamza Essouli
*/

// Create the feedback table when the plugin is activated
register_activation_hook( __FILE__, 'feedback_form_activate' );
function feedback_form_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'feedbacks';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        feedback TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Enqueue the required scripts and styles
add_action( 'wp_enqueue_scripts', 'feedback_form_enqueue_scripts' );
function feedback_form_enqueue_scripts() {
    wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array( 'jquery' ) );
}

// Add the feedback form shortcode
add_shortcode( 'feedback_form', 'feedback_form_shortcode' );
function feedback_form_shortcode() {
    $form_html = '';

    if ( isset( $_POST['feedback_submit'] ) ) {
        // Handle form submission
        global $wpdb;

        $name = sanitize_text_field( $_POST['full_name'] );
        $feedback = sanitize_text_field( $_POST['feedback'] );

        $table_name = $wpdb->prefix . 'feedbacks';

        $wpdb->insert( 
            $table_name, 
            array( 
                'name' => $name, 
                'feedback' => $feedback 
            ), 
            array( 
                '%s', 
                '%s' 
            ) 
        );

        $form_html .= '<div class="alert alert-success" role="alert">Thank you for your feedback!</div>';
    } else {
        // Display the form
        $form_html .= '<form method="post" action="' . get_permalink() . '">';
        $form_html .= '<div class="form-group">';
        $form_html .= '<label for="full_name">Full Name</label>';
        $form_html .= '<input type="text" class="form-control" id="full_name" name="full_name" required>';
        $form_html .= '</div>';
        $form_html .= '<div class="form-group">';
        $form_html .= '<label for="feedback">Feedback</label>';
        $form_html .= '<textarea class="form-control" id="feedback" name="feedback" rows="5" required></textarea>';
        $form_html .= '</div>';
        $form_html .= '<button type="submit" class="btn btn-primary" name="feedback_submit">Submit</button>';
        $form_html .= '</form>';
    }

    return $form_html;
}

