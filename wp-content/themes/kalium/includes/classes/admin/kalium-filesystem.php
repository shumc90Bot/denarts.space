<?php
/**
 * Kalium WordPress Theme
 *
 * File system class of Kalium
 *
 * @link https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Magic methods inherited from WP_Filesystem_Base.
 *
 * @method string abspath()
 * @method string wp_content_dir()
 */
class Kalium_Filesystem {

	/**
	 * Status of filesystem.
	 *
	 * @var bool
	 */
	private $ok = false;

	/**
	 * Credentials value.
	 *
	 * @var array|bool
	 */
	private $credentials = false;

	/**
	 * Credentials form.
	 *
	 * @var string
	 */
	private $credentials_form = '';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Initialize filesystem.
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	public function initialize( $url = '' ) {

		// Initialed status
		static $initialized = false;

		// Initialize only once
		if ( false === $initialized ) {

			// Load filesystem functions
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			// Credentials
			$this->credentials = $this->request_credentials( $url, true );

			// Setup system global
			$this->ok = WP_Filesystem( $this->credentials );

			// Mark as initialized
			$initialized = true;
		}

		return $this->ok;
	}

	/**
	 * Reference to WP_Filesystem instance.
	 *
	 * @return WP_Filesystem_Base|null
	 */
	public function instance() {
		return $GLOBALS['wp_filesystem'];
	}

	/**
	 * Reference to WP_Filesystem_Direct instance.
	 *
	 * @return WP_Filesystem_Direct|null
	 */
	public function instance_direct() {
		static $instance_direct;

		// Initialize direct fs instance
		if ( is_null( $instance_direct ) ) {
			$fs_direct_class = 'WP_Filesystem_Direct';

			// Load class if not exists
			if ( ! class_exists( $fs_direct_class ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			}

			$instance_direct = new $fs_direct_class( null );
		}

		return $instance_direct;
	}

	/**
	 * Get errors from WP Filesystem.
	 *
	 * @return WP_Error|null
	 */
	public function get_errors() {
		return $this->instance()->errors;
	}

	/**
	 * Check if filesystem reported errors.
	 *
	 * @return bool
	 */
	public function has_errors() {
		return $this->get_errors() && $this->get_errors()->has_errors();
	}

	/**
	 * Get error message from WP Filesystem.
	 *
	 * @return string
	 */
	public function get_error_message() {
		$error_messages = [];

		if ( $this->has_errors() ) {
			$error_messages = $this->get_errors()->get_error_messages();
		}

		return implode( PHP_EOL, $error_messages );
	}

	/**
	 * Get filesystem credentials.
	 *
	 * @return array|bool
	 */
	public function get_credentials() {
		return $this->credentials;
	}

	/**
	 * Get credentials form.
	 *
	 * @return string
	 */
	public function get_credentials_form() {
		return $this->credentials_form;
	}

	/**
	 * Mirror of Filesystem Base -> abspath method.
	 *
	 * @return string
	 */
	public function abspath() {
		return $this->instance()->abspath();
	}

	/**
	 * Mirror of Filesystem Base -> wp_content_dir method.
	 *
	 * @return string
	 */
	public function wp_content_dir() {
		return $this->instance()->wp_content_dir();
	}

	/**
	 * Replace abs path with remote file abs path.
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function real_abspath( $file ) {
		return str_replace( ABSPATH, $this->abspath(), $file );
	}

	/**
	 * Reads entire file into a string.
	 *
	 * @param string $file
	 *
	 * @return string|false
	 */
	public function get_contents( $file ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->get_contents( $file );
		}

		return $this->instance()->get_contents( $file_real );
	}

	/**
	 * Writes a string to a file.
	 *
	 * @param string    $file
	 * @param string    $contents
	 * @param int|false $mode
	 *
	 * @return bool
	 */
	public function put_contents( $file, $contents, $mode = false ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->put_contents( $file, $contents, $mode );
		}

		return $this->instance()->put_contents( $file_real, $contents, $mode );
	}

	/**
	 * Copies a file.
	 *
	 *
	 * @param string    $source
	 * @param string    $destination
	 * @param bool      $overwrite
	 *                               Default false.
	 * @param int|false $mode
	 *
	 * @return bool
	 */
	public function copy( $source, $destination, $overwrite = false, $mode = false ) {
		$source_real      = $this->real_abspath( $source );
		$destination_real = $this->real_abspath( $destination );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->copy( $source_real, $destination_real, $overwrite, $mode );
		}

		return $this->instance()->copy( $source_real, $destination_real, $overwrite, $mode );
	}

	/**
	 * Moves file.
	 *
	 * @param string $source
	 * @param string $destination
	 * @param bool   $overwrite
	 *
	 * @return bool
	 */
	public function move( $source, $destination, $overwrite = false ) {
		$source_real      = $this->real_abspath( $source );
		$destination_real = $this->real_abspath( $destination );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->move( $source, $destination, $overwrite );
		}

		return $this->instance()->move( $source_real, $destination_real, $overwrite );
	}

	/**
	 * Deletes a file or directory.
	 *
	 * @param string       $file
	 * @param bool         $recursive
	 * @param string|false $type
	 *
	 * @return bool
	 */
	public function delete( $file, $recursive = 0, $type = 0 ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->delete( $file, $recursive, $type );
		}

		return $this->instance()->delete( $file_real, $recursive, $type );
	}

	/**
	 * Check if file exists using Filesystem method.
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	public function exists( $file ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->exists( $file );
		}

		return $this->instance()->exists( $file_real );
	}

	/**
	 * Gets the file size (in bytes).
	 *
	 * @param string $file
	 *
	 * @return int|false
	 */
	public function size( $file ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->size( $file );
		}

		return $this->instance()->size( $file_real );
	}

	/**
	 * Sets the access and modification times of a file.
	 *
	 * @param string $file
	 * @param int    $time
	 * @param int    $atime
	 *
	 * @return bool
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		$file_real = $this->real_abspath( $file );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->touch( $file, $time, $atime );
		}

		return $this->instance()->touch( $file_real, $time, $atime );
	}

	/**
	 * Request filesystem credentials.
	 *
	 * @param string $url
	 * @param bool   $silent
	 *
	 * @return array|bool
	 */
	public function request_credentials( $url = '', $silent = false ) {

		// Direct method, no work required
		if ( 'direct' === get_filesystem_method() ) {
			$creds = request_filesystem_credentials( esc_url_raw( $url ) );
		} // FTP/FTPS or SSH
		else {

			ob_start();

			// Request credentials
			$creds = request_filesystem_credentials( esc_url_raw( $url ) );

			// Save FTP form output
			$ftp_form = ob_get_clean();

			// Do not show FTP form
			if ( $silent ) {
				$this->credentials_form = $ftp_form;
			} // Show FTP form
			elseif ( ! $creds ) {
				echo $ftp_form;
			}
		}

		return $creds;
	}

	/**
	 * Unzips a specified ZIP file to a location on the filesystem.
	 *
	 * @param string $file
	 * @param string $to
	 *
	 * @return bool|true|WP_Error
	 */
	public function unzip_file( $file, $to ) {

		// Error when filesystem global is not set up
		if ( $this->has_errors() ) {
			return $this->get_errors();
		}

		return unzip_file( $file, $this->real_abspath( $to ) );
	}

	/**
	 * Compress a file or directory with WordPress PclZip library.
	 *
	 * @param string $source
	 * @param string $destination
	 *
	 * @return true|WP_Error
	 */
	public function zip_file( $source, $destination = '' ) {

		// Set the mbstring internal encoding to a binary safe encoding
		mbstring_binary_safe_encoding();

		// Optional destination path name generate
		if ( ! $destination ) {
			$destination = trailingslashit( dirname( $source ) ) . basename( $source ) . '.zip';
		} elseif ( '.' === dirname( $destination ) ) {
			$destination = trailingslashit( dirname( $source ) ) . $destination;
		}

		// Add zip extension if not present
		if ( ! preg_match( '/\.zip$/i', $destination ) ) {
			$destination .= '.zip';
		}

		// Absolute destination path
		$destination = $this->real_abspath( $destination );

		// Load class file if it's not loaded yet
		if ( ! class_exists( 'PclZip' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
		}

		// Creative archive
		$archive = new PclZip( wp_normalize_path( $destination ) );
		$result  = $archive->add( wp_normalize_path( $source ), PCLZIP_OPT_REMOVE_PATH, dirname( $source ) );

		// Reset the mbstring internal encoding
		reset_mbstring_encoding();

		// Creating archive failed
		if ( 0 === $result ) {
			return new WP_Error( 'kalium_fs_zip_failed', $archive->error_string );
		}

		return true;
	}

	/**
	 * Copies a directory from one location to another via the WordPress Filesystem Abstraction.
	 *
	 * @param string   $from
	 * @param string   $to
	 * @param string[] $skip_list
	 *
	 * @return true|WP_Error
	 */
	public function copy_dir( $from, $to, $skip_list = [] ) {
		return copy_dir( $this->real_abspath( $from ), $this->real_abspath( $to ), $skip_list );
	}

	/**
	 * Deletes a directory.
	 *
	 * @param string $path
	 * @param bool   $recursive
	 *
	 * @return bool
	 */
	public function rmdir( $path, $recursive = false ) {
		$path = $this->real_abspath( $path );

		// Use direct filesystem method
		if ( $this->is_direct_method_required() ) {
			return $this->instance_direct()->rmdir( $path, $recursive );
		}

		return $this->instance()->delete( $path, $recursive );
	}

	/**
	 * Checks if direct filesystem is required to avoid incorrect results with FTPext transport method.
	 *
	 * @return bool
	 */
	private function is_direct_method_required() {
		return in_array( get_filesystem_method(), [ 'ftpext' ] );
	}
}
