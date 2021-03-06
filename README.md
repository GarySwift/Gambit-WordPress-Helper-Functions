# Gambit's WordPress Helper Functions
A collection of helper functions which we use in our various WordPress plugins and themes. Feel free to use them in your own.

# Usage
Require it in your WordPress project:

    require_once( 'gambit-helpers.php' );

Use it:

	add_filter( 'the_content', 'do_something_only_in_the_content_not_in_excerpts' );

	function do_something_only_in_the_content_not_in_excerpts( $content ) {
		if ( ! gambit_is_doing_excerpt() ) {
			$content .= 'I should only show up in post content and not excerpts.';
		}
		return $content;
	}

# Helper functions

| Helper Function | Returns | Description |
| --- | :---: | --- |
| `gambit_is_doing_excerpt()` | `boolean` | Returns `true` if currently creating an excerpt. Useful while inside `the_content` filters. |
| `gambit_get_all_post_types()` | `array` | Gets all the post types currently registered. Returns an associative array of all post type slugs and post type names. |
| `gambit_get_all_taxonomies()` | `array` | Gets all the taxonomies currently in the site. Returns an associative array of all taxonomy ids and post type names. |
| `gambit_get_all_terms_of_post_type($post_type)` | `array` | Gets all the taxonomies associated with a post type. Returns an array of all taxonomy ids. |
| `gambit_get_current_url()` | `string` | Gets the current URL, regardless whether we are in a singular page or not. |
| `gambit_abbreviate_number($value)` | `string` | Abbreviates a number with a unit. E.g. Converts 1100 to 1.1K |
| `gambit_hex_to_rgb($hex)` | `array` | Converts a hex color `#ffffff` or `#fff` to rgb `array(255,255,255)` |
| `gambit_hex_to_rgba($hex, $opacity=1.0)` | `string` | Converts a hex color `#ffffff` or `#fff` and an opacity value `1.0` to an rgba string `rgba(255,255,255,1.0)` |
| `gambit_get_ip()` | `string` | Gets the current IP address of the visitor |
| `gambit_is_spam($content)` | `boolean` | Checks whether a given content is spam using Akismet's system. Returns `true` if Akismet tagged the given content as spam, `false` otherwise. Requires Akismet. If it isn't installed, this always returns `false`. |
| `gambit_get_video_provider($video_string)` | `array` | Gets the Video ID & Provider (whether YouTube or Vimeo) from a video URL or ID. Supports only YouTube and Vimeo. E.g. Converts `https://www.youtube.com/watch?v=dQw4w9WgXcQ` to `array('type'=>'youtube', 'id'=>'dQw4w9WgXcQ')` |
