import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import { registerReactControllerComponents } from '@symfony/ux-react';
registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/));

// any CSS you import will output into a single css file (app.css in this case)
<<<<<<< ours
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// the FontAwesome library
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
=======
import './styles/app.css';
>>>>>>> theirs
