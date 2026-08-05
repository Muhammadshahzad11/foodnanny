importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyAEotcEUmJoyZlBJzpsA1c3gZw2bsVoE08",
        authDomain: "foodnanny-27d2c.firebaseapp.com",
        projectId: "foodnanny-27d2c",
        storageBucket: "foodnanny-27d2c.firebasestorage.app",
        messagingSenderId: "24639567330",
        appId: "1:24639567330:web:d535c6ed8bf0dcf2d6b5bb",
        measurementId: "G-K95FZ49B6E",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/default/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
