# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Changed
- Use PHP5 constructors.

## [1.5] - 0000-00-00

### Added
- Add post classes (`has-content-parts`, `content-parts-{n}`, `no-content-parts`).

### Changed
- Updated Tiny MCE button image.
- Tested up to WordPress 4.0

### Fixed
- Don't load editor functionality if `DOING_AJAX`.

## [1.4] - 0000-00-00

### Added
- Automatically make content parts work when 'in the loop'.
- Added `%%part%%` placeholder to before/after strings to replace with content part index.
- Add `content_part_args` filter.

## [1.3] - 0000-00-00

### Changed
- Moved code to a class structure.
- All functions can now be passed an array of parameters.
- Deprecate `the_content_part()` multiple args - now expects an array.

## [1.2] - 0000-00-00

### Changed
- Validate 'start' and 'limit' args are numeric.
- If $post not set, ignore.
- Checked WordPress 3.3 compatibility.

## [1.1] - 0000-00-00

### Added
- Added `count_content_parts()` function. props Rory.

## [1.0] - 0000-00-00

### Added
- First release.

[Unreleased]: https://github.com/benhuson/content-parts/compare/1.5...HEAD
[1.5]: https://github.com/benhuson/content-parts/compare/1.4...1.5
[1.4]: https://github.com/benhuson/content-parts/compare/1.3...1.4
[1.3]: https://github.com/benhuson/content-parts/compare/1.2...1.3
[1.2]: https://github.com/benhuson/content-parts/compare/1.1...1.2
[1.1]: https://github.com/benhuson/content-parts/compare/1.0...1.1
[1.0]: https://github.com/benhuson/content-parts/tree/1.0