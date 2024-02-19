/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import $ from "jquery";
window.$ = $;

// Popper.js
import { createPopper } from '@popperjs/core';
window.Popper = createPopper;

// Bootstrap
import * as bootstrap from 'bootstrap'

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/*
 |-------------------------------------
 |
 | Load the dependencies
 |
 |-------------------------------------
 */

// import 'dropzone';
import Swal from 'sweetalert2/dist/sweetalert2.js'

/*
 |-------------------------------------
 |
 | Custom Features for Oxygen
 |
 |-------------------------------------
 */


/*
 |-------------------------------------
 | Setup Global Object
 |-------------------------------------
 */
// Responsive Breakpoints
// Small    < 768px
// Medium   >= 768px & <= 992px
// Big      > 992px && <= 1200px
// Xl       > 1200px

// setup base objects
window._oxygen = window._oxygen || {};

// set defaults
if (window._oxygen.sidebar_status === undefined) {
	// set version - only use integers. Increase for breaking changes
	window._oxygen.version = 1;

	// store visibility
	window._oxygen.sidebar_visible = true;

	// type of sidebar
	window._oxygen.sidebar_status = 'normal';
}

// define functions
if (window._oxygen.fn === undefined) {
	window._oxygen.fn = {

		/*
		 |-----------------------------------------------------------
		 | Save Local State in localStorage
		 |-----------------------------------------------------------
		 */
		saveLocalState() {
			if (!localStorage) {
				return false;
			}

			let data = Object.assign({}, window._oxygen);

			// remove functions, because we only want to save the data
			delete(data.fn);

			localStorage.setItem('_oxygen', JSON.stringify(data));

			// console.log('saveLocalState()', data);
		},

		/*
		 |-----------------------------------------------------------
		 | Reload state from localStorage (if any)
		 |-----------------------------------------------------------
		 */
		loadLocalState() {
			if (!localStorage) {
				return false;
			}

			if (!localStorage.hasOwnProperty('_oxygen')) {
				return false;
			}

			let data = JSON.parse(localStorage.getItem('_oxygen'));
			if (!data.hasOwnProperty('version')) {
				return false;
			}

			// copy the values
			for (let key in data) {
				window._oxygen[key] = data[key];
			}

			window._oxygen.fn.refreshSidebar();
			// console.log('loadLocalState()', window._oxygen);
		},


		/*
		 |-----------------------------------------------------------
		 | Show the correct navigation arrows
		 |-----------------------------------------------------------
		 */
		// show Right arrow
		showRightArrow() {
			$('.js-toggle-right-mini-sidebar .js-icon').removeClass('point-left').addClass('point-right');
		},

		// show Left arrow
		showLeftArrow() {
			$('.js-toggle-right-mini-sidebar .js-icon').removeClass('point-right').addClass('point-left');
		},

		// update the arrow direction
		updateArrowDirection() {
			if (window._oxygen.sidebar_visible === false) {
				window._oxygen.fn.showRightArrow();
			} else if (window._oxygen.sidebar_status === 'mini') {
				window._oxygen.fn.showRightArrow();
			} else if (window._oxygen.sidebar_status === 'normal') {
				window._oxygen.fn.showLeftArrow();
			}
		},

		/*
		 |-----------------------------------------------------------
		 | Refresh Sidebars (usually after loading page)
		 |-----------------------------------------------------------
		 */
		refreshSidebar() {
			if (window._oxygen.sidebar_status === 'mini') {
				$('#sidebar').removeClass('normal-sidebar').addClass('mini-sidebar');
				// by default, the `normal` sidebar will be displayed
				// } else if (window._oxygen.sidebar_status === 'normal') {
				//	$('#sidebar').removeClass('mini-sidebar').addClass('normal-sidebar');
			}

			// by default, the sidebar will be open, so no need to update that
			if (!window._oxygen.sidebar_visible) {
				$('#sidebar').addClass('d-none');
			}

			window._oxygen.fn.updateArrowDirection();
		},

		// show mini sidebar
		showMiniSidebar: function (moveSidebar = true) {
			if (moveSidebar) {
				$('#sidebar').removeClass('normal-sidebar').addClass('mini-sidebar');
			}
			window._oxygen.fn.showRightArrow();
			window._oxygen.sidebar_status = 'mini';
			window._oxygen.sidebar_visible = true;
		},

		// show normal sidebar
		showNormalSidebar: function (moveSidebar = true) {
			if (moveSidebar) {
				$('#sidebar').addClass('normal-sidebar').removeClass('d-none').removeClass('mini-sidebar');
			}
			window._oxygen.fn.showLeftArrow();
			window._oxygen.sidebar_status = 'normal';
			window._oxygen.sidebar_visible = true;
		},

		/*
		 |-----------------------------------------------------------
		 | Handle Button Clicks
		 |-----------------------------------------------------------
		 */
		// show/hide sidebar
		toggleSidebarVisibility() {
			if (window._oxygen.sidebar_visible) {
				// when visible, hide it
				$('#sidebar').addClass('d-none');
				window._oxygen.sidebar_visible = false;
			} else {
				// when hidden, reveal it
				$('#sidebar').removeClass('d-none');
				window._oxygen.sidebar_visible = true;
			}

			window._oxygen.fn.updateArrowDirection();
			window._oxygen.fn.saveLocalState();
		},

		// toggle sidebar type
		toggleSidebar: function () {

			// if hidden, expand will make it normal
			// let $el = $('#sidebar');
			// force a DOM reflow, so the animations can be restarted
			// without a reflow the animations won't show.
			// $el.outerWidth($el.outerWidth);

			// if it's not visible, show it first
			if (!window._oxygen.sidebar_visible) {
				window._oxygen.fn.toggleSidebarVisibility();
			}

			if (window._oxygen.sidebar_status === 'normal') {
				window._oxygen.fn.showMiniSidebar();
			} else if (window._oxygen.sidebar_status === 'mini') {
				window._oxygen.fn.showNormalSidebar();
			} else {
				// console.log('Unknown status');
				// console.log(window._oxygen.sidebar_status);
			}

			window._oxygen.fn.saveLocalState();
		}
	};
}

/*
 |-----------------------------------------------------------
 | Start jQuery Calls
 |-----------------------------------------------------------
 */
$(document).ready(function () {

	// load state parameters
	window._oxygen.fn.loadLocalState();

	// confirmation
	$('.js-confirm').on('submit', function (e) {
		e.preventDefault();
		let form = this;

		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to undo this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#e74c3c',
			cancelButtonColor: '#2ecc71',
			confirmButtonText: 'Yes, I\'m Sure',
			focusCancel: true,
		}).then((result) => {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});

	// delete confirmation
	$('.js-confirm-delete').on('submit', function (e) {
		e.preventDefault();
		let form = this;

		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to undo this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#e74c3c',
			cancelButtonColor: '#2ecc71',
			confirmButtonText: 'Yes, delete it!',
			focusCancel: true,
		}).then((result) => {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});

	$('.js-confirm-generic').on('submit', function (e) {
		return confirm('Are you sure?');
	});

	// trigger tooltips
	let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	// collapse sidebar
	$('.js-toggle-right-mini-sidebar').on('click', function (e) {
		window._oxygen.fn.toggleSidebar();
	});

	// set sidebar button arrows
	$('.js-toggle-right-sidebar').on('click', function () {
		window._oxygen.fn.toggleSidebarVisibility();
	});
});
