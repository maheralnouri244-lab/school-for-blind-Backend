importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyCZOOIISH9H0kSMHUqKz8S-hhScOlcjRDQ",
  authDomain: "schoolforblindapp.firebaseapp.com",
  projectId: "schoolforblindapp",
  storageBucket: "schoolforblindapp.firebasestorage.app",
  messagingSenderId: "992430713424",
  appId: "1:992430713424:web:3058ec50a1d8e525343f55",
  measurementId: "G-NQF1G3XQ4W"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);

  const notificationTitle = payload.notification?.title || 'إشعار جديد';
  const notificationOptions = {
    body: payload.notification?.body || '',
    icon: '/favicon.ico', 
    badge: '/favicon.ico', 
    data: payload.data 
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const clickAction = event.notification.data?.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      for (let client of windowClients) {
        if (client.url === clickAction && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(clickAction);
      }
    })
  );
});