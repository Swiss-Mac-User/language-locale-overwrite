# Language Locale Overwrite for WordPress

This lightweight SEO plugin for WordPress allows changing the default Language Locale and also on individual Posts and Pages.

The Plugin is mostly of use when your WordPress blog contains a _mix of languages with individual Posts/Pages_, and you do not want to install a huge translation plugin (because your posts are not actually translations, but just each individually published in a different content language). Accordingly you may also _not_ want to change any of the URLs of your existing articles.


## 💡 What does this do?

1. Allows changing the default Language Locale for your Blog,
2. and overwriting the Language Locale on individual Posts and Pages (in addition to 1.)

I developed this because my Blog is mostly written in English, but contains some articels written in German. And I wanted to improve their respective search engine optimization. In addition, with this plugin, I was able to change the default "English (United States)" (`en-US`) language code to just neutral "English" (`en`).


## 🎓 How to use

### ⚙️ Install and enable the Plugin
* Download the Plugin
* Place the unzipped `language-locale-overwrite`-folder **into your WordPress Plugin directory**:<br>`[wordpress]/wp-content/plugins/`
* Go to your WordPress Admin Dashboard » Plugins, and **activate «Language Locale Overwrite»**

### 📝
* Now open any Post or Page of which you want to modify its language locales for improved Search Engine content indexing
* There's a new Section «Language Locale» where a valid ISO-Language/-Country code can be specified

### 👨‍💻 Display overwritten Language Locale in your Theme

> I'm a bit too lazy right now, so this is filter is not part of the Plugin itself.
> Maybe this will change in the future... let's see. (A Pull Request would also be welcome 😉)

Add the following Code to your WordPress Child-Theme's `functions.php`-file, and **modify it** according to your needs:

```php
/**
 * Modify get_bloginfo( 'language' ) to use 2-char ISO-Code.
 * @link https://wordpress.stackexchange.com/a/210101/110615
 * @uses language_attributes()
 */
add_filter( 'language_attributes', 'set_custom_language_attributes' );
if ( ! function_exists( 'set_custom_language_attributes' ) ) {
	function set_custom_language_attributes( $wp_default_locale )
	{
        /** Set this, if needed. Example: 'en' */
        $use_different_global_locale = '';

        $default_locale = ( empty($use_different_global_locale) ? $wp_default_locale : $use_different_global_locale);
		if ( is_singular() && !empty( get_post_meta( get_the_ID(), 'language_locale_overwrite', true ) ) ) {
			$custom_locale = esc_attr( get_post_meta( get_the_ID(), 'language_locale_overwrite', true ) );
		}
		$use_locale = ( isset($custom_locale) && !empty($custom_locale) ? $custom_locale : $default_locale );
		$set_custom_language_attribute = 'lang="'.$use_locale.'"';

		/** Return the modified `lang="..."` HTML attribute */
		return $set_custom_language_attribute;
	}
}
```


## ℹ️ List of valid language locales

Valid language locales either consist of 2-characters ISO-code for languages, or a combination thereof with a 2-characters ISO country code. Here are some examples:

* `de` → German
* `de-DE` → Germany (German)
* `de-CH` → Switzerland (German)
* `en` → English
* `en-GB` → United Kingdom (English)
* `fr` → French
* `zh-CN` → Chinese (Simplified)
* …and so on
