<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            background-image: url('login.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        img {
            width: 130px;
            margin-top: 60px;
            margin-left: 50px;
        }

        .use {
            position: relative;
            margin-bottom: 20px;
        }

        .use input {
            width: 310px;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
            font-size: 13px;
            background: transparent;
            color: white;
            transition: border-color 0.3s, transform 0.2s;
        }

        .use input:focus {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .forgot-password {
            color: grey;
            margin-left: 170px;
            position: absolute;
        }

        .forgot-password a {
            text-decoration: none;
            color: #007bff;
        }

        .login-button {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-left: 150px;
            margin-top: 30px;
            border-radius: 5px;
        }

        .login-button:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
            box-shadow: 0px 0px 20px rgba(255, 75, 43, 0.8);
            transform: scale(1.1);
        }

        .part1 {
            margin-left: 890px;
            margin-top: 70px;
            width: 500px;
            height: 500px;
            background: transparent;
            box-shadow: 0 .1875rem .4375rem 0 rgba(0, 0, 0, .13), 0 .0625rem .125rem 0 rgba(0, 0, 0, .11);
            padding: 20px;
            border-radius: 10px;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h3 {
            margin-left: 150px;
            margin-top: 40px;
            font-size: 26px;
            color: #ccc;
            transition: color 0.3s;
        }

        h3:hover {
            color: #007bff;
        }

        /* Custom Popup Styles */
        .custom-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .custom-popup.error {
            background: rgba(231, 76, 60, 0.9); /* Red for errors */
        }

        .custom-popup .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #ccc;
        }

        .custom-popup .close-btn:hover {
            color: #fff;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="part1">
        <h3>Login In</h3>
        <form action="loginprogress.php" method="POST">
            <div class="use">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="use">
                <input id="password" name="password" type="password" placeholder="Password" required>
            </div>
            <p style="color: white; margin-left: 60px; margin-top: 50px;">
                No Account? <a href="signup.php">Create One!</a>
            </p>
            <button class="login-button">LOGIN</button>
        </form>
    </div>

    <!-- Custom Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="custom-popup" id="successPopup">
        <span class="close-btn" onclick="closePopup()">×</span>
        <h4>Success!</h4>
        <p></p> <!-- Message will be set dynamically -->
    </div>

    <script>
        function showPopup(message, isError = false) {
            const popup = document.getElementById('successPopup');
            const overlay = document.getElementById('overlay');
            if (!popup || !overlay) return;
            const p = popup.querySelector('p');
            p.textContent = message;
            popup.className = 'custom-popup' + (isError ? ' error' : '');
            popup.style.display = 'block';
            overlay.style.display = 'block';
            setTimeout(() => {
                closePopup(isError ? 'login.php' : 'dashboard.php');
            }, 3000); // Redirect after 3 seconds
        }

        function closePopup(redirectUrl = 'dashboard.php') {
            const popup = document.getElementById('successPopup');
            const overlay = document.getElementById('overlay');
            if (popup && overlay) {
                popup.style.display = 'none';
                overlay.style.display = 'none';
                window.location.href = redirectUrl;
            }
        }

        // Check for success or error messages
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            const message = urlParams.get('message');
            if (success === 'true' && message) {
                showPopup(decodeURIComponent(message), false);
            } else if (error === 'true' && message) {
                showPopup(decodeURIComponent(message), true);
            }
        });
    </script>
</body>
</html>