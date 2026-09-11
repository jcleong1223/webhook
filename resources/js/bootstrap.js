import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Bootstrap 5 JS bundle (Popper is pulled in automatically)
import 'bootstrap';

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';