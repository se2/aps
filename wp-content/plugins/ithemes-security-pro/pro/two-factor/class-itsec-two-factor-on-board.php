<?php

/**
 * Class ITSEC_Two_Factor_On_Board
 */
class ITSEC_Two_Factor_On_Board {

	const LAST_PROMPT_META_KEY = '_itsec_2fa_last_prompt';
	const SKIP_TIMES_META_KEY = '_itsec_2fa_skips';

	/** @var ITSEC_Two_Factor */
	private $two_factor;

	/**
	 * ITSEC_Two_Factor_On_Board constructor.
	 *
	 * @param ITSEC_Two_Factor $two_factor
	 */
	public function __construct( ITSEC_Two_Factor $two_factor ) { $this->two_factor = $two_factor; }

	public function run() {
		add_action( 'itsec_login_interstitial_init', array( $this, 'register' ) );
	}

	/**
	 * Register our login interstitial.
	 *
	 * @param ITSEC_Lib_Login_Interstitial $lib
	 */
	public function register( $lib ) {
		$lib->register( '2fa-on-board', array( $this, 'render' ), array(
			'show_to_user'     => array( $this, 'show_to_user' ),
			'force_completion' => array( $this, 'force_completion' ),
			'submit'           => array( $this, 'submit' ),
			'ajax_handler'     => array( $this, 'ajax_handler' ),
			'wp_login_only'    => true,
		) );
	}

	/**
	 * Whether the on board prompt should be shown to the given user.
	 *
	 * @param WP_User $user
	 * @param bool    $requested
	 *
	 * @return bool
	 */
	public function show_to_user( $user, $requested ) {

		if ( ! $this->get_available_providers( $user ) ) {
			return false;
		}

		if ( $this->two_factor->get_available_providers_for_user( $user, false ) ) {
			return false;
		}

		if ( $requested ) {
			return true;
		}

		if ( 'user_type' === $this->two_factor->get_two_factor_requirement_reason( $user->ID ) ) {
			return true;
		}

		$last_prompt  = (int) get_user_meta( $user->ID, self::LAST_PROMPT_META_KEY, true );
		$time_elapsed = ITSEC_Core::get_current_time_gmt() - $last_prompt;

		if ( $time_elapsed / WEEK_IN_SECONDS > 2 ) {
			return true;
		}

		return false;
	}

	/**
	 * Does the given user have to complete the 2fa flow.
	 *
	 * @param WP_User $user
	 *
	 * @return bool
	 */
	public function force_completion( $user ) {
		return (bool) $this->two_factor->get_two_factor_requirement_reason( $user->ID );
	}

	/**
	 * Process the Two-Factor response.
	 *
	 * @param WP_User $user
	 * @param array   $data
	 *
	 * @return WP_Error|null
	 */
	public function submit( $user, $data ) {

		require_once( dirname( __FILE__ ) . '/providers/class.two-factor-backup-codes.php' );

		if ( ! empty( $data['itsec_skip'] ) ) {
			if ( $this->force_completion( $user ) ) {
				return new WP_Error(
					'itsec-2fa-on-board-cannot-skip',
					esc_html__( 'Your account is required to setup Two Factor authentication.', 'it-l10n-ithemes-security-pro' )
				);
			}

			if ( get_user_meta( $user->ID, Two_Factor_Backup_Codes::TEMP_FLAG_META_KEY, true ) ) {
				delete_user_meta( $user->ID, Two_Factor_Backup_Codes::BACKUP_CODES_META_KEY );
				delete_user_meta( $user->ID, Two_Factor_Backup_Codes::TEMP_FLAG_META_KEY );
			}

			update_user_meta( $user->ID, self::LAST_PROMPT_META_KEY, ITSEC_Core::get_current_time_gmt() );

			$skips = (int) get_user_meta( $user->ID, self::SKIP_TIMES_META_KEY, true );
			update_user_meta( $user->ID, self::SKIP_TIMES_META_KEY, $skips + 1 );

			return null;
		}

		if ( empty( $data['itsec_two_factor_on_board_data'] ) ) {
			return new WP_Error(
				'itsec-2fa-on-board-no-data',
				esc_html__( 'No On-Board data provided.', 'it-l10n-ithemes-security-pro' )
			);
		}

		$providers = json_decode( wp_unslash( $data['itsec_two_factor_on_board_data'] ), true );

		if ( null === $providers || ( function_exists( 'json_last_error' ) && json_last_error() ) ) {
			return new WP_Error(
				'itsec-2fa-on-board-invalid-json',
				sprintf( esc_html__( 'Invalid On-Board data: %s', 'it-l10n-ithemes-security-pro' ), json_last_error_msg() )
			);
		}

		$enabled = array();
		$primary = false;

		foreach ( $providers as $provider ) {
			if ( $provider['status'] !== 'disabled' ) {
				$enabled[] = $provider['id'];
			}
		}

		if ( in_array( 'Two_Factor_Totp', $enabled, true ) ) {
			$primary = 'Two_Factor_Totp';
		} elseif ( in_array( 'Two_Factor_Email', $enabled, true ) ) {
			$primary = 'Two_Factor_Email';
		}

		if ( ! in_array( 'Two_Factor_Backup_Codes', $enabled, true ) && get_user_meta( $user->ID, Two_Factor_Backup_Codes::TEMP_FLAG_META_KEY, true ) ) {
			delete_user_meta( $user->ID, Two_Factor_Backup_Codes::BACKUP_CODES_META_KEY );
		}
		delete_user_meta( $user->ID, Two_Factor_Backup_Codes::TEMP_FLAG_META_KEY );

		$this->two_factor->set_enabled_providers_for_user( $enabled, $user->ID );

		if ( $primary ) {
			$this->two_factor->set_primary_provider_for_user( $primary, $user->ID );
		}

		update_user_meta( $user->ID, self::LAST_PROMPT_META_KEY, ITSEC_Core::get_current_time_gmt() );
		delete_user_meta( $user->ID, Two_Factor_Backup_Codes::TEMP_FLAG_META_KEY );

		return null;
	}

	/**
	 * Ajax handler.
	 *
	 * @param WP_User
	 * @param array $data
	 *
	 * @return void
	 */
	public function ajax_handler( $user, $data ) {

		if ( empty( $data['itsec_method'] ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Invalid ajax method.', 'it-l10n-ithemes-security-pro' ),
			) );
		}

		foreach ( $this->get_available_providers( $user ) as $provider ) {
			$provider->handle_ajax_on_board( $user, $data );
		}

		wp_send_json_error( array(
			'message' => esc_html__( 'Invalid ajax method.', 'it-l10n-ithemes-security-pro' ),
		) );
	}

	/**
	 * Render the on board HTML.
	 *
	 * @param WP_User $user
	 */
	public function render( $user ) {

		$can_skip  = ! $this->force_completion( $user );
		$providers = array();

		foreach ( $this->get_available_providers( $user ) as $provider ) {
			$providers[] = array(
				'id'           => get_class( $provider ),
				'label'        => $provider->get_on_board_label(),
				'description'  => $provider->get_on_board_description(),
				'dashicon'     => $provider->get_on_board_dashicon(),
				'configurable' => $provider->has_on_board_configuration(),
				'config'       => $provider->get_on_board_config( $user ),
				'status'       => $this->get_provider_status( $user, $provider ),
			);
		}

		wp_enqueue_style( 'itsec-2fa-on-board', plugin_dir_url( __FILE__ ) . 'css/on-board.css', array( 'dashicons' ) );
		wp_enqueue_script( 'itsec-2fa-on-board', plugin_dir_url( __FILE__ ) . 'js/on-board.js', array( 'jquery', 'wp-backbone', 'underscore', 'wp-a11y' ), 3 );
		wp_localize_script( 'itsec-2fa-on-board', 'ITSEC2FAOnBoard', array(
			'user'      => $user->ID,
			'can_skip'  => $can_skip,
			'providers' => $providers,
			'l10n'      => array(
				'enabled'              => __( 'Enabled', 'it-l10n-ithemes-security-pro' ),
				'disabled'             => __( 'Disabled', 'it-l10n-ithemes-security-pro' ),
				'not-configured'       => __( 'Unconfigured', 'it-l10n-ithemes-security-pro' ),
				'backup_codes_warning' => sprintf(
					esc_html__( 'Make sure to copy or download the backup codes before proceeding. %1$s Ok %2$s', 'it-l10n-ithemes-security-pro' ),
					'<button class="button-link">',
					'</button>'
				),
				'require_notice'       => $this->two_factor->get_reason_description( $this->two_factor->get_two_factor_requirement_reason( $user->ID ) ),
			),
		) );

		$two_factor_info = esc_html__( 'WordPress two-factor authentication (or WordPress 2-step verification) adds an important extra layer of protection to your login on this website by requiring 1) a password and 2) a secondary time-sensitive code to login.', 'it-l10n-ithemes-security-pro' );

		/**
		 * Filter the info about Two-Factor provided on the first screen of the Two Factor On-Board flow.
		 *
		 * @param string  $two_factor_info Info text.
		 * @param WP_User $user            The user being shown the flow. Do Not use wp_get_current_user().
		 */
		$two_factor_info = apply_filters( 'itsec_two_factor_on_board_info', $two_factor_info, $user );
		?>

		<div id="itsec-2fa-on-board-app">
			<div class="itsec-screen itsec-screen--intro">
				<div class="itsec-screen__content">
					<noscript><?php esc_html_e( 'JavaScript is required to setup Two-Factor Authentication.', 'it-l10n-ithemes-security-pro' ); ?></noscript>
					<h2 style="margin-bottom: .5em"><?php esc_html_e( 'Setup Two-Factor', 'it-l10n-ithemes-security-pro' ); ?></h2>
					<p>
						<?php echo $two_factor_info; ?>
					</p>
				</div>
				<div class="itsec-screen__actions">
					<?php if ( $can_skip ) : ?>
						<button class="button-link itsec-screen__actions--skip" name="itsec_skip" value="skip" type="submit" disabled>
							<?php esc_html_e( 'Skip', 'it-l10n-ithemes-security-pro' ); ?>
						</button>
					<?php endif; ?>
					<button class="button button-primary itsec-screen__actions--continue" disabled>
						<?php esc_html_e( 'Continue', 'it-l10n-ithemes-security-pro' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php

		require_once( dirname( __FILE__ ) . '/includes/template.php' );
	}

	/**
	 * Get the available providers.
	 *
	 * @param WP_User $user
	 *
	 * @return ITSEC_Two_Factor_Provider_On_Boardable[]
	 */
	private function get_available_providers( $user ) {
		$providers = array();

		foreach ( $this->two_factor->get_helper()->get_enabled_provider_instances() as $provider ) {
			if ( $provider instanceof ITSEC_Two_Factor_Provider_On_Boardable ) {
				$providers[] = $provider;
			}
		}

		return $providers;
	}

	/**
	 * Get the provider status.
	 *
	 * @param WP_User             $user
	 * @param Two_Factor_Provider $provider
	 *
	 * @return string
	 */
	private function get_provider_status( $user, $provider ) {

		$is_configured = (bool) $this->two_factor->get_available_providers_for_user( $user, false );

		$default_enabled = ! $this->force_completion( $user ) ? array() : array(
			'Two_Factor_Backup_Codes',
			'Two_Factor_Email',
		);

		$enabled = $this->two_factor->get_enabled_providers_for_user( $user );

		if ( ! $is_configured && in_array( get_class( $provider ), $default_enabled, true ) ) {
			return $provider->is_available_for_user( $user ) ? 'enabled' : 'not-configured';
		} elseif ( ! isset( $enabled[ get_class( $provider ) ] ) ) {
			return 'disabled';
		} elseif ( $provider->is_available_for_user( $user ) ) {
			return 'enabled';
		} else {
			return 'not-configured';
		}
	}

}