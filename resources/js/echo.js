import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from "axios";
import ENV from "./config/env.js";

const PUSHER_KEY     = ENV.PUSHER_KEY;
const PUSHER_CLUSTER = ENV.PUSHER_CLUSTER;


if (PUSHER_KEY && PUSHER_CLUSTER) {
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: PUSHER_KEY,
        cluster: PUSHER_CLUSTER,
        forceTLS: true,
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    }, {
                        withCredentials: true
                    }).then(response => {
                        callback(false, response.data);
                    }).catch(error => {
                        callback(true, error);
                    });
                }
            };
        }
    });
} else {
    if (import.meta.env.VITE_DEMO) {
        console.warn("Pusher key and/or cluster are not defined in the .env file. Echo was not initialized.");
    }
}
