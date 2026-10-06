import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// moment must be a global BEFORE daterangepicker is imported. daterangepicker is a
// UMD module whose factory runs at import time and reads window.moment; if it is
// still undefined it falls back to require('moment'), which under Vite's CJS interop
// yields a namespace object ({ default: fn }) and throws "moment is not a function".
// bootstrap.js is app.js's first import, so this runs before daterangepicker evaluates.
import moment from 'moment';
window.moment = moment;

// Bootstrap 5 JS bundle (Popper is pulled in automatically)
import 'bootstrap';

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';