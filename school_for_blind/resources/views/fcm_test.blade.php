<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Get FCM Token</title>
    <style>
        body {
            background-color: #0b132b; 
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 40px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        #getTokenBtn {
            background-color: #39ff14; 
            color: #000000;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 0 15px #39ff14, 0 0 30px #39ff14; 
            transition: all 0.3s ease;
        }

        #getTokenBtn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px #39ff14, 0 0 50px #39ff14;
        }

        #tokenDisplay {
            margin-top: 40px;
            background-color: #1c2541; 
            color: #39ff14; 
            border: 1px solid #39ff14;
            border-radius: 8px;
            padding: 20px;
            font-size: 15px;
            line-height: 1.5;
            resize: none;
            outline: none;
            width: 80%;
            max-width: 600px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
        }

        #tokenDisplay::placeholder {
            color: #8b9bb4;
        }
    </style>
</head>
<body>

    <h2>توليد Firebase Token للاختبار</h2>
    
    <button id="getTokenBtn">جلب التوكن</button>
    
    <textarea id="tokenDisplay" rows="6" placeholder="سيظهر التوكن هنا..." readonly></textarea>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCZOOIISH9H0kSMHUqKz8S-hhScOlcjRDQ",
            authDomain: "schoolforblindapp.firebaseapp.com",
            projectId: "schoolforblindapp",
            storageBucket: "schoolforblindapp.firebasestorage.app",
            messagingSenderId: "992430713424",
            appId: "1:992430713424:web:3058ec50a1d8e525343f55",
            measurementId: "G-NQF1G3XQ4W"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        onMessage(messaging, (payload) => {
            console.log('إشعار واصل والشاشة مفتوحة:', payload);

            const title = payload.notification?.title || 'تنبيه جديد';
            const body = payload.notification?.body || '';

            alert(`📢 ${title}\n${body}`);

            if (Notification.permission === 'granted') {
                new Notification(title, {
                    body: body,
                    icon: '/favicon.ico'
                });
            }
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);
                })
                .catch((err) => {
                    console.error('Service Worker registration failed:', err);
                });
        }

        document.getElementById('getTokenBtn').addEventListener('click', async () => {
            try {
                if (!('Notification' in window)) {
                    alert('هذا المتصفح لا يدعم الإشعارات. افتح الرابط بمتصفح Chrome أو Safari.');
                    return;
                }

                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    const registration = await navigator.serviceWorker.ready;
                    const currentToken = await getToken(messaging, { 
                        vapidKey: 'BEjjdXidw3mYHsFpGwfB7uROBgwL3XzEqzJ-bE9XVGPpV5sq4abmrHmE3lez194MzVzYBHSwbYb3-0Vfb7u25S0',
                        serviceWorkerRegistration: registration 
                    });

                    if (currentToken) {
                        document.getElementById('tokenDisplay').value = currentToken;
                    } else {
                        alert('لم يتم العثور على توكن.');
                    }
                } else {
                    alert('تم رفض إذن الإشعارات من المتصفح.');
                }
            } catch (err) {
                console.error('حدث خطأ أثناء جلب التوكن:', err);
                alert('حدث خطأ: ' + err.message);
            }
        });
    </script>
</body>
</html>