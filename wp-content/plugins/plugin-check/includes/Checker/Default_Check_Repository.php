<?php
/**
 * Class WordPress\Plugin_Check\Checker\Default_Check_Repository
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Checker;

/**
 * Default Check Repository class.
 *
 * @since 1.0.0
 */
class Default_Check_Repository extends Empty_Check_Repository {

	/**
	 * True if the class was fully initialized.
	 *
	 * If the class is instantiated before plugins are loaded, this will be set to false.
	 * This way the checks will be re-initialized once plugins have been loaded, and only then it is set to true.
	 *
	 * @since 1.3.0
	 * @var bool
	 */
	protected $fully_initialized;

	/**
	 * Initializes checks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->fully_initialized = did_action( 'plugins_loaded' ) > 0;
		$this->register_default_checks();
	}

	/**
	 * Returns an array of checks.
	 *
	 * @since 1.0.0
	 *
	 * @param int $flags The check type flag.
	 * @return Check_Collection Check collection providing an indexed array of check instances.
	 */
	public function get_checks( $flags = self::TYPE_ALL ) {
		// Once plugins have been loaded, re-initialize the checks.
		if ( ! $this->fully_initialized && did_action( 'plugins_loaded' ) ) {
			$this->fully_initialized = true;
			$this->runtime_checks    = array();
			$this->static_checks     = array();
			$this->register_default_checks();
		}

		return parent::get_checks( $flags );
	}

	/**
	 * Registers Checks.
	 *
	 * @since 1.0.0
	 */
	private function register_default_checks() {
		/**
		 * Filters the available plugin check classes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $checks An array map of check slugs to Check instances.
		 */
		$checks = apply_filters(
			'wp_plugin_check_checks',
			array(
				'i18n_usage'                 => new Checks\General\I18n_Usage_Check(),
				'enqueued_scripts_size'      => new Checks\Performance\Enqueued_Scripts_Size_Check(),
				'enqueued_styles_size'       => new Checks\Performance\Enqueued_Styles_Size_Check(),
				'code_obfuscation'           => new Checks\Plugin_Repo\Code_Obfuscation_Check(),
				'file_type'                  => new Checks\Plugin_Repo\File_Type_Check(),
				'plugin_header_fields'       => new Checks\Plugin_Repo\Plugin_Header_Fields_Check(),
				'late_escaping'              => new Checks\Security\Late_Escaping_Check(),
				'plugin_updater'             => new Checks\Plugin_Repo\Plugin_Updater_Check(),
				'plugin_review_phpcs'        => new Checks\Plugin_Repo\Plugin_Review_PHPCS_Check(),
				'direct_db_queries'          => new Checks\Security\Direct_DB_Queries_Check(),
				'performant_wp_query_params' => new Checks\Performance\Performant_WP_Query_Params_Check(),
				'enqueued_scripts_in_footer' => new Checks\Performance\Enqueued_Scripts_In_Footer_Check(),
				'enqueued_resources'         => new Checks\Performance\Enqueued_Resources_Check(),
				'plugin_readme'              => new Checks\Plugin_Repo\Plugin_Readme_Check(),
				'enqueued_styles_scope'      => new Checks\Performance\Enqueued_Styles_Scope_Check(),
				'enqueued_scripts_scope'     => new Checks\Performance\Enqueued_Scripts_Scope_Check(),
				'localhost'                  => new Checks\Plugin_Repo\Localhost_Check(),
				'no_unfiltered_uploads'      => new Checks\Plugin_Repo\No_Unfiltered_Uploads_Check(),
				'trademarks'                 => new Checks\Plugin_Repo\Trademarks_Check(),
				'non_blocking_scripts'       => new Checks\Performance\Non_Blocking_Scripts_Check(),
				'offloading_files'           => new Checks\Plugin_Repo\Offloading_Files_Check(),
				'image_functions'            => new Checks\Performance\Image_Functions_Check(),
				'setting_sanitization'       => new Checks\Plugin_Repo\Setting_Sanitization_Check(),
			)
		);

		foreach ( $checks as $slug => $check ) {
			$this->register_check( $slug, $check );
		}
	}
}
