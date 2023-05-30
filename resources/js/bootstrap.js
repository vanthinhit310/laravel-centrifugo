/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

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
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
import { Centrifuge } from "centrifuge";

const getToken = (url, ctx) => {
    return new Promise((resolve, reject) => {
        fetch(url, {
            method: "POST",
            headers: new Headers({ "Content-Type": "application/json" }),
            body: JSON.stringify(ctx),
        })
            .then((res) => {
                if (!res.ok) {
                    throw new Error(`Unexpected status code ${res.status}`);
                }
                return res.json();
            })
            .then((data) => {
                resolve(data.token);
            })
            .catch((err) => {
                reject(err);
            });
    });
};

window.CENTRIFUGE_INSTANCE = new Centrifuge(
    "ws://localhost:8002/connection/websocket",
    {
        getToken: function (ctx) {
            return getToken(route("centrifuge.genConnectionToken"), {
                _token: document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            });
        },
    }
);

CENTRIFUGE_INSTANCE.on("connecting", function (ctx) {
    console.log(`connecting: ${ctx.code}, ${ctx.reason}`);
})
    .on("connected", function (ctx) {
        console.log(`connected over ${ctx.transport}`);
    })
    .on("disconnected", function (ctx) {
        console.log(`disconnected: ${ctx.code}, ${ctx.reason}`);
    })
    .connect();
