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
