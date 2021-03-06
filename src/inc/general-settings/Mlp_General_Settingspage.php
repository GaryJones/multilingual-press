<?php

/**
 * Settings page controller.
 *
 * @version 2014.07.04
 * @author  Inpsyde GmbH, toscho
 * @license GPL
 */
class Mlp_General_Settingspage {

	/**
	 * @type Mlp_Assets_Interface
	 */
	private $assets;

	/**
	 * @var Mlp_General_Settings_Module_Mapper
	 */
	private $model;

	/**
	 * @var Mlp_Module_Manager_Interface
	 */
	private $modules;

	/**
	 * @var string
	 */
	private $page_hook;

	/**
	 * Constructor
	 *
	 * @param Mlp_Module_Manager_Interface $modules
	 * @param Mlp_Assets_Interface         $assets
	 */
	public function __construct( Mlp_Module_Manager_Interface $modules, Mlp_Assets_Interface $assets ) {

		$this->modules = $modules;
		$this->assets = $assets;
	}

	/**
	 * Set up the page.
	 *
	 * @wp-hook plugins_loaded
	 *
	 * @return void
	 */
	public function setup() {

		$this->model = new Mlp_General_Settings_Module_Mapper( $this->modules );

		add_filter( 'network_admin_menu', [ $this, 'register_settings_page' ] );
		add_filter( 'admin_post_mlp_update_modules', [ $this->model, 'update_modules' ] );
		add_action( 'network_admin_notices', [ $this, 'show_update_message' ] );
	}

	/**
	 * Add MultilingualPress network settings and module page.
	 *
	 * @return void
	 */
	public function register_settings_page() {

		$view = new Mlp_General_Settings_View( $this->model );

		// Register options page
		$this->page_hook = add_submenu_page(
			'settings.php',
			__( 'MultilingualPress', 'multilingual-press' ),
			__( 'MultilingualPress', 'multilingual-press' ),
			'manage_network_options',
			'mlp',
			[ $view, 'render_page' ]
		);

		$this->assets->provide( [ 'mlp-admin', 'mlp_admin_css' ] );
	}

	/**
	 * Handle update messages.
	 *
	 * @return void
	 */
	public function show_update_message() {

		global $hook_suffix;

		if ( empty( $hook_suffix ) || $this->page_hook !== $hook_suffix ) {
			return;
		}

		if ( ! isset( $_GET[ 'message' ] ) ) {
			return;
		}

		$msg = __( 'Settings saved.', 'multilingual-press' );
		$notice = new Mlp_Admin_Notice( $msg, [ 'class' => 'updated' ] );
		$notice->show();
	}

}
