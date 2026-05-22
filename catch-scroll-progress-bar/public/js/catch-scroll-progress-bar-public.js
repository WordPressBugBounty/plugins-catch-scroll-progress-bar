(function( $ ) {
	'use strict';

	jQuery(function($) {
		var $bar = $('.catchProgressbar');

		// Calculate and set the progress bar's maximum value.
		function updateMax() {
			$bar.attr('max', $(document).height() - $(window).height());
		}

		// Update the current progress value based on scroll position.
		function updateValue() {
			$bar.attr('value', $(window).scrollTop());
		}

		// Set values immediately on DOM ready.
		updateMax();
		updateValue();

		// Recalculate max once all resources (images, iframes) are loaded
		// for a more accurate document height — avoids the missed-event
		// problem of the old $(window).load() by initialising above first.
		$(window).on('load', updateMax);

		// Recalculate max when the viewport is resized.
		$(window).on('resize', updateMax);

		// Update progress value on every scroll.
		$(document).on('scroll', updateValue);
	});

})( jQuery );
