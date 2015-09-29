<?php


/**
 * Centralizes control over resource fetching.
 * 
 * @since 4.7
 */
class WPRSS_Feed_Access {
	
	protected static $_instance;
	
	protected $_certificate_file_path;
	
	const SETTING_KEY_CERTIFICATE_PATH = 'certificate-path';
	
	/**
	 * @since 4.7
	 * @return WPRSS_Feed_Access The singleton instance of this class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			$class_name = __CLASS__;
			self::$_instance = new $class_name;
		}
		
		return self::$_instance;
	}
	
	
	public function __construct() {
		$this->_construct();
	}
	
	
	/**
	 * The parameter-less constructor.
	 * 
	 * @since 4.7
	 */
	protected function _construct() {
		add_action( 'wprss_fetch_feed_before', array( $this, 'set_feed_options' ), 10 );
		add_action( 'wprss_settings_array', array( $this, 'add_settings' ) );
		add_action( 'wprss_default_settings_general', array( $this, 'add_default_settings' ) );
	}
	
	
	/**
	 * Sets the path to the certificate, which will be used by WPRSS to fetch remote content.
	 * 
	 * @since 4.7
	 * @param string $path Absolute path to the certificate file.
	 * @return \WPRSS_Feed_Access This instance.
	 */
	public function set_certificate_file_path( $path ) {
		$this->_certificate_file_path = $path;
		return $this;
	}
	
	
	/**
	 * Gets the path to the certificate, which will be used by WPRSS to fetch remote content.
	 * 
	 * @since 4.7
	 * @see get_certificate_path_setting()
	 * @return string Absolute path to the certificate file. By default will use the option.
	 */
	public function get_certificate_file_path() {
		if ( empty( $this->_certificate_file_path ) )
			$this->_certificate_file_path = $this->get_certificate_path_setting();

		return $this->_certificate_file_path;
	}
	
	
	/**
	 * Gets the value of the option that stores the path to the certificate file.
	 * Relative paths will be converted to absolute, as if relative to WP root.
	 * 
	 * @since 4.7
	 * @return string Absolute path to the certificate file.
	 */
	public function get_certificate_path_setting() {
		$path = wprss_get_general_setting( self::SETTING_KEY_CERTIFICATE_PATH );
		
		if ( empty( $path ) )
			return $path;
		
		if ( !path_is_absolute( $path ) )
			$path = ABSPATH . $path;
		
		return $path;
	}
	
	
	/**
	 * This happens immediately before feed initialization.
	 * Handles the `wprss_fetch_feed_before` action.
	 * 
	 * @since 4.7
	 * @param SimplePie $feed The instance of the object that represents the feed to be fetched.
	 * @param string $url The URL, from which the feed is going to be fetched.
	 */
	public function set_feed_options( $feed ) {
		$feed->set_file_class( 'WPRSS_SimplePie_File' );
		WPRSS_SimplePie_File::set_default_certificate_file_path( $this->get_certificate_file_path() );
	}
	
	
	/**
	 * Implements a `wprss_settings_array` filter.
	 * 
	 * @since 4.7
	 * @param array $settings The current settings array, where 1st dimension is secion code, 2nd is setting code, 3rd is setting option(s).
	 * @return array The new settings array.
	 */
	public function add_settings( $settings ) {
		$settings['general'][ self::SETTING_KEY_CERTIFICATE_PATH ] = array(
			'label'			=> __( 'Certificate Path', WPRSS_TEXT_DOMAIN ),
			'callback'		=> array( $this, 'render_certificate_path_setting' )
		);
		
		return $settings;
	}
	
	
	/**
	 * @since 4.7
	 * @param array $settings The array of settings, where key is
	 * @return array The new array of default settings
	 */
	public function add_default_settings( $settings ) {
		$settings[ self::SETTING_KEY_CERTIFICATE_PATH ] = implode( '/', array( WPINC, 'certificates', 'ca-bundle.crt' ) );

		return $settings;
	}
	
	
	/**
	 * Renders the setting field for the certificate path.
	 * 
	 * @since 4.7
	 * @see wprss_admin_init
	 * @param array $field Data of this field.
	 */
	public function render_certificate_path_setting( $field ) {
        $feed_limit = wprss_get_general_setting( $field['field_id'] );
        ?>
		<input id="<?php echo $field['field_id'] ?>" name="wprss_settings_general[<?php echo $field['field_id'] ?>]" type="text" value="<?php echo $feed_limit ?>" />
		<?php echo wprss_settings_inline_help( $field['field_id'], $field['tooltip'] );
	}
}

// Initialize
WPRSS_Feed_Access::instance();


/**
 * A padding layer used to give WPRSS more control over fetching of feed resources.
 * @since 4.7
 */
class WPRSS_SimplePie_File extends SimplePie_File {

	protected static $_default_certificate_file_path;
	protected $_certificate_file_path;
	
	
	/**
	 * Copied from {@see SimplePie_File#__construct()}.
	 * Adds call to {@see _before_curl_exec()}.
	 * 
	 * @since 4.7
	 */
	public function __construct( $url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false ) {
		if ( class_exists( 'idna_convert' ) ) {
			$idn = new idna_convert();
			$parsed = SimplePie_Misc::parse_url( $url );
			$url = SimplePie_Misc::compress_parse_url( $parsed['scheme'], $idn->encode( $parsed['authority'] ), $parsed['path'], $parsed['query'], $parsed['fragment'] );
			wprss_log_obj('Converted IDNA URL', $url, null, WPRSS_LOG_LEVEL_SYSTEM);
		}
		$this->url = $url;
		$this->useragent = $useragent;
		if ( preg_match( '/^http(s)?:\/\//i', $url ) ) {
			if ( $useragent === null ) {
				$useragent = ini_get( 'user_agent' );
				$this->useragent = $useragent;
			}
			if ( !is_array( $headers ) ) {
				$headers = array();
			}
			if ( !$force_fsockopen && function_exists( 'curl_exec' ) ) {
				$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE | SIMPLEPIE_FILE_SOURCE_CURL;
				$fp = curl_init();
				$headers2 = array();
				foreach ( $headers as $key => $value ) {
					$headers2[] = "$key: $value";
				}
				if ( version_compare( SimplePie_Misc::get_curl_version(), '7.10.5', '>=' ) ) {
					curl_setopt( $fp, CURLOPT_ENCODING, '' );
				}
				curl_setopt( $fp, CURLOPT_URL, $url );
				curl_setopt( $fp, CURLOPT_HEADER, 1 );
				curl_setopt( $fp, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $fp, CURLOPT_TIMEOUT, $timeout );
				curl_setopt( $fp, CURLOPT_CONNECTTIMEOUT, $timeout );
				curl_setopt( $fp, CURLOPT_REFERER, $url );
				curl_setopt( $fp, CURLOPT_USERAGENT, $useragent );
				curl_setopt( $fp, CURLOPT_HTTPHEADER, $headers2 );
				if ( !ini_get( 'open_basedir' ) && !ini_get( 'safe_mode' ) && version_compare( SimplePie_Misc::get_curl_version(), '7.15.2', '>=' ) ) {
					curl_setopt( $fp, CURLOPT_FOLLOWLOCATION, 1 );
					curl_setopt( $fp, CURLOPT_MAXREDIRS, $redirects );
				}

				$this->_before_curl_exec( $fp, $url );

				$this->headers = curl_exec( $fp );
				if ( curl_errno( $fp ) === 23 || curl_errno( $fp ) === 61 ) {
					curl_setopt( $fp, CURLOPT_ENCODING, 'none' );
					$this->headers = curl_exec( $fp );
				}
				if ( curl_errno( $fp ) ) {
					$this->error = 'cURL error ' . curl_errno( $fp ) . ': ' . curl_error( $fp );
					$this->success = false;
				} else {
					$info = curl_getinfo( $fp );
					curl_close( $fp );
					$this->headers = explode( "\r\n\r\n", $this->headers, $info['redirect_count'] + 1 );
					$this->headers = array_pop( $this->headers );
					$parser = new SimplePie_HTTP_Parser( $this->headers );
					if ( $parser->parse() ) {
						$this->headers = $parser->headers;
						$this->body = $parser->body;
						$this->status_code = $parser->status_code;
						if ( (in_array( $this->status_code, array( 300, 301, 302, 303, 307 ) ) || $this->status_code > 307 && $this->status_code < 400) && isset( $this->headers['location'] ) && $this->redirects < $redirects ) {
							$this->redirects++;
							$location = SimplePie_Misc::absolutize_url( $this->headers['location'], $url );
							return $this->__construct( $location, $timeout, $redirects, $headers, $useragent, $force_fsockopen );
						}
					}
				}
			} else {
				$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE | SIMPLEPIE_FILE_SOURCE_FSOCKOPEN;
				$url_parts = parse_url( $url );
				$socket_host = $url_parts['host'];
				if ( isset( $url_parts['scheme'] ) && strtolower( $url_parts['scheme'] ) === 'https' ) {
					$socket_host = "ssl://{$url_parts['host']}";
					$url_parts['port'] = 443;
				}
				if ( !isset( $url_parts['port'] ) ) {
					$url_parts['port'] = 80;
				}
				$fp = @fsockopen( $socket_host, $url_parts['port'], $errno, $errstr, $timeout );
				if ( !$fp ) {
					$this->error = 'fsockopen error: ' . $errstr;
					$this->success = false;
				} else {
					stream_set_timeout( $fp, $timeout );
					if ( isset( $url_parts['path'] ) ) {
						if ( isset( $url_parts['query'] ) ) {
							$get = "{$url_parts['path']}?{$url_parts['query']}";
						} else {
							$get = $url_parts['path'];
						}
					} else {
						$get = '/';
					}
					$out = "GET $get HTTP/1.1\r\n";
					$out .= "Host: {$url_parts['host']}\r\n";
					$out .= "User-Agent: $useragent\r\n";
					if ( extension_loaded( 'zlib' ) ) {
						$out .= "Accept-Encoding: x-gzip,gzip,deflate\r\n";
					}

					if ( isset( $url_parts['user'] ) && isset( $url_parts['pass'] ) ) {
						$out .= "Authorization: Basic " . base64_encode( "{$url_parts['user']}:{$url_parts['pass']}" ) . "\r\n";
					}
					foreach ( $headers as $key => $value ) {
						$out .= "$key: $value\r\n";
					}
					$out .= "Connection: Close\r\n\r\n";
					fwrite( $fp, $out );

					$info = stream_get_meta_data( $fp );

					$this->headers = '';
					while ( !$info['eof'] && !$info['timed_out'] ) {
						$this->headers .= fread( $fp, 1160 );
						$info = stream_get_meta_data( $fp );
					}
					if ( !$info['timed_out'] ) {
						$parser = new SimplePie_HTTP_Parser( $this->headers );
						if ( $parser->parse() ) {
							$this->headers = $parser->headers;
							$this->body = $parser->body;
							$this->status_code = $parser->status_code;
							if ( (in_array( $this->status_code, array( 300, 301, 302, 303, 307 ) ) || $this->status_code > 307 && $this->status_code < 400) && isset( $this->headers['location'] ) && $this->redirects < $redirects ) {
								$this->redirects++;
								$location = SimplePie_Misc::absolutize_url( $this->headers['location'], $url );
								return $this->__construct( $location, $timeout, $redirects, $headers, $useragent, $force_fsockopen );
							}
							if ( isset( $this->headers['content-encoding'] ) ) {
								// Hey, we act dumb elsewhere, so let's do that here too
								switch ( strtolower( trim( $this->headers['content-encoding'], "\x09\x0A\x0D\x20" ) ) ) {
									case 'gzip':
									case 'x-gzip':
										$decoder = new SimplePie_gzdecode( $this->body );
										if ( !$decoder->parse() ) {
											$this->error = 'Unable to decode HTTP "gzip" stream';
											$this->success = false;
										} else {
											$this->body = $decoder->data;
										}
										break;

									case 'deflate':
										if ( ($decompressed = gzinflate( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else if ( ($decompressed = gzuncompress( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else if ( function_exists( 'gzdecode' ) && ($decompressed = gzdecode( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else {
											$this->error = 'Unable to decode HTTP "deflate" stream';
											$this->success = false;
										}
										break;

									default:
										$this->error = 'Unknown content coding';
										$this->success = false;
								}
							}
						}
					} else {
						$this->error = 'fsocket timed out';
						$this->success = false;
					}
					fclose( $fp );
				}
			}
		} else {
			$this->method = SIMPLEPIE_FILE_SOURCE_LOCAL | SIMPLEPIE_FILE_SOURCE_FILE_GET_CONTENTS;
			if ( !$this->body = file_get_contents( $url ) ) {
				$this->error = 'file_get_contents could not read the file';
				$this->success = false;
			}
		}
	}
	
	
	/**
	 * Additional preparation of the curl request.
	 * Sets the {@link CURLOPT_CAINFO http://php.net/manual/en/function.curl-setopt.php}
	 * cURL option to a value determined by {@see get_default_certificate_file_path}.
	 * If the value is empty, leaves it as is.
	 * 
	 * @since 4.7
	 * @param resource $fp Pointer to a resource created by {@see curl_init()}.
	 * @param string $url The URL, to which the cURL request is being made.
	 * @return \WPRSS_SimplePie_File This instance.
	 */
	protected function _before_curl_exec( $fp, $url ) {
		if ( ($ca_path = self::get_default_certificate_file_path()) && !empty( $ca_path ) ) {
			$this->_certificate_file_path = $ca_path;
			curl_setopt( $fp, CURLOPT_CAINFO, $this->_certificate_file_path );
		}
		do_action( 'wprss_before_curl_exec', $fp );

		return $this;
	}
	
	
	/**
	 * Gets the path to the certificate, which will be used by this instance
	 * to fetch remote content.
	 * 
	 * @since 4.7
	 * @return string Path to the certificate file.
	 */
	public function get_certificate_file_path() {
		return $this->_certificate_file_path;
	}
	
	
	/**
	 * Gets the path to the certificate file, which will be used by future
	 * instances of this class.
	 * 
	 * @since 4.7
	 * @return string Path to the certificate file.
	 */
	public static function get_default_certificate_file_path() {
		return self::$_default_certificate_file_path;
	}
	
	
	/**
	 * Sets the path to the certificate file.
	 * This path will be used by future instances of this class.
	 * 
	 * @since 4.7
	 * @param string $path The path to the certificate file.
	 */
	public static function set_default_certificate_file_path( $path ) {
		self::$_default_certificate_file_path = $path;
	}

}
