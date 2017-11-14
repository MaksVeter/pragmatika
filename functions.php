<?php
/**
 * For translating admin panel need to call this function before requiring of any module
 */
const THEME_TEXT_DOMAIN = '';
load_theme_textdomain( THEME_TEXT_DOMAIN, get_template_directory() . '/languages' );

require_once ABSPATH . 'WPKit/init_autoloader.php';
$loader = new \WPKit\Module\Loader();
$loader->load_modules();