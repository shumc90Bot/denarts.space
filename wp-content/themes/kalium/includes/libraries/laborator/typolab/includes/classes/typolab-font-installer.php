<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Font installer class.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TypoLab_Font_Installer {

	/**
	 * Temporary file directories.
	 *
	 * @var array
	 */
	private static $tmp_directories = [];

	/**
	 * Download url and save it on TypoLab folder.
	 *
	 * @param string $url
	 * @param string $file_name
	 *
	 * @return string|WP_Error
	 */
	public static function download_url( $url, $file_name = 'file.tmp' ) {
		$tmp_dir   = trailingslashit( TypoLab::$fonts_dir ) . 'tmp' . mt_rand( 100000, 999999 ) . '/';
		$file_path = $tmp_dir . $file_name;

		// Create directory to store the downloaded file
		if ( wp_mkdir_p( $tmp_dir ) ) {
			$request = wp_remote_get( $url, [
				'stream'   => true,
				'filename' => $file_path,
				'timeout'  => 60,
			] );

			$response_code = wp_remote_retrieve_response_code( $request );

			// Not found
			if ( 200 !== $response_code ) {
				return new WP_Error( 'http_error_' . $response_code, wp_remote_retrieve_response_message( $request ) );
			}

			// On error
			if ( is_wp_error( $request ) ) {
				return $request;
			}

			// Add to tmp directories list (later to be deleted)
			self::$tmp_directories[] = $tmp_dir;

			return $file_path;
		}

		return new WP_Error( 'typolab_font_installer_could_not_create_dir', 'Directory could not be created. Download stopped!' );
	}

	/**
	 * Fetch font faces.
	 *
	 * @param string $stylesheet_url
	 * @param array  $args
	 *
	 * @return array|WP_Error
	 */
	public static function fetch_font_faces( $stylesheet_url ) {

		// Get stylesheet contents
		$request = wp_remote_get( $stylesheet_url, [
			'headers' => [
				'User-Agent' => TypoLab_Helper::USER_AGENT_CHROME,
			],
		] );

		// Stop on error
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		// Stylesheet contents
		$stylesheet = wp_remote_retrieve_body( $request );

		return self::parse_font_faces( $stylesheet );
	}

	/**
	 * Parse font faces.
	 *
	 * @return array
	 */
	public static function parse_font_faces( $stylesheet ) {
		$font_faces = [];

		/**
		 * Source mapper function.
		 *
		 * @param string $source
		 *
		 * @return array
		 */
		$src_mapper = function ( $css_source ) {
			$source = $format = '';

			if ( preg_match( '/url\((\'|\")?(?<source>[^\'\"\)]+)(\'|\")?\)\s+format\((\'|\")(?<format>[^\'|\"\)]+)(\'|\")\)/i', $css_source, $matches ) ) {
				$source = $matches['source'];
				$format = $matches['format'];
			}

			return [
				'source' => $source,
				'format' => $format,
			];
		};

		// Match @font-faces
		if ( preg_match_all( '/(\/\*\s*(?<subset>\[?[\w-]+\]?)\s*\*\/\s+)?@font-face.*?{(?<props>[^}]*)(?=})/', $stylesheet, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$subset    = kalium_get_array_key( $match, 'subset' );
				$css_props = explode( ';', $match['props'] );
				$font_face = [];
				$files     = [];

				foreach ( $css_props as $css_prop ) {
					if ( preg_match( '/(?<name>[^:]+):\s*(?<value>[^;]+)/i', $css_prop, $css_prop_match ) ) {
						$prop_name  = trim( $css_prop_match['name'] );
						$prop_value = trim( $css_prop_match['value'] );

						// Handel props
						switch ( $prop_name ) {

							// Font family
							case 'font-family':
								$prop_value = str_replace( [ '"', "'" ], '', $prop_value ); // Strip quotes or double quotes
								break;

							// Source
							case 'src':
								$files = array_map( $src_mapper, explode( ',', $prop_value ) );
								break;
						}

						// Source is added already
						if ( 'src' === $prop_name ) {
							continue;
						}

						// Add CSS prop
						$font_face[ $prop_name ] = $prop_value;
					}
				}

				$font_faces[] = [
					'subset' => $subset,
					'props'  => $font_face,
					'files'  => $files,
				];
			}
		}

		return $font_faces;
	}

	/**
	 * Get files from archive.
	 *
	 * @param string $archive_url
	 *
	 * @return []|WP_Error
	 */
	public static function get_files_from_archive( $archive_url ) {

		// Initialize filesystem
		kalium()->filesystem->initialize();

		// Download files
		$file = self::download_url( $archive_url, 'font.zip' );

		// Failed to download
		if ( is_wp_error( $file ) ) {
			return $file;
		}

		// Downloaded directory
		$file_dir = trailingslashit( dirname( $file ) );

		// Extract archive
		if ( kalium()->filesystem->unzip_file( $file, dirname( $file ) ) ) {

			// Delete downloaded file
			kalium()->filesystem->delete( $file );

			// Files
			$files_dirlist = kalium()->filesystem->instance()->dirlist( $file_dir, true, true );
			$files         = self::path_array( $files_dirlist );

			// Prepend files full path directory
			foreach ( $files as $i => $file_entry ) {
				$files[ $i ] = $file_dir . $file_entry;
			}
		} else {
			return new WP_Error( 'could_not_extract_archive', 'Could not extract downloaded archive!' );
		}

		return isset( $files ) ? $files : [];
	}

	/**
	 * Move font file to its respective directory.
	 *
	 * @param TypoLab_Font $font
	 * @param string       $file
	 * @param string       $new_file_name
	 *
	 * @return string|false
	 */
	public static function copy_file_to_font_directory( $font, $file, $new_file_name = null ) {
		if ( ! $font instanceof TypoLab_Font ) {
			return false;
		}

		// Initialize filesystem
		kalium()->filesystem->initialize();

		// File name
		$file_name = isset( $new_file_name ) ? $new_file_name : basename( $file );
		$file_name = strtolower( sanitize_file_name( $file_name ) );

		// Relative file path
		$relative_file_path = trailingslashit( self::get_relative_font_directory( $font ) ) . $file_name;

		// Full path to copy file to
		$full_file_path = trailingslashit( TypoLab::$fonts_dir ) . $relative_file_path;
		$copy_dir       = trailingslashit( dirname( $full_file_path ) );

		// Create directory for the new file
		if ( ! kalium()->filesystem->exists( $copy_dir ) ) {
			wp_mkdir_p( $copy_dir );
		}

		// Create index.html file if not exists
		if ( ! kalium()->filesystem->exists( trailingslashit( $copy_dir ) . 'index.html' ) ) {
			kalium()->filesystem->touch( trailingslashit( $copy_dir ) . 'index.html' );
		}

		// On success return relative file path
		if ( kalium()->filesystem->copy( $file, $full_file_path, true ) ) {
			return $relative_file_path;
		}

		return false;
	}

	/**
	 * Delete file inside typolab fonts directory.
	 *
	 * @param string $relative_path
	 *
	 * @return bool|WP_Error
	 */
	public static function delete_file( $relative_path, $function = 'delete' ) {

		// Initialize filesystem
		kalium()->filesystem->initialize();

		// On errors
		if ( kalium()->filesystem->has_errors() ) {
			return kalium()->filesystem->get_errors();
		}

		// File path
		$file_path = trailingslashit( TypoLab::$fonts_dir ) . $relative_path;

		return kalium()->filesystem->$function( $file_path, true );
	}

	/**
	 * Delete directory.
	 *
	 * @param string $relative_path
	 *
	 * @return bool|WP_Error
	 */
	public static function delete_directory( $relative_path ) {
		return self::delete_file( $relative_path, 'rmdir' );
	}

	/**
	 * Delete tmp files generated by self::get_files_from_archive().
	 */
	public static function delete_tmp_files() {

		// Initialize filesystem
		kalium()->filesystem->initialize();

		// Delete tmp directories
		foreach ( self::$tmp_directories as $path ) {
			kalium()->filesystem->rmdir( $path, true );
		}
	}

	/**
	 * Create CSS file.
	 *
	 * @param TypoLab_Font $font
	 * @param string       $css_style
	 *
	 * @return string|WP_Error
	 */
	public static function create_font_file( $font, $css_style ) {

		// Initialize filesystem
		kalium()->filesystem->initialize();

		// On errors
		if ( kalium()->filesystem->has_errors() ) {
			return kalium()->filesystem->get_errors();
		}

		// Unique ID
		$unique_id = substr( md5( time() + mt_rand( 0, 9999 ) ), 0, 3 );

		// File name
		$file_name = sprintf( '%s-%s.css', strtolower( sanitize_title( $font->get_family_name() ) ), $unique_id );

		// File path
		$file_path = trailingslashit( TypoLab::$fonts_dir ) . $file_name;

		// If file with the same name already exists, try another one
		if ( kalium()->filesystem->exists( $file_path ) ) {
			return self::create_font_file( $font, $css_style );
		}

		// Create file
		kalium()->filesystem->put_contents( $file_path, $css_style );

		return $file_name;
	}

	/**
	 * Get relative font directory.
	 *
	 * @param TypoLab_Font $font
	 *
	 * @return string|null
	 */
	public static function get_relative_font_directory( $font ) {
		if ( ! $font instanceof TypoLab_Font ) {
			return null;
		}

		// Font hash
		$font_hash = substr( md5( $font->get_id() ), 0, 5 );

		return implode( '/', [
			$font->get_source(),
			sanitize_title( $font->get_family_name() . '-' . $font_hash ),
		] );
	}

	/**
	 * Recursively process dirlist files array into list of file paths.
	 *
	 * @param array $files_arr
	 * @param array $path
	 *
	 * @return array
	 */
	public static function path_array( $files_arr, $path = [] ) {
		$files = [];

		foreach ( $files_arr as $name => $file_info ) {
			if ( 'f' === $file_info['type'] ) {
				$files[] = implode( '/', $path ) . ( count( $path ) ? '/' : '' ) . $name;
			} else if ( 'd' === $file_info['type'] ) {
				$files = array_merge( $files, self::path_array( $file_info['files'], array_merge( $path, [ $name ] ) ) );
			}
		}

		return $files;
	}
}
