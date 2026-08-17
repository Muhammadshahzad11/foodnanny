importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyBINiUJUOYBSKvXaItoUAVTT-zBYtmn934",
        authDomain: "ctocfoods-6aa0c.firebaseapp.com",
        projectId: "ctocfoods-6aa0c",
        storageBucket: "ctocfoods-6aa0c.firebasestorage.app",
        messagingSenderId: "73684103673",
        appId: "1:73684103673:web:176a1f8d9f0cb418ec0d28",
        measurementId: "G-VLML26K20",
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
