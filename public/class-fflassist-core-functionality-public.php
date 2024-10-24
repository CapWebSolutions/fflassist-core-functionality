<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Fflassist_Core_Functionality
 * @subpackage Fflassist_Core_Functionality/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fflassist_Core_Functionality
 * @subpackage Fflassist_Core_Functionality/public
 * @author     Your Name <email@example.com>
 */
class Fflassist_Core_Functionality_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $fflassist_core_functionality    The ID of this plugin.
	 */
	private $fflassist_core_functionality;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $fflassist_core_functionality       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $fflassist_core_functionality, $version ) {

		$this->fflassist_core_functionality = $fflassist_core_functionality;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fflassist_Core_Functionality_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fflassist_Core_Functionality_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->fflassist_core_functionality, plugin_dir_url( __FILE__ ) . 'css/xxx-core-functionality-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fflassist_Core_Functionality_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fflassist_Core_Functionality_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->fflassist_core_functionality, plugin_dir_url( __FILE__ ) . 'js/xxx-core-functionality-public.js', array( 'jquery' ), $this->version, false );

	}

}
