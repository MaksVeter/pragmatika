<?php

namespace modules\theme;

use WPKit\AdminPage\OptionPage;
use WPKit\Module\AbstractThemeInitialization;
use WPKit\Options\Option;
use WPKit\Options\OptionBox;

/**
 * Class Initialization
 *
 * @package modules\theme
 */
class Initialization extends AbstractThemeInitialization {

	const OPTIONS_PAGE_SOCIAL = 'social';

	/**
	 * @var array
	 */
	protected static $_image_sizes = [
		//'sample_size' => [270, 430, true],
	];

	public function register_image_sizes()
	{
		foreach (static::$_image_sizes as $key => $data) {
			$width  = isset($data[0]) ? $data[0] : 0;
			$height = isset($data[1]) ? $data[1] : 0;
			$crop   = isset($data[2]) ? $data[2] : false;

			add_image_size($key, $width, $height, $crop);
		}
	}

	public function register_nav_menus()
	{
		register_nav_menus([
			'primary' => __('Primary', THEME_TEXT_DOMAIN),
		]);
	}

	public function register_dynamic_sidebars()
	{

	}

	public function register_remove_admin_bar_nodes_hook()
	{
		add_action('admin_bar_menu', function (\WP_Admin_Bar $wp_admin_bar) {
			$wp_admin_bar->remove_node('comments');
		}, 999);
	}

	public function register_async_scripts_loading()
	{
		add_filter('script_loader_tag', function ($tag) {
			return !is_admin() ? str_replace(' src', ' defer src', $tag) : $tag;
		}, 10, 2);
		/**
		 * For cases when jQuery is used before it will be loaded (in content)
		 * Also added code snippet in the end of last script which call all saved functions
		 */
		add_action('wp_head', function () {
			?>
            <script>
                (function (w, d, u) {
                    var alias,
                            pushToQ;

                    w.bindReadyQ = [];
                    w.bindLoadQ = [];

                    pushToQ = function (x, y) {

                        switch (x) {
                            case 'load':
                                w.bindLoadQ.push(y);

                                break;
                            case 'ready':
                                w.bindReadyQ.push(y);

                                break;
                            default:
                                w.bindReadyQ.push(x);

                                break;
                        }
                    };

                    alias = {
                        load: pushToQ,
                        ready: pushToQ,
                        bind: pushToQ,
                        on: pushToQ
                    };

                    w.$ = w.jQuery = function (handler) {

                        if (handler === d || handler === u || handler === w) {
                            return alias;
                        } else {
                            pushToQ(handler);
                        }
                    };
                })(window, document);
            </script>
			<?php
		}, 1);
	}

	public function add_action_wp_enqueue_scripts()
	{
		static::_enqueue_styles();
		static::_enqueue_scripts();
	}

	public function add_action_after_setup_theme()
	{
		add_theme_support('post-thumbnails', ['post', 'page']);
		add_theme_support('title-tag');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'rest_output_link_wp_head');
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		add_editor_style($this->get_theme_assets_url() . "/built/stylesheets/editor.css");
		add_editor_style($this->get_theme_assets_url() . "/built/stylesheets/customize.css");
	}

	public function add_action_login_enqueue_scripts()
	{
		wp_enqueue_style('theme-login', $this->get_theme_assets_url() . '/built/stylesheets/login.css');
	}

	public function add_action_admin_init()
	{
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('commentsdiv', 'post', 'normal');
		remove_meta_box('commentsdiv', 'page', 'normal');
		remove_meta_box('commentstatusdiv', 'post', 'normal');
		remove_meta_box('commentstatusdiv', 'page', 'normal');
		remove_meta_box('postcustom', 'post', 'normal');
		remove_meta_box('postcustom', 'page', 'normal');
		remove_meta_box('trackbacksdiv', 'post', 'normal');
	}

	public function add_action_admin_menu()
	{
		remove_menu_page('edit-comments.php');
		remove_submenu_page('options-general.php', 'options-discussion.php');

		if ( !is_super_admin()) {
			remove_menu_page('plugins.php');
			remove_submenu_page('themes.php', 'themes.php');
		}
	}

	/**
	 * @param \WP_Screen $current_screen
	 */
	public function add_action_current_screen($current_screen)
	{
		if ($current_screen->base == 'edit') {
			$columns_hook = function ($columns) {
				unset($columns['comments']);

				return $columns;
			};

			foreach (['posts', 'pages'] as $post_type) {
				add_filter("manage_{$post_type}_columns", $columns_hook);
			}
		}
	}

	public function add_action_header()
	{
		echo Option::get('ga_code');
	}

	public function add_action_admin_enqueue_scripts()
	{
		wp_enqueue_style('theme-admin', $this->get_theme_assets_url() . '/built/stylesheets/admin.css');
	}

	public function add_action_wp_footer()
	{
		echo "<noscript>
            <div style=\"position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 20px; background-color: #FFF; text-align: center; color: #000; z-index: 999; border-top: 1px solid #000;\">
                " . __('JavaScript is disabled on your browser. Please enable JavaScript or upgrade to a JavaScript-capable browser to use this site.', THEME_TEXT_DOMAIN) . "
            </div>
        </noscript>
        <script>
            document.getElementsByTagName('html')[0].className = document.getElementsByTagName('html')[0].className.replace(/\b(no-js)\b/,'');
        </script>";
	}

	/**
	 * @return string
	 */
	public function add_filter_network_home_url()
	{
		return home_url('/');
	}

	/**
	 * @return string
	 */
	public function add_filter_excerpt_more()
	{
		return '...';
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	public function add_filter_embed_oembed_html($html)
	{
		return "<div class=\"video-container\">$html</div>";
	}

	/**
	 * @param array $settings
	 *
	 * @return array
	 */
	public function add_filter_tiny_mce_before_init($settings)
	{
		$settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre';

		return $settings;
	}

	public function admin_register_option_pages()
	{
		new OptionPage('theme', __('Theme Settings', THEME_TEXT_DOMAIN), 'options-general.php');
		$option_page = new OptionPage('contact', __('Contact', THEME_TEXT_DOMAIN));
		$option_page->set_menu_icon('dashicons-email');
		new OptionPage('office', __('Offices', THEME_TEXT_DOMAIN), 'contact');
		new OptionPage('social', __('Social', THEME_TEXT_DOMAIN), 'contact');
	}

	public function admin_register_options()
	{
		$this->_add_theme_options();
		$this->_add_social_options();
	}

	public function admin_register_widgets_hook()
	{
		add_action('widgets_init', function () {
			unregister_widget('WP_Widget_Calendar');
			unregister_widget('WP_Widget_Archives');
			unregister_widget('WP_Widget_Links');
			unregister_widget('WP_Widget_Meta');
			unregister_widget('WP_Widget_Search');
			unregister_widget('WP_Widget_Categories');
			unregister_widget('WP_Widget_Pages');
			unregister_widget('WP_Widget_Recent_Posts');
			unregister_widget('WP_Widget_Recent_Comments');
			unregister_widget('WP_Widget_RSS');
			unregister_widget('WP_Widget_Tag_Cloud');
			unregister_widget('WP_Nav_Menu_Widget');
		}, 20);
	}

	public function admin_register_remove_from_nav_menus()
	{
		add_action('admin_head-nav-menus.php', function () {
			remove_meta_box('add-post_tag', 'nav-menus', 'side');
			remove_meta_box('add-post', 'nav-menus', 'side');
			remove_meta_box('add-category', 'nav-menus', 'side');
		});
	}

	protected function _enqueue_styles()
	{
		wp_enqueue_style(
			'theme',
			$this->get_theme_assets_url() . "/built/stylesheets/screen.css"
		);
		wp_enqueue_style(
			'theme-print',
			$this->get_theme_assets_url() . "/built/stylesheets/print.css",
			[],
			null,
			'print'
		);
	}

	protected function _enqueue_scripts()
	{
		wp_register_script(
			'theme',
			$this->get_theme_assets_url() . '/built/javascripts/common.js',
			['jquery',],
			null,
			true
		);
//		wp_localize_script('theme', 'theme_settings', []);
		wp_enqueue_script('theme');
	}

	public function register_remove_rel_next_links()
	{
		add_filter('index_rel_link', '__return_false');
		add_filter('parent_post_rel_link', '__return_false');
		add_filter('start_post_rel_link', '__return_false');
		add_filter('previous_post_rel_link', '__return_false');
		add_filter('next_post_rel_link', '__return_false');
	}

	public function admin_register_default_image_link_to()
	{
		add_action('after_setup_theme', function () {
			update_option('image_default_link_type', 'none');
		});
	}


	public static function register_login_url()
	{
		add_filter('login_headerurl', function () {
			return home_url('/');
		});
	}

	protected function _add_theme_options()
	{
		$option_box = new OptionBox('general', __('General Options', THEME_TEXT_DOMAIN));
		$option_box->add_field('ga_code', __('Google Analytics', THEME_TEXT_DOMAIN), 'Textarea');
		$option_box->set_page('theme');
	}

	protected function _add_social_options()
	{
		$option_box = new OptionBox(self::OPTIONS_PAGE_SOCIAL . '_links', __('Social Links', THEME_TEXT_DOMAIN));
		$option_box->add_field('facebook', __('Facebook', THEME_TEXT_DOMAIN), 'Url');
		$option_box->set_page(self::OPTIONS_PAGE_SOCIAL);
	}
}

