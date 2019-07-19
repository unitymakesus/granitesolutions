// Import parent JS
import '../../../../unity-core/dist/scripts/main.js';

// Core Materialize JS
import 'materialize-css/js/cash.js';
import 'materialize-css/js/component.js';
import 'materialize-css/js/global.js';
import 'materialize-css/js/anime.min.js';

// Materialize form interactions
import 'materialize-css/js/forms.js';

/** Import local dependencies */
import Router from './util/Router';
import common from './routes/common';
// import home from './routes/home';
// import aboutUs from './routes/about';
// import archive from './routes/archive';

/** Populate Router instance with DOM routes */
const routes = new Router({
  common,
});

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());
