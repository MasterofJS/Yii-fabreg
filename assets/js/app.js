global.jQuery = require('jquery');
global.$ = jQuery;
//window.jQuery = jQuery;
//window.$ = jQuery;

import attachFastClick from 'fastclick';
import '../../bower_components/foundation/js/foundation/foundation.js';
import '../../bower_components/foundation/js/foundation/foundation.dropdown.js';
import '../../bower_components/foundation/js/foundation/foundation.abide.js';
global.Foundation = Foundation;

$(function () {

    $(document).foundation();

    attachFastClick(document.body);

});









