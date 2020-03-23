<?php
/**
* Functions: list
* @version 1.0
* @package wp-cc-blank-theme
*/

/* Theme Constants (to speed up some common things) ------*/
define('HOME_URI', get_bloginfo( 'url' ));
define('PRE_HOME_URI',get_bloginfo('url').'/wp-content/themes');
define('SITE_NAME', get_bloginfo( 'name' ));
define('THEME_URI', get_template_directory_uri());
define('THEME_IMG', THEME_URI . '/assets/img');
define('THEME_CSS', THEME_URI . '/assets/css');
define('THEME_FONTS', THEME_URI . '/assets/fonts');
define('THEME_JS', THEME_URI. '/assets/js');
/*
	calling related files
*/
	// include TEMPLATEPATH . '/inc/site.php';
	// include TEMPLATEPATH . '/inc/render.php';
	// include TEMPLATEPATH . '/inc/widgets.php';
	// include TEMPLATEPATH . '/inc/helpers.php';
	// include TEMPLATEPATH . '/inc/metaboxes.php';
	// include TEMPLATEPATH . '/inc/search.php';
	// include TEMPLATEPATH . '/inc/settings.php';
	// include TEMPLATEPATH . '/inc/filters.php';

//Custom Post type files
//include TEMPLATEPATH . '/inc/custom-post-type/queulat-cc-chapters-cpt-plugin/cc-chapters-cpt-plugin.php';

/**
 * Images
 * ------
 * */
// Add theme suppor for post thumbnails
add_theme_support( 'post-thumbnails' );
// Define the default post thumbnail size

// set_post_thumbnail_size( 200, 130, true );

// Define custom thumbnail sizes
// add_image_size( $name, $width, $height, $crop );
add_image_size( 'squared', 300, 300, true );
add_image_size( 'landscape-medium', 740, 416, true );
add_image_size( 'landscape-featured', 2000, 700, true );
/*
	REGISTER SIDEBARS
*/
/*
	Theme sidebars
*/
$mandatory_sidebars = array(
	
	'Single' => array(
		'name' => 'single',
	),
    'Page' => array(
		'name' => 'page',
	)
);
$mandatory_sidebars = apply_filters('cc_theme_base_mandatory_sidebars',$mandatory_sidebars);
foreach ( $mandatory_sidebars as $sidebar => $id_sidebar ) {
	register_sidebar( array(
		'name'          => $sidebar,
		'id'			=> $id_sidebar['name'],
		'before_widget' => '<section id="%1$s" class="widget %2$s">'."\n",
		'after_widget'  => '</section>',
		'before_title'  => '<header class="widget-header"><h3 class="widget-title">',
		'after_title'   => '</h3></header>'
	) );
}

/**
 * Theme specific stuff
 * --------------------
 * */

/**
 * Theme singleton class
 * ---------------------
 * Stores various theme and site specific info and groups custom methods
 **/
class site {
	private static $instance;

	protected $settings;
	public $show_welcome = true;

	const id = __CLASS__;
	const theme_ver = '20191001';
	private function __construct(){
		/**
		 * Get our custom theme options so we can easily access them
		 * on templates or other admin pages
		 * */
		$this->actions_manager();

	}
	public function __get($key){
		return isset($this->$key) ? $this->$key : null;
	}
	public function __isset($key){
		return isset($this->$key);
	}
	public static function get_instance(){
		if ( !isset(self::$instance) ){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	public function __clone(){
		trigger_error( 'Clone is not allowed.', E_USER_ERROR );
	}
	/**
	 * Setup theme actions, both in the front and back end
	 * */
	public function actions_manager(){
		add_action( 'after_setup_theme', array($this, 'setup_theme') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
		add_action( 'enqueue_scripts', array($this, 'enqueue_scripts') );
		//add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );
		add_action('init', array($this, 'init_functions') );
		add_action('init', array($this,'register_menus_locations') );
	}
	public function init_functions() {
		add_post_type_support( 'page', 'excerpt' );
	}
	/**
	 * Enable theme extra supports
	 * @return void
	 */
	public function setup_theme(){
		add_theme_support('post-formats', array('gallery', 'image', 'video'));
		add_theme_support('post-thumbnails');
		add_theme_support('menus');
	}

	public function register_menus_locations(){
        $theme_base_menus = array(
            'main-menu' => 'Main menu',
            'main-menu-mobile' => 'Main menu mobile',
			'footer' => 'Footer menu'
        );
        $theme_base_menus = apply_filters('cc_theme_base_menus',$theme_base_menus);
		register_nav_menus();
	}

	public function get_post_thumbnail_url( $postid = null, $size = 'landscape-medium' ){
		if ( is_null($postid) ){
			global $post;
			$postid = $post->ID;
		}
		$thumb_id = get_post_thumbnail_id( $postid );
		$img_src  = wp_get_attachment_image_src( $thumb_id, $size );
		return $img_src ? current( $img_src ) : '';
	}

	public function enqueue_styles(){
		// Front-end styles
		wp_enqueue_style( 'Gfonts', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700|Source+Sans+Pro:400,400i,600" rel="stylesheet'); //TO-DO local webfonts
        //TO-DO add CC Akzidens
        wp_enqueue_style( 'cc_blank_style', THEME_CSS .'/styles.css', self::theme_ver );
		wp_enqueue_style( 'dashicons' );
	}

	function admin_enqueue_scripts(){

		// admin scripts
	}

	function enqueue_scripts(){
		// front-end scripts
		wp_enqueue_script( 'jquery' , true);
		wp_enqueue_script( 'dependencies', THEME_JS .'/dependencies.js', array('jquery'), self::theme_ver, true );
		wp_enqueue_script( 'cc_blank_script', THEME_JS .'/script.js', array('jquery'), self::theme_ver, true );
		//attach data to script.js
		$ajax_data = array(
			'url' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'cc_blank_script', 'Ajax', $ajax_data );
	}
}

/**
 * Instantiate the class object
 * */

$_s = site::get_instance();