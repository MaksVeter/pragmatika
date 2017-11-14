import $ from 'jquery';
import detectTouchSupport from "./modules/detectTouchSupport";
import gallery from "./modules/gallery";
$( function ( $ ) {
    detectTouchSupport();
    gallery();
} );

