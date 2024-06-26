<?php
/**
 * Fonts manager
 *
 * @package Kenta Blocks
 */

namespace KentaBlocks;

class Fonts {

	/**
	 * Queued fonts
	 *
	 * @var array
	 */
	protected static $queue = [];

	/**
	 * @param $typography
	 */
	public static function enqueue( $typography ) {
		self::$queue[] = $typography;
	}

	/**
	 * Return system fonts array
	 *
	 * @return mixed|void
	 */
	public static function system() {
		$system_fonts = array(
			'inherit' => array( 'f' => 'Default', 's' => '' ),
			'sans'    => array(
				'f' => 'Sans',
				's' => 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"'
			),
			'serif'   => array( 'f' => 'Serif', 's' => 'ui-serif, Georgia, Cambria, "Times New Roman", Times, serif' ),
			'mono'    => array(
				'f' => 'Mono',
				's' => 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace'
			),
		);

		$fonts_json = kb_wp_filesystem()->get_contents( KENTA_BLOCKS_PLUGIN_PATH . 'assets/system-fonts.json' );

		// Change the object to a multidimensional array.
		$fonts_array = json_decode( $fonts_json, true );

		foreach ( $fonts_array['items'] as $key => $font ) {
			$system_fonts[ $font['id'] ] = array(
				'f' => $font['label'],
				's' => $font['stack']
			);
		}

		return apply_filters( 'kb/system_fonts', $system_fonts );
	}

	/**
	 * Enqueue typography scripts
	 *
	 * @param $id
	 * @param false $version
	 */
	public static function enqueue_scripts( $id, $version = false ) {
		$google_font_url = self::get_webfont_url( $id );

		if ( $google_font_url !== '' ) {
			wp_enqueue_style( $id, $google_font_url, array(), $version );
		}
	}

	/**
	 * Get web font url
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function get_webfont_url( $id ) {

		$queued = apply_filters( $id, self::$queue );

		$font_list = [];

		foreach ( $queued as $typography ) {
			$font_list = self::addTypography( $font_list, $typography );
		}

		$web_font_url = self::google_fonts_url( $font_list );
		if ( $web_font_url !== '' && kenta_blocks_setting()->value( 'kb_local_webfonts' ) === 'yes' ) {
			$web_font_url = wptt_get_webfont_url( $web_font_url );
		}

		return $web_font_url;
	}

	/**
	 * Add font to fonts stack from typography
	 *
	 * @param $fonts
	 * @param $typography
	 *
	 * @return array|mixed
	 */
	public static function addTypography( $fonts, $typography ) {
		$google = Fonts::google();

		$family  = $typography['family'] ?? 'inherit';
		$variant = $typography['variant'] ?? '400';

		if ( isset( $google[ $family ] ) ) {
			$variants = $google[ $family ]['v'] ?? [];
			$variant  = in_array( $variant, $variants ) ? $variant : ( $variants[0] ?? '400' );
		}

		return self::add( $fonts, $family, $variant );
	}

	/**
	 * Return google fonts array
	 *
	 * @return array
	 */
	public static function google() {
		$google_fonts = array();

		$fonts_json = kb_wp_filesystem()->get_contents( KENTA_BLOCKS_PLUGIN_PATH . 'assets/google-fonts.json' );

		// Change the object to a multidimensional array.
		$fonts_array = json_decode( $fonts_json, true );

		// Format the variants array for easier use.
		foreach ( $fonts_array['items'] as $key => $font ) {
			$font['v'] = array_values( $font['v'] );

			$fonts_array['items'][ $key ] = $font;
		}

		// Change the array key to the font's ID.
		foreach ( $fonts_array['items'] as $font ) {
			$id = trim( strtolower( str_replace( ' ', '-', $font['f'] ) ) );

			$google_fonts[ $id ] = $font;
		}

		return apply_filters( 'kb/google_fonts', $google_fonts );
	}

	/**
	 * Add font to stack
	 *
	 * @param $fonts
	 * @param $name
	 * @param array $variants
	 *
	 * @return array|mixed
	 */
	public static function add( $fonts, $name, $variants = array() ) {

		if ( 'inherit' == $name ) {
			return $fonts;
		}

		if ( ! is_array( $variants ) ) {
			// For multiple variant selected for fonts.
			$variants = explode( ',', str_replace( 'italic', 'i', $variants ) );
		}

		if ( is_array( $variants ) ) {
			$key = array_search( 'inherit', $variants );
			if ( false !== $key ) {

				unset( $variants[ $key ] );

				if ( ! in_array( 400, $variants ) ) {
					$variants[] = 400;
				}
			}
		} elseif ( 'inherit' == $variants ) {
			$variants = 400;
		}

		if ( isset( $fonts[ $name ] ) ) {
			foreach ( (array) $variants as $variant ) {
				if ( ! in_array( $variant, $fonts[ $name ]['variants'] ) ) {
					$fonts[ $name ]['variants'][] = $variant;
				}
			}
		} else {
			$fonts[ $name ] = array(
				'variants' => (array) $variants,
			);
		}

		return $fonts;
	}

	/**
	 * Get google fonts url, Combine multiple google font in one URL
	 *
	 * @param $font_list
	 * @param array $subsets
	 *
	 * @return string
	 */
	public static function google_fonts_url( $font_list, array $subsets = array() ) {

		$google_fonts = self::google();
		$fonts        = [];

		foreach ( $font_list as $name => $args ) {
			if ( ! empty( $name ) && isset( $google_fonts[ $name ] ) ) {
				$font = $google_fonts[ $name ];
				// Add font variants.
				$fonts[ $font['f'] ?? $name ] = $args['variants'] ?? [];
			}
		}

		/* URL */
		$base_url  = 'https://fonts.googleapis.com/css';
		$font_args = array();
		$family    = array();

		/* Format Each Font Family in Array */
		foreach ( $fonts as $font_name => $font_weight ) {
			$font_name = str_replace( ' ', '+', $font_name );
			if ( ! empty( $font_weight ) ) {
				if ( is_array( $font_weight ) ) {
					$font_weight = implode( ',', $font_weight );
				}
				$font_family = explode( ',', $font_name );
				$font_family = str_replace( "'", '', self::get_prop( $font_family, 0 ) );
				$family[]    = trim( $font_family . ':' . rawurlencode( trim( $font_weight ) ) );
			} else {
				$family[] = trim( $font_name );
			}
		}

		/* Only return URL if font family defined. */
		if ( ! empty( $family ) ) {
			/* Make Font Family a String */
			$family = implode( '|', $family );
			/* Add font family in args */
			$font_args['family'] = $family;
			/* Add font subsets in args */
			if ( ! empty( $subsets ) ) {
				/* format subsets to string */
				if ( is_array( $subsets ) ) {
					$subsets = implode( ',', $subsets );
				}
				$font_args['subset'] = rawurlencode( trim( $subsets ) );
			}
			$font_args['display'] = 'fallback';

			return add_query_arg( $font_args, $base_url );
		}

		return '';
	}

	/**
	 * @param $array
	 * @param $prop
	 * @param null $default
	 *
	 * @return mixed|string|null
	 */
	protected static function get_prop( $array, $prop, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof \ArrayAccess ) ) {
			return $default;
		}

		if ( isset( $array[ $prop ] ) ) {
			$value = $array[ $prop ];
		} else {
			$value = '';
		}

		return empty( $value ) && null !== $default ? $default : $value;
	}
}