=== TheatreBase Production Subsites ===
Contributors: carstenbach
Donate link: https://figuren/theater/
Tags: Theatre, Productions, Subsites, Child-Parent-Cross-PT-Relationship, Endpoints
Requires at least: 4.5
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enables the 'production-subsite' PT, which can be added to parent 'production' PT posts.

== Description ==

Sacffolded using:

```
wp --path=wp scaffold plugin theatrebase-production-subsites --plugin_name="TheatreBase Production Subsites" --plugin_description="Enables the 'production-subsite' PT, which can be added to parent 'production' PT posts." --plugin_author="Carsten Bach" --plugin_author_uri="https://carsten-bach.de" --plugin_uri="https://figuren.theater/"
```

and

```
wp --path=wp scaffold post-type tb_prod_subsite --label=Subsite --textdomain=theatrebase-production-subsites --dashicon=dashicons-book-alt --plugin=theatrebase-production-subsites
```

following the [**How to** create a custom plugin](https://make.wordpress.org/cli/handbook/how-to-create-custom-plugins/) on  WP-CLI – WordPress.org




== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](https://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: https://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
