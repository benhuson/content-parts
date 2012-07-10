=== Content Parts  ===
Contributors: husobj
Donate link: http://www.benhuson.co.uk/wordpress-plugins/content-parts/
Tags: content, layout, the_content, templates, theme, editor
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.4

Divide your post content into parts that you can show in different areas of your theme templates.

== Description ==
If you want to spice up your theme layouts this plugin will allow you to show different parts of your content in different area of your theme templates - break out of a single column of content.

More information can be found on the [Content Parts plugin page](http://www.benhuson.co.uk/wordpress-plugins/content-parts/).

== Installation ==
1. Download the archive file and uncompress it (or install it via your WordPress admin).
2. Put the "content-parts" folder in the "wp-content/plugins" folder
3. Enable in WordPress by visiting the "Plugins" menu and activating it.

= Upgrading =

If you are not performing an automatic upgrade, deactivate and reactivate the plugin to ensure any new features are correctly installed.

= Documentation =

For full details how to implement this plugin visit the [Content Parts plugin page](http://www.benhuson.co.uk/wordpress-plugins/content-parts/).

== Changelog ==

= Content Parts 1.4 =

* Automatically make content parts work when 'in the loop'.
* Added %%part%% placeholder to before/after strings to replace with content part index.
* Add content_part_args filter.

= Content Parts 1.3 =

* All functions can now be passed an array of parameters.
* Deprecate the_content_part() multiple args - now expects an array.
* Moved code to a class structure.

= Content Parts 1.2 =

* Validate 'start' and 'limit' args are numeric.
* If $post not set, ignore.
* Checked WordPress 3.3 compatibility.

= Content Parts 1.1 =

* Added count_content_parts() function. props Rory.

= Content Parts 1.0 =

* First release.
