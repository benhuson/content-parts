Content Parts
=============

Divide your post content into parts that you can show in different areas of your theme templates.

If you want to spice up your theme layouts this plugin will allow you to show different parts of your content in different area of your theme templates - break out of a single column of content.

More information can be found on the [Content Parts plugin page](http://www.benhuson.co.uk/wordpress-plugins/content-parts/).

Documentation
-------------

For full details how to implement this plugin visit the [Content Parts plugin page](https://github.com/benhuson/content-parts/wiki).

Installation
------------

1. Download the archive file and uncompress it (or install it via your WordPress admin).
1. Put the "content-parts" folder in the "wp-content/plugins" folder
1. Enable in WordPress by visiting the "Plugins" menu and activating it.

Upgrading
---------

If you are not performing an automatic upgrade, deactivate and reactivate the plugin to ensure any new features are correctly installed.

Upgrade Notice
--------------

### 1.8
Added wrapper divs around automatically output content parts.

### 1.7
Remove automatic output of `<div>` blocks - broke styling on some some sites. Instead provide settings page and `content_parts_auto_format_post_types` filter.

### 1.6
Automatically output `<div>` blocks around content parts in the main content on single posts and pages. Disable via the `content_parts_auto_content` filter.

### 1.5
Add post classes (`has-content-parts`, `content-parts-{n}`, `no-content-parts`).

### 1.4
Added `content_part_args` filter and `%%part%%` placeholder to before/after strings to replace with content part index.

### 1.3
All functions can now be passed an array of parameters. Deprecate `the_content_part()` multiple args - now expects an array.

### 1.2
Validate `start` and `limit` args are numeric.

### 1.1
Added `count_content_parts()` function. props Rory.

Changelog
---------

View a list of all plugin changes in [CHANGELOG.md](https://github.com/benhuson/content-parts/blob/master/CHANGELOG.md).
