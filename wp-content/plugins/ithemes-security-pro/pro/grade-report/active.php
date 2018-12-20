<?php

final class ITSEC_Grading_System_Active {
	private static $instance = false;


	private function __construct() {
		$this->add_hooks();
	}

	public static function init() {
		if ( self::$instance ) {
			return;
		}

		self::$instance = new self();
	}

	private function add_hooks() {
		add_action( 'wp_ajax_itsec_grade_report_page', array( $this, 'handle_ajax_request' ) );
		add_filter( 'itsec-admin-page-file-path-grade-report', array( $this, 'get_admin_page_file' ) );
		add_filter( 'itsec-admin-page-refs', array( $this, 'filter_admin_page_refs' ), 10, 3 );
		add_filter( 'itsec_mail_digest', array( $this, 'customize_digest' ), 10, 2 );
		add_filter( 'itsec_security_digest_include_security_check', '__return_false' );
	}

	public function get_admin_page_file( $file ) {
		return dirname( __FILE__ ) . '/admin-page/page.php';
	}

	public function filter_admin_page_refs( $page_refs, $capability, $callback ) {
		$page_refs[] = add_submenu_page( 'itsec', '', __( 'Grade Report', 'it-l10n-ithemes-security-pro' ), $capability, 'itsec-grade-report', $callback );

		return $page_refs;
	}

	public function handle_ajax_request() {
		do_action( 'wp_ajax_itsec_settings_page' );
	}

	/**
	 * Customize the Daily Digest email to include the current grade.
	 *
	 * @param array      $content
	 * @param ITSEC_Mail $mail
	 *
	 * @return array
	 */
	public function customize_digest( $content, $mail ) {

		if ( ! isset( $content['intro'] ) ) {
			return $content;
		}

		require_once( dirname( __FILE__ ) . '/report.php' );
		$report = ITSEC_Grading_System::get_report();

		$grade = $report['grade']['real'];

		switch ( $grade[0] ) {
			case 'A':
				$color = '#00C778';
				break;
			case 'B':
				$color = '#00A0D2';
				break;
			case 'C':
				$color = '#FA9408';
				break;
			case 'D':
				$color = '#E7635D';
				break;
			case 'F':
				$color = '#98030E';
				break;
			default:
				$color = '';
				break;
		}

		$summary = $this->get_grade_summary_html( $grade, $color, $this->get_summary( $report ) ) . $mail->get_divider();

		$content = ITSEC_Lib::array_insert_after( 'intro', $content, 'grade-summary', $summary );

		return $content;
	}

	private function get_grade_summary_html( $grade, $color, $summary ) {

		$template = file_get_contents( dirname( __FILE__ ) . '/mail-templates/grade-overview.html' );

		$tags = array(
			'grade'       => $grade,
			'grade_color' => $color,
			'summary'     => $summary,
			'title'       => esc_html__( 'Your Current WordPress Security Grade', 'it-l10n-ithemes-security-pro' ),
			'button_text' => esc_html__( 'See Your Grade Report →', 'it-l10n-ithemes-security-pro' ),
			'button_link' => esc_url( network_admin_url( 'admin.php?page=itsec-grade-report' ) ),
		);

		return ITSEC_Lib::replace_tags( $template, $tags );
	}

	private function get_summary( $report ) {

		if ( 0 === $report['issues'] ) {
			return esc_html__( 'Great work! Based on your current security settings and software, your website has gotten the top WordPress security grade possible.', 'it-l10n-ithemes-security-pro' );
		}

		if ( 0 === $report['fixable_issues'] ) {
			return sprintf(
				esc_html__( 'Your WordPress Security Grade is based on your current security settings and software. %1$sView details%2$s about your WordPress security grade.', 'it-l10n-ithemes-security-pro' ),
				'<a href="' . esc_attr( ITSEC_Mail::filter_admin_page_url( network_admin_url( 'admin.php?page=itsec-grade-report' ) ) ) . '">',
				'</a>'
			);
		}

		return sprintf(
			esc_html__( 'Your WordPress Security Grade is based on your current security settings and software. Resolve %1$sthese issues now%2$s to raise your WordPress security grade.', 'it-l10n-ithemes-security-pro' ),
			'<a href="' . esc_attr( ITSEC_Mail::filter_admin_page_url( network_admin_url( 'admin.php?page=itsec-grade-report' ) ) ) . '">',
			'</a>'
		);
	}
}

ITSEC_Grading_System_Active::init();
