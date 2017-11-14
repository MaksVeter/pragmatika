import $ from 'jquery';
export default () => {
	let touchSupport = (('ontouchstart' in window) || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));
	if(touchSupport) {
	    $("body").removeClass('with-hovers');
	}
};