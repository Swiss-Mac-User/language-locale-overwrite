<?php
/*
 * Plugin Name:			Language Locale Overwrite
 * Plugin URI:			https://github.com/Swiss-Mac-User/language-locale-overwrite
 * Description:			This plugin allows overwriting the general Language Locale and on individual pages.
 * Version:				1.0.0
 * Requires at least:	6.2
 * Requires PHP:		7.4
 * Author:				Swiss-Mac-User
 * Author URI:			https://github.com/Swiss-Mac-User/
 * Text Domain:			custom-language-locale
 */

 /** Exit when file is opened in webbrowser */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Add fields only if logged-in User in the Backend */
if ( is_admin() ) {
    add_action( 'add_meta_boxes', 'add_custom_meta_boxes' );
    add_action( 'save_post', 'save_language_locale_overwrite' );
}
function add_custom_meta_boxes() {
    add_meta_box( 'language-locale-overwrite', __( 'Language Locale', 'language_locale_overwrite' ), 'show_language_locale_meta_box', ['post', 'page'] );
}

/**
 * Render Meta Box content.
 * @link https://metabox.io/how-to-create-custom-meta-boxes-custom-fields-in-wordpress/
 * @param WP_Post $post The post object.
 */
function show_language_locale_meta_box( $post ) {

    /** Get chached Views via get_post_meta from the database. */
    wp_nonce_field( 'language_locale_meta_box_inner', 'language_locale_meta_box_inner_nonce' ); // Add an nonce field for save()
    $value = get_post_meta( $post->ID, 'language_locale_overwrite', true );

    /** Display the meta box content. */
    ?>
    <div id="llo" class="rwmb-field rwmb-text-wrapper">
        <div class="rwmb-label">
            <label for="language_locale_overwrite">Set Locale to</label>
        </div>
        <div class="rwmb-input">
            <input type="text" class="rwmb-text" id="language_locale_overwrite" name="language_locale_overwrite" maxlength="5"
            value="<?php echo esc_attr( get_post_meta( $post->ID, 'language_locale_overwrite', true ) ); ?>">
            <p id="language_locale_overwrite_placeholder" class="description">Default: <?php echo get_bloginfo( 'language' ); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Save the meta when the post is saved.
 *
 * We need to verify this came from the our screen and with proper authorization,
 * because save_post can be triggered at other times.
 *
 * @link https://metabox.io/how-to-create-custom-meta-boxes-custom-fields-in-wordpress/
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_language_locale_overwrite( $post_id ) {

    /** If this is an autosave, our form has not been submitted, so we don't want to do anything. */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }

    /** Check if our nonce is set. */
    if ( ! isset( $_POST['language_locale_meta_box_inner_nonce'] ) ) return $post_id;

    $nonce = $_POST['language_locale_meta_box_inner_nonce'];

    /** Verify that the nonce is valid. */
    if ( ! wp_verify_nonce( $nonce, 'language_locale_meta_box_inner' ) ) return $post_id;

    /** Check the user's permissions. */
    if ( 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    /** Sanitize the user input. */
    $meta_box_fieldvalue = sanitize_text_field( $_POST['language_locale_overwrite'] );

    /** Add the meta field value to the database. */
    update_post_meta( $post_id, 'language_locale_overwrite', $meta_box_fieldvalue );
}
