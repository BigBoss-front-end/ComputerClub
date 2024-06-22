import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
tippy('[data-tippy-content]');

import moment from 'moment';
import { tz } from 'moment-timezone';
window.moment = moment;
moment.tz.setDefault('Asia/Yekaterinburg')

import _ from 'lodash';
window._ = _;

import $ from 'jquery';
window.$ = $;

import 'remodal/dist/remodal.css'
import 'remodal/dist/remodal-default-theme.css'
import 'remodal';

import './libs/tailwind';
import './libs/phoneinput';

import '../css/app.scss'

import axios from 'axios';
window.axios = axios;

import 'izitoast/dist/css/iziToast.min.css';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
