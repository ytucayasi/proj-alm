// Importar jQuery
import jQuery from 'jquery';
window.$ = jQuery;

// Importar select2
import select2 from 'select2';
select2();

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
