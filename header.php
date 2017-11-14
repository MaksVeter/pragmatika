<?php
/**
 *
 * @package    skeleton
 * @author     vitaly
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( "charset" ) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <?php wp_head() ?>
</head>
<body id="body" <?php body_class( 'with-hovers' ) ?>>
<div class="global-wrapper">
    <div class="svg-sprite" style="display: none">
		<?php get_template_part( 'sprite' ) ?>
    </div>
    <header id="header">
        <div class="wrapper">
			<p>header</p>
        </div>
    </header>
    <section class="main-wrapper">