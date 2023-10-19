# Language Locale Overwrite for WordPress

This lightweight SEO plugin for WordPress allows changing the default HTML lang attribute globally, and on individual Posts and Pages. Along with cross-referencing similar Posts/Pages in different Languages using Alternate Hreflang tags.

The Plugin is mostly of use when your WordPress blog contains a _mix of languages with individual Posts/Pages_, and you do not want to install a huge translation plugin (because your posts are not actually translations, but just each individually published in a different content language). Accordingly you may also _not_ want to change any of the URLs of your existing articles.


## 💡 What does this do?

1. Allows changing the default **HTML lang attribute** for your Blog,
2. overwriting the HTML lang attribute **on individual Posts and Pages** (in addition to 1.),
3. and cross-referencing Posts/Pages in different languages using **Alternate Hreflang tags**.

I developed this because my Blog is mostly written in English, but contains some articels written in German - of which some have gotten a content recycle across the two languages. And I wanted to improve their respective search engine optimization. In addition, with this plugin, I was able to change the default "English (United States)" (`en-US`) language code to just neutral "English" (`en`).


## 🎓 How to use

### ⚙️ Install and enable the Plugin
* Download the Plugin
* Place the unzipped `language-locale-overwrite`-folder **into your WordPress Plugin directory**:<br>`[wordpress]/wp-content/plugins/`
* Go to your WordPress Admin Dashboard » Plugins, and **activate «Language Locale Overwrite»**

### 📝 Settings
#### Global change of `<html lang="…">`
1. Go to your WordPress "Settings » General" at /wp-admin/options-general.php#llo_global
2. Set your desired ISO-Language/-Country locale using the setting «Change HTML lang attribute»
3. (Optional) For OpenGraph `og:locale` you can set a preferred 2-char country code using «Custom Country code»

#### Individual HTML lang change for Posts/Pages
1. Open any Post or Page, for which you want to modify its language locale
2. There's a new section «Language Locale» where a valid ISO-Language/-Country code can be specified

#### Link Posts/Pages with same content in different languages
Pre-requisite: you must have Post/Pages already tagged with a "non-default" language locale!

1. Edit the Post or Page, for which you want to reference a translated Post/Page
2. In the «Language Locale» section, use the «Link alternate lang…»-dropdowns to make cross-links


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

---

<p align="center"><a href="https://bmc.link/swissmacuser/">
    <img src="https://cdn.buymeacoffee.com/buttons/default-yellow.png" alt="Support this project with a Coffee." height="40" width="172">
</a></p>
