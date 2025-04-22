<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            background-image: url('login.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial, sans-serif;
            color: white;
        }
        .container {
            margin-left: 890px;
            margin-top: 70px;
            width: 500px;
            height: 550px;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 .1875rem .4375rem 0 rgba(0, 0, 0, .13), 0 .0625rem .125rem 0 rgba(0, 0, 0, .11);
            padding: 20px;
            border-radius: 10px;
        }
        h3 {
            text-align: center;
            font-size: 26px;
        }
        .input-group {
            margin: 20px 60px;
            display: flex;
            flex-direction: column;
        }
        input {
            width: 85%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
            background: transparent;
            color: white;
            font-size: 14px;
        }
        .login-button {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            cursor: pointer;
            transition: 0.3s;
            margin: 30px auto;
            display: block;
        }
        .login-button:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
            box-shadow: 0px 0px 20px rgba(255, 75, 43, 0.8);
            border: none;
        }
        .login-link {
            text-align: center;
        }
        .login-link a {
            text-decoration: none;
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
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
    <div class="container">
        <h3>Sign Up</h3>
        <form action="signupprogress.php" method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Full Name" required>
            </div>
            <div class="input-group">
                <input type="text" name="phoneno" placeholder="Phone Number" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="login-button">Sign Up</button>
        </form>
        <p class="login-link">Have an account? <a href="login.php">Login in!</a></p>
    </div>

    <!-- Custom Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="custom-popup" id="successPopup">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <h4>Success!</h4>
        <p>Registration successful! Redirecting to login...</p>
    </div>

    <script>
        function showPopup(message) {
            const popup = document.getElementById('successPopup');
            const overlay = document.getElementById('overlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
            setTimeout(() => {
                closePopup();
                window.location.href = 'login.php';
            }, 3000); // Redirect after 3 seconds
        }

        function closePopup() {
            const popup = document.getElementById('successPopup');
            const overlay = document.getElementById('overlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }

        // Check for success message from signupprogress.php
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 'true') {
            echo "showPopup('Registration successful! Redirecting to login...');";
        }
        ?>
    </script>
</body>
</html>