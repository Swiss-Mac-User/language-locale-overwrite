<?php
/*
 * Plugin Name:			Language Locale Overwrite
 * Plugin URI:			https://github.com/Swiss-Mac-User/language-locale-overwrite
 * Description:			This plugin allows overwriting the general Language Locale and on individual pages.
 * Version:				2.1.0
 * Requires at least:	6.2
 * Requires PHP:		7.4
 * Author:				Swiss-Mac-User
 * Author URI:			https://github.com/Swiss-Mac-User/
 *
 * Text Domain:			custom-language-locale
 */

/** Exit when file is opened in webbrowser */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Plugin Settings to Database, editable in Admin area
 */
/* Runs when plugin is activated */
register_activation_hook( __FILE__, 'enable_language_locale_overwrite_plugin' );
function enable_language_locale_overwrite_plugin() {
    add_option( 'custom_global_language_html_lang' );
    add_option( 'custom_global_language_og_territory' );
}
/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'disable_language_locale_overwrite_plugin' );
function disable_language_locale_overwrite_plugin() {
    delete_option( 'custom_global_language_html_lang' );
    delete_option( 'custom_global_language_og_territory' );
}

/**
 * Mapping hooks to function filters
 */
if ( is_admin() ) {
	/** Apply hooks only if logged-in User in the Backend */
	add_action( 'admin_init', 'language_locale_overwrite_admin_settings' );
	add_action( 'add_meta_boxes', 'add_custom_meta_boxes' );
	add_action( 'save_post', 'save_language_locale_overwrite' );
} else {
	/** Apply hooks in the Frontend (all users / visitors) */
	add_filter( 'language_attributes', 'set_custom_language_attributes' );
	add_action( 'wp_head', 'add_alternate_hreflang_metatags', 5);
}

/**
 * Add Plugin specific Admin Settings
 */
function language_locale_overwrite_admin_settings() {

	/** Add new Settings Section to "Settings » General" /wp-admin/options-general.php */
	add_settings_section(
		'llosettings'
		,__( '<hr>WordPress Language Locale override', 'custom-language-locale' )
		,'language_locale_overwrite_admin_section'
		,'general'
	);

	$fieldargs = array(
				 'type' => 'string'
				,'sanitize_callback' => 'sanitize_text_field'
				,'default' => NULL
			);

	/** Add Settings Field «HTML lang override» to Plugin section */
	register_setting( 'general', 'custom_global_language_html_lang', $fieldargs );
    add_settings_field(
		 'llo_global'
		,__( 'Change HTML lang attribute', 'custom-language-locale' )
		,'language_locale_overwrite_admin_content_llo'
		,'general'
		,'llosettings'
		,[
			 'id' => 'llo'
			,'option_name' => 'custom_global_language_html_lang'
			,'label_for' => 'llo_global'
		]
	);
	/** Add Settings Field «og:local Territory» to Plugin section */
	register_setting( 'general', 'custom_global_language_og_territory', $fieldargs );
    add_settings_field(
		 'llo_ogc'
		,__( 'Custom Country code', 'custom-language-locale' )
		,'language_locale_overwrite_admin_content_ogt'
		,'general'
		,'llosettings'
		,[
			'id' => 'ogt'
		   ,'option_name' => 'custom_global_language_og_territory'
		   ,'label_for' => 'llo_ogc'
	   ]
	);

}
/**
 * Settings Section-content for "Settings » General"
 */
function language_locale_overwrite_admin_section(){
    _e( '<p class="description">Custom Language override for all Pages and Posts</p>', 'custom-language-locale' );
}
/**
 * Settings Field for «HTML lang override»
 */
function language_locale_overwrite_admin_content_llo(){
?>
	<input type="text" id="llo_global" name="custom_global_language_html_lang" maxlength="5"
		value="<?php echo esc_attr( strtolower( get_option( 'custom_global_language_html_lang' ) ) ); ?>">
	<p class="description">
		<?php _e( 'Applied to all Posts/Pages (unless overwritten)', 'custom-language-locale' ); ?>
	</p>
<?php }
/**
 * Settings Field for «og:local Territory»
 */
function language_locale_overwrite_admin_content_ogt(){
?>
	<input type="text" id="llo_ogc" name="custom_global_language_og_territory" maxlength="2"
		value="<?php echo esc_attr( strtoupper( get_option( 'custom_global_language_og_territory' ) ) ); ?>">
	<p class="description">
		<?php _e( '2-char ISO Country Code used for og:locale', 'custom-language-locale' ); ?>
	</p>
<?php }


/**
 * Map custom Meta Box Section to functions for Posts and Pages
 */
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
	$global_locale_changed = get_global_custom_language_locale();
	$custom_locale = get_post_meta( $post->ID, 'language_locale_overwrite', true );
	$alternate_lang_post_ids = get_post_meta( $post->ID, 'alternate_lang_posts', true );

	/** Display the meta box content. */
	?>
	<div id="llo" class="rwmb-field rwmb-text-wrapper">
		<div class="rwmb-label">
			<label for="language_locale_overwrite">Change Locale of this <?php echo ucfirst($post->post_type); ?></label>
		</div>
		<div class="rwmb-input">
			<input type="text" class="rwmb-text" id="language_locale_overwrite" name="language_locale_overwrite" maxlength="5"
			 value="<?php echo esc_attr( $custom_locale ); ?>">
			<p id="language_locale_overwrite_placeholder" class="description">
				<?php echo ($global_locale_changed !== false ? 'Global <a href="'.get_admin_url( null, 'options-general.php#llo_global' ).'" target="_blank">changed default</a>: <b>'.esc_attr($global_locale_changed).'</b> ' : '' ).'(WordPress default: '.get_bloginfo( 'language' ).')'; ?>
			</p>
		</div>
	</div><?php
	$find_all_custom_locales_used = get_all_custom_language_locales();
	if ( !empty($find_all_custom_locales_used) && is_array($find_all_custom_locales_used) )
	{
		foreach( $find_all_custom_locales_used as $other_custom_locale ) {
			/** Ignore empty & current Post/Page's locale (no alternate hreflang needed) */
			if (!empty($other_custom_locale) && $other_custom_locale != $custom_locale)
			{
				$find_alternate_posts = get_posts_with_language_locale_overwrite( $post->post_type, $other_custom_locale );
				if ( !empty($find_alternate_posts) ) { ?>
				<div id="ahl-<?php echo esc_attr($other_custom_locale); ?>" class="rwmb-field rwmb-text-wrapper">
					<div class="rwmb-label">
						<label for="alternate_lang_post_<?php echo esc_attr($other_custom_locale); ?>">
							Link alternate lang <?php echo ucfirst($post->post_type) . ': ' . esc_attr($other_custom_locale); ?>
						</label>
					</div>
					<div class="rwmb-input">
						<select class="rwmb-select" id="alternate_lang_post_<?php echo esc_attr($other_custom_locale); ?>" name="alternate_lang_posts[<?php echo esc_attr($other_custom_locale); ?>]">
							<option value="">--- None selected ---</option>
							<?php
							foreach ($find_alternate_posts as $alternate_post) {
								$selected = ( isset($alternate_lang_post_ids[$other_custom_locale]) && ($alternate_post->ID === $alternate_lang_post_ids[$other_custom_locale]) ? 'selected' : '' );
								printf('<option value="%d" %s>%s</option>',
										esc_attr( $alternate_post->ID )
										,$selected
										,esc_html( $alternate_post->post_title )
									);
							}
							?>
						</select>
						<p class="description">
							Select <?php echo ucfirst($post->post_type); ?> with <i>similar Content</i> but in locale: <b><?php echo esc_attr($other_custom_locale); ?></b>
						</p>
					</div>
				</div>
			<?php }
			}
		}
	}
}

/**
 * Get posts with language locale overwrite meta_key value.
 *
 * @link https://developer.wordpress.org/reference/functions/get_posts/
 * @link https://developer.wordpress.org/reference/functions/get_pages/
 * @param string $type The post type.
 * @param string $locale The language locale.
 * @return array|bool The array of posts with language locale overwrite or false if no posts found.
 */
function get_posts_with_language_locale_overwrite( $type, $locale ) {
	switch ($type) {
		case 'post':
			$posts_array = get_posts([
										'meta_key' => 'language_locale_overwrite'
										,'meta_value' => $locale
										,'posts_per_page' => -1 // All
										,'orderby' => 'name'
										,'order' => 'ASC'
									]);
			break;

		case 'page':
			$posts_array = get_pages([
										'meta_key' => 'language_locale_overwrite'
										,'meta_value' => $locale
										,'number' => 0 // All
										,'sort_column' => 'name'
										,'sort_order' => 'ASC'
									]);
			break;

		default:
			$posts_array = false;
	}
	return $posts_array;
}

/**
 * Get all custom language locales.
 *
 * This function retrieves all the language locales that have been set
 * as custom values for the 'language_locale_overwrite' meta key.
 *
 * @param bool $no_duplicates If true, filters out any duplicates. Default: true
 * @return array An array of language locales.
 */
function get_all_custom_language_locales( $no_duplicates=true ) {

	$defined_language_locales = array();
	$list_of_custom_locales = array();
	$query = new WP_Query([
		 'meta_key' => 'language_locale_overwrite'
		,'meta_compare' => 'EXISTS'
		,'fields' => 'ids'
	]);
	if ( $query->have_posts() )
	{
		foreach( $query->posts as $post_id ) {
			$defined_language_locales[] = get_post_meta( $post_id, 'language_locale_overwrite', true );
		}
		$list_of_custom_locales = ( $no_duplicates ? array_unique($defined_language_locales) : $defined_language_locales);
		return $list_of_custom_locales;
	}
	return $list_of_custom_locales;

}

/**
 * Get WP language locale from settings.
 *
 * @return string The defined WP language locale.
 */
function get_default_wp_language_locale(  ) {

	return get_locale();

}

/**
 * Get custom global locale if different to WP language locale.
 * @uses get_default_wp_language_locale()
 * @uses get_option('custom_global_language_html_lang')
 * @return string|bool The custom global language locale, or false if WP default.
 */
function get_global_custom_language_locale( ) {

	$overwritten_global_locale = false;
	$custom_locale = get_option('custom_global_language_html_lang');
	if ( !empty($custom_locale) && get_default_wp_language_locale() !== $custom_locale ) {
		$overwritten_global_locale = $custom_locale;
	}
	return $overwritten_global_locale;

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
		if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}

	/** Sanitize the user input. */
	$meta_box_fieldvalue = sanitize_text_field( $_POST['language_locale_overwrite'] );
	foreach ( $_POST['alternate_lang_posts'] as $alternate_lang => $alternate_post_id ) {
		if (!empty($alternate_post_id)) $alternate_lang_post_ids[$alternate_lang] = absint( $alternate_post_id );
	}

	/** Add the meta field values to the database. */
	update_post_meta( $post_id, 'language_locale_overwrite', $meta_box_fieldvalue );
	update_post_meta( $post_id, 'alternate_lang_posts', $alternate_lang_post_ids );

}

/**
 * Modify get_bloginfo( 'language' ) to use 2-char ISO-Code.
 *
 * @link https://wordpress.stackexchange.com/a/210101/110615
 * @uses get_global_custom_language_locale()
 */
function set_custom_language_attributes( $wp_default_lang )
{

	/** Check if HTML Lang for single Post/Page differs from global locale */
	$global_locale_changed = get_global_custom_language_locale();
	$single_custom_locale = false;
	if ( is_singular() && !empty( get_post_meta( get_the_ID(), 'language_locale_overwrite', true ) ) ) {
		$single_custom_locale = esc_attr( get_post_meta( get_the_ID(), 'language_locale_overwrite', true ) );
	}
	$custom_locale = ( $single_custom_locale !== false ? $single_custom_locale : $global_locale_changed );

	/** If applicable, apply the modified `lang="..."` HTML tag attribute */
	$html_lang = ($custom_locale !== false ? 'lang="'.$custom_locale.'"' : $wp_default_lang);

	return $html_lang;

}

/**
 * Add required alternate hreflang Meta Tags to wp_head().
 *
 * @link https://www.tutorialexample.com/wordpress-add-html-meta-tag-in-header-wordpress-tutorial/
 * @uses get_global_custom_language_locale(), get_default_wp_language_locale()
 */
function add_alternate_hreflang_metatags() {

	if( is_singular() && !empty(get_the_ID()) ) {
		$alternate_lang_posts = get_post_meta( get_the_ID(), 'alternate_lang_posts', true );

		/** One or multiple Alternate Hreflang URLs found */
		if ( !empty($alternate_lang_posts) ) {

			/** Self-referencing Alternate Hreflang Metatag */
			$global_locale = (get_global_custom_language_locale() !== false ? get_global_custom_language_locale() : get_default_wp_language_locale());
			$alternate_hreflang_self = get_permalink( get_the_ID() );
			$single_has_custom_locale = get_post_meta( get_the_ID(), 'language_locale_overwrite', true );
			$alternate_locale_self = ( !empty($single_has_custom_locale) ? $single_has_custom_locale : $global_locale ); ?>
			<link rel="alternate" hreflang="<?php echo esc_attr($alternate_locale_self); ?>" href="<?php echo esc_url($alternate_hreflang_self); ?>">
		<?php
			/** Alternate Hreflang Metatags to Posts/Pages in other Languages */
			foreach ( $alternate_lang_posts as $alternate_locale => $linked_postid  ) {
				$alternate_hreflang = get_permalink( $linked_postid ); ?>
				<link rel="alternate" hreflang="<?php echo esc_attr($alternate_locale); ?>" href="<?php echo esc_url($alternate_hreflang); ?>">
		<?php }
		}
	}

}

/** =========================================
 * COMPATIBILITY WITH OTHER WORDPRESS PLUGINS
 */

 /**
 * Plugin dependency: OG — Better Share on Social Media
 *
 * User custom locale (language code) properly as "og:local" tag.
 * @link https://wordpress.org/plugins/og/
 * @link https://github.com/gohugoio/hugo/issues/8296
 * @uses og_get_locale(), get_global_custom_language_locale()
 * @uses get_option('custom_global_language_og_territory')
 */
if ( ! function_exists( 'og_get_locale' ) ) {

	add_filter('og_get_locale', 'og_plugin_language_locale_overwrite');
	function og_plugin_language_locale_overwrite( $locale ) {

		global $post;
		$global_locale_changed = get_global_custom_language_locale();
		$page_locale_changed = get_post_meta( $post->ID, 'language_locale_overwrite', true );
		$custom_locale = ( !empty($page_locale_changed) ? $page_locale_changed : ( $global_locale_changed !== false ? $global_locale_changed : $locale ) );
		$custom_og_countrycode = get_option('custom_global_language_og_territory');

		/** Ensure og:locale format is "language_TERRITORY" */
		if (strpos($custom_locale, '-') !== false || strpos($custom_locale, '_') !== false) {
			$og_locale = str_replace('-', '_', $custom_locale);
		}
		/** Assumes anything else, that is not like "ll_CC", is only "ll" (and hence a custom override) */
		elseif (!empty($custom_og_countrycode)) {
			$og_locale = sprintf('%s_%s', strtolower($custom_locale), strtoupper($custom_og_countrycode));
		}
		/** Use default as fallback, if no custom og:locale can be mapped */
		else {
			$og_locale = $locale;
		}

		return $og_locale;

	}
}
