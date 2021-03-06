<?php
/**
 * A list of various helper functions for WordPress.
 */

if ( ! function_exists( 'gambit_is_doing_excerpt' ) ) {

	/**
	 * Use this to check whether we are currently generating an excerpt
	 * in the WP lifecycle.
	 *
	 * @return bool True if we are currently generating an excerpt, false otherwise.
	 */
	function gambit_is_doing_excerpt() {
		return isset( $GLOBALS['_gambit_is_doing_excerpt'] );
	}
}

if ( ! function_exists( 'gambit_set_is_excerpt' ) ) {
	add_filter( 'get_the_excerpt', 'gambit_set_is_excerpt', 0 );
	add_filter( 'the_excerpt', 'gambit_set_is_excerpt', 0 );

	/**
	 * Does nothing but set a global variable to true, meaning we are
	 * currently generating an excerpt.
	 *
	 * @param string $text The excerpt.
	 *
	 * @return string The excerpt
	 */
	function gambit_set_is_excerpt( $text ) {
		$GLOBALS['_gambit_is_doing_excerpt'] = true;
		return $text;
	}
}

if ( ! function_exists( 'gambit_unset_is_excerpt' ) ) {
	add_filter( 'get_the_excerpt', 'gambit_unset_is_excerpt', 99999 );
	add_filter( 'the_excerpt', 'gambit_unset_is_excerpt', 99999 );

	/**
	 * Does nothing but unset a global variable, meaning we are
	 * gone generating an excerpt.
	 *
	 * @param string $text The excerpt.
	 *
	 * @return string The excerpt
	 */
	function gambit_unset_is_excerpt( $text ) {
		unset( $GLOBALS['_gambit_is_doing_excerpt'] );
		return $text;
	}
}


if ( ! function_exists( 'gambit_get_all_post_types' ) ) {
	/**
	 * Gets all post type slugs and their display names.
	 *
	 * @return array An associative array of all post type slugs and post type names.
	 */
	function gambit_get_all_post_types() {
		$args = array(
		   'public' => true,
		   '_builtin' => true,
		);
		$post_types = get_post_types( $args, 'objects' );

		$args = array(
		   'public' => true,
		   '_builtin' => false,
		);
		$post_types2 = get_post_types( $args, 'objects' );

		$post_types = array_merge( $post_types, $post_types2 );

		$ret = array();
		foreach ( $post_types as $post_type ) {

			$slugname = $post_type->name;

			$name = $post_type->name;
			if ( ! empty( $post_type->labels->singular_name ) ) {
				$name = $post_type->labels->singular_name . ' (' . $slugname . ')';
			}

			$ret[ $slugname ] = $name;
		}

		return $ret;
	}
}


if ( ! function_exists( 'gambit_get_current_url' ) ) {

	/**
	 * Gets the current URL.
	 *
	 * @return string The current URL.
	 */
	function gambit_get_current_url() {
		if ( ! is_main_query() && ! is_singular() ) {
			return trailingslashit( home_url( add_query_arg( null, null ) ) );
		}
		return trailingslashit( get_permalink( get_the_ID() ) );
	}
}


if ( ! function_exists( 'gambit_abbreviate_number' ) ) {

	/**
	 * Abbreviates a number with a unit. E.g. Converts 1100 to 1.1K
	 *
	 * @see http://stackoverflow.com/questions/13049851/php-number-abbreviator
	 *
	 * @param int $value The number to abbreviate.
	 *
	 * @return string The abbreviated number.
	 */
	function gambit_abbreviate_number( $value ) {

		$abbreviations = array(
			12 => 'T',
			9 => 'B',
			6 => 'M',
			3 => 'K',
			0 => '',
		);

		foreach ( $abbreviations as $exponent => $abbreviation ) {
			if ( $value >= pow( 10, $exponent ) ) {
				return round( floatval( $value / pow( 10, $exponent ) ), 1 ).$abbreviation;
			}
		}

		return $value;
	}
}


if ( ! function_exists( 'gambit_hex_to_rgb' ) ) {

	/**
	 * Converts a hex color to rgb values.
	 *
	 * @param string $hex The hexadecimal color value.
	 *
	 * @return array The individual r, g, b colors
	 */
	function gambit_hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( 3 !== strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}

		return array( $r, $g, $b );
	}
}


if ( ! function_exists( 'gambit_hex_to_rgba' ) ) {

	/**
	 * Converts a hex color and opacity float into an rgba string, usable in CSS.
	 *
	 * @param string $hex The hexadecimal color value.
	 * @param float  $opacity The opacity value.
	 *
	 * @return string The rgba color string
	 */
	function gambit_hex_to_rgba( $hex, $opacity = 1.0 ) {
		$rgb = gambit_hex_to_rgb( $hex );
		return 'rgba(' . $hex[0] . ',' . $hex[1] . ',' . $hex[2] . ',' . $opacity . ')';
	}
}


if ( ! function_exists( 'gambit_get_ip' ) ) {

	/**
	 * Gets the IP & if possible the forwarded proxy name. To be used for identifying non-logged in ratings and to secure against duplicate ratings.
	 *
	 * @return string The IP of the visitor.
	 */
	function gambit_get_ip() {
		// @codingStandardsIgnoreLine
		$unique_ip = empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? '' : $_SERVER['HTTP_X_FORWARDED_FOR'];
		$unique_ip .= empty( $unique_ip ) ? '' : '-';
		// @codingStandardsIgnoreLine
		$unique_ip .= empty( $_SERVER['REMOTE_ADDR'] ) ? '' : $_SERVER['REMOTE_ADDR'];
		return $unique_ip;
	}
}


if ( ! function_exists( 'gambit_get_all_taxonomies' ) ) {

	/**
	 * Pulls a list of taxonomies.
	 *
	 * @return array An array of post types and slugs usable in Titan Framework options.
	 */
	function gambit_get_all_taxonomies() {
		$post_types = gambit_get_all_post_types( false );

		$ret = array();
		foreach ( $post_types as $post_type => $post_type_name ) {
			$taxonomies = get_object_taxonomies( $post_type );

			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy_object = get_taxonomy( $taxonomy );
				$taxonomy_name = $taxonomy_object->label;
				if ( ! empty( $taxonomy_object->labels->singular_name ) ) {
					$taxonomy_name = $taxonomy_object->labels->singular_name;
				}

				$terms = get_terms( $taxonomy, array(
					'parent' => 0,
					'hide_empty' => false,
				) );

				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					continue;
				}

				foreach ( $terms as $term ) {
					$ret[ $term->term_id ] = $term->name . ' (' . $post_type_name . ' &middot; ' . $taxonomy_name . ')';
				}

				// Child terms aren't outputted by get_terms, we'll need to get the child terms individually.
				foreach ( $terms as $term ) {
					$term_children = get_term_children( $term->term_id, $taxonomy );

					if ( is_wp_error( $term_children ) || empty( $term_children ) ) {
						continue;
					}

					foreach ( $term_children as $term_child_id ) {

						// @codingStandardsIgnoreLine
						$term_child = get_term_by( 'id', $term_child_id, $taxonomy );

						$ret[ $term_child_id ] = $term->name . ' &rarr; ' . $term_child->name . ' (' . $post_type_name . ' &middot; ' . $taxonomy_name . ')';
					}
				}
			}
		}
		return $ret;
	}
}


if ( ! function_exists( 'gambit_get_all_terms_of_post_type' ) ) {

	/**
	 * Get all term IDs of a post type.
	 *
	 * @param string $post_type The post type to get the terms of.
	 *
	 * @return array An array of term IDs.
	 */
	function gambit_get_all_terms_of_post_type( $post_type ) {
		$ret = array();

		$taxonomies = get_object_taxonomies( $post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$taxonomy_name = $taxonomy_object->label;
			if ( ! empty( $taxonomy_object->labels->singular_name ) ) {
				$taxonomy_name = $taxonomy_object->labels->singular_name;
			}

			$terms = get_terms( $taxonomy, array(
				'parent' => 0,
				'hide_empty' => false,
			) );

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$ret[] = $term->term_id;
			}

			// Child terms aren't outputted by get_terms, we'll need to get the child terms individually.
			foreach ( $terms as $term ) {
				$term_children = get_term_children( $term->term_id, $taxonomy );

				if ( is_wp_error( $term_children ) || empty( $term_children ) ) {
					continue;
				}

				foreach ( $term_children as $term_child_id ) {
					$ret[] = $term_child_id;
				}
			}
		}

		return $ret;
	}
}


if ( ! function_exists( 'gambit_is_spam' ) ) {
	/**
	 * Checks whether a given content is spam using Akismet's system.
	 *
	 * @param array $content Contains form input to be checked for spam.
	 *
	 * @return boolean True if Akismet tagged the given content as spam, false otherwise.
	 */
	function gambit_is_spam( $content ) {

		// Innocent until proven guilty.
		$is_spam = false;

		// Contents are always an array. Make sure of that.
		$content = (array) $content;

		// Make sure Akismet is active before proceeding.
		if ( function_exists( 'akismet_init' ) ) {

			// Make sure we have the API key before proceeding.
			$wpcom_api_key = get_option( 'wordpress_api_key' );
			if ( ! empty( $wpcom_api_key ) ) {

				global $akismet_api_host, $akismet_api_port;

				// Set remaining required values for akismet api.
				// @codingStandardsIgnoreLine
				$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
				// @codingStandardsIgnoreLine
				$content['referrer'] = $_SERVER['HTTP_REFERER'];
				$content['blog'] = get_option( 'home' );

				if ( empty( $content['referrer'] ) ) {
					$content['referrer'] = get_permalink();
				}

				$query_string = '';

				foreach ( $content as $key => $data ) {
					if ( ! empty( $data ) ) {
						$query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
					}
				}

				// Send it for analysis.
				$response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );

				// If we have a hit, update statistics and send the bad news. Do nothing if it's clean.
				if ( 'true' === $response[1] ) {
					update_option( 'akismet_spam_count', get_option( 'akismet_spam_count' ) + 1 );
					$is_spam = true;
				}
			}
		}

		return $is_spam;

	}
}


if ( ! function_exists( 'gambit_get_video_provider' ) ) {

	/**
	 * Gets the Video ID & Provider from a video URL or ID. Supports only YouTube and Vimeo.
	 *
	 * @param string $video_string The URL or ID of a video.
	 *
	 * @return array An array whether the video is a YouTube or Vimeo video along with the video ID.
	 */
	function gambit_get_video_provider( $video_string ) {

		$video_string = trim( $video_string );

		/*
		 * Check for YouTube.
		 */
		$video_id = false;
		if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_string, $id ) ) {
			if ( count( $id > 1 ) ) {
				$video_id = $id[1];
			}
		} else if ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $video_string, $id ) ) {
			if ( count( $id > 1 ) ) {
				$video_id = $id[1];
			}
		} else if ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $video_string, $id ) ) {
			if ( count( $id > 1 ) ) {
				$video_id = $id[1];
			}
		} else if ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $video_string, $id ) ) {
			if ( count( $id > 1 ) ) {
				$video_id = $id[1];
			}
		}

		if ( ! empty( $video_id ) ) {
			return array(
				'type' => 'youtube',
				'id' => $video_id,
			);
		}

		/*
		 * Check for Vimeo.
		 */
		if ( preg_match( '/vimeo\.com\/(\w*\/)*(\d+)/', $video_string, $id ) ) {
			if ( count( $id > 1 ) ) {
				$video_id = $id[ count( $id ) - 1 ];
			}
		}

		if ( ! empty( $video_id ) ) {
			return array(
				'type' => 'vimeo',
				'id' => $video_id,
			);
		}

		/*
		 * Non-URL form.
		 */
		if ( preg_match( '/^\d+$/', $video_string ) ) {
			return array(
				'type' => 'vimeo',
				'id' => $video_string,
			);
		}

		return array(
			'type' => 'youtube',
			'id' => $video_string,
		);
	}
}
