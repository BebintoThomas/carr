<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection to verify booking
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "carren";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    header("Location: dashbord.php?error=" . urlencode("Database connection error."));
    exit;
}

// Get and sanitize URL parameters
$booking_id = filter_input(INPUT_GET, 'booking_id', FILTER_VALIDATE_INT) ?: 0;
$car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT) ?: 0;
$total_amount = filter_input(INPUT_GET, 'total_amount', FILTER_VALIDATE_FLOAT) ?: 0;
$advance_amount = $total_amount > 0 ? $total_amount / 2 : 0;

// Verify booking belongs to the user
$check_sql = "SELECT COUNT(*) FROM bookings WHERE booking_id = ? AND car_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    $conn->close();
    header("Location: dashbord.php?error=" . urlencode("Database query error."));
    exit;
}
$check_stmt->bind_param("iii", $booking_id, $car_id, $_SESSION['user_id']);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count == 0 || $booking_id <= 0 || $car_id <= 0 || $total_amount <= 0) {
    $conn->close();
    header("Location: dashbord.php?error=" . urlencode("Invalid booking or car details."));
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Luxury Car Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0a0a0a;
            overflow: hidden;
            position: relative;
        }

        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .payment-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 50px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            color: #fff;
            position: relative;
            z-index: 2;
            animation: slideIn 1s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .payment-box h2 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .location {
            font-size: 14px;
            font-weight: 400;
            color: #ccc;
            text-align: center;
            margin-bottom: 20px;
        }

        .amount {
            font-size: 40px;
            font-weight: 700;
            color: #ffcc00;
            text-align: center;
            margin: 25px 0;
            letter-spacing: 1px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            margin: 12px 0;
            font-size: 16px;
            font-weight: 500;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.3);
        }

        .btn-upi {
            background: linear-gradient(135deg, #27ae60, #219653);
            color: #fff;
        }

        .btn-card {
            background: linear-gradient(135deg, #3498db, #2e86c1);
            color: #fff;
        }

        .btn-home {
            background: linear-gradient(135deg, #666, #555);
            color: #fff;
        }

        .form-container {
            display: none;
            margin-top: 25px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-container label {
            font-size: 14px;
            font-weight: 500;
            display: block;
            margin: 12px 0 5px;
            color: #fff;
        }

        .form-container input {
            width: 100%;
            padding: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-container input:focus {
            outline: none;
            border-color: #ffcc00;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3);
        }

        .form-container input::placeholder {
            color: #ccc;
            transition: all 0.3s ease;
        }

        .form-container input:focus::placeholder {
            color: transparent;
        }

        .form-container .error {
            border-color: #e74c3c;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message {
            display: none;
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
        }

        .pay-button {
            display: none;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #e91e63, #c2185b);
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            margin-top: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pay-button:hover {
            background: linear-gradient(135deg, #d81b60, #b01751);
            transform: translateY(-3px);
        }

        .pay-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.3);
        }

        .pay-button:disabled {
            background: #666;
            cursor: not-allowed;
        }

        .pay-button.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid #fff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .custom-alert {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: slideDown 0.5s ease-in-out;
        }

        .custom-alert.error {
            background: rgba(231, 76, 60, 0.9);
            color: #fff;
        }

        .custom-alert.success {
            background: rgba(39, 174, 96, 0.9);
            color: #fff;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translate(-50%, -50px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }

        /* Success Popup */
        .success-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            z-index: 2000;
            animation: popIn 0.5s ease-out;
            max-width: 400px;
            width: 90%;
        }

        .success-popup h3 {
            font-size: 24px;
            font-weight: 600;
            color: #27ae60;
            margin-bottom: 15px;
        }

        .success-popup p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .success-popup .btn-home {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #fff;
            padding: 12px 24px;
            width: auto;
            display: inline-block;
        }

        @keyframes popIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1500;
        }

        @media (max-width: 480px) {
            .payment-box {
                padding: 30px;
                margin: 20px;
            }

            .payment-box h2 {
                font-size: 28px;
            }

            .location {
                font-size: 12px;
            }

            .amount {
                font-size: 32px;
            }

            .btn, .pay-button {
                padding: 14px;
                font-size: 15px;
            }

            .success-popup {
                padding: 30px;
            }

            .success-popup h3 {
                font-size: 20px;
            }

            .success-popup p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<canvas class="particles" id="particles"></canvas>

<div class="payment-box">
    <div class="location">RentalX, Thavukunnu, Vayattuparamba, Kannur, Kerala</div>
    <h2>Advance Payment</h2>
    <div class="amount" id="advance-amount">₹<?php echo number_format($advance_amount, 2); ?></div>

    <form action="paymentprogress.php" method="POST" id="payment-form" onsubmit="return handleSubmit(event)">
        <button type="button" class="btn btn-upi" onclick="showForm('upi')">Pay with UPI</button>
        <button type="button" class="btn btn-card" onclick="showForm('card')">Pay with Card</button>

        <!-- UPI Form -->
        <div class="form-container" id="upi-form">
            <label for="upi-id">UPI ID</label>
            <input type="text" name="upi_id" id="upi-id" placeholder="example@upi" oninput="validateUpi(this)" />
            <div class="error-message" id="upi-error">Please enter a valid UPI ID.</div>
        </div>

        <!-- Card Form -->
        <div class="form-container" id="card-form">
            <label for="card-number">Card Number</label>
            <input type="text" name="card_number" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCardNumber(this)" />
            <div class="error-message" id="card-number-error">Please enter a valid 16-digit card number.</div>
            <label for="expiry">Expiry Date (MM/YY)</label>
            <input type="text" name="card_expiry" id="expiry" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)" />
            <div class="error-message" id="expiry-error">Please enter a valid expiry date.</div>
            <label for="cvv">CVV</label>
            <input type="password" name="cvv" id="cvv" placeholder="123" maxlength="3" oninput="validateCvv(this)" />
            <div class="error-message" id="cvv-error">Please enter a valid 3-digit CVV.</div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="payment_method" id="payment-method" />
        <input type="hidden" name="amount_paid" id="amount-paid" value="<?php echo $advance_amount; ?>" />
        <input type="hidden" name="car_id" id="car-id" value="<?php echo $car_id; ?>" />
        <input type="hidden" name="booking_id" id="booking-id" value="<?php echo $booking_id; ?>" />
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />

        <button type="submit" class="pay-button" id="pay-now">Pay Now</button>
    </form>

    <button type="button" class="btn btn-home" onclick="window.location.href='dashbord.php'">Back to Home</button>
</div>

<div class="overlay" id="overlay"></div>
<div class="success-popup" id="success-popup">
    <h3>Payment Successful!</h3>
    <p>Your payment of ₹<?php echo number_format($advance_amount, 2); ?> has been processed successfully.</p>
    <button class="btn btn-home" onclick="window.location.href='dashbord.php'">Return to Home</button>
</div>

<div class="custom-alert" id="custom-alert"></div>

<script>
// Particles Background
function initParticles() {
    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particlesArray = [];
    const numberOfParticles = 30;

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 2 + 1;
            this.speedX = Math.random() * 0.3 - 0.15;
            this.speedY = Math.random() * 0.3 - 0.15;
        }

        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.size > 0.2) this.size -= 0.01;
        }

        draw() {
            ctx.fillStyle = 'rgba(255, 204, 0, 0.4)';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function handleParticles() {
        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
            particlesArray[i].draw();
            if (particlesArray[i].size <= 0.2) {
                particlesArray.splice(i, 1);
                i--;
                particlesArray.push(new Particle());
            }
        }
    }

    for (let i = 0; i < numberOfParticles; i++) {
        particlesArray.push(new Particle());
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        handleParticles();
        requestAnimationFrame(animate);
    }

    animate();

    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

initParticles();

// Form Handling
function showForm(method) {
    const upiForm = document.getElementById('upi-form');
    const cardForm = document.getElementById('card-form');
    const payButton = document.getElementById('pay-now');
    const methodField = document.getElementById('payment-method');

    upiForm.style.display = 'none';
    cardForm.style.display = 'none';
    payButton.style.display = 'none';

    if (method === 'upi') {
        upiForm.style.display = 'block';
        payButton.style.display = 'block';
        methodField.value = 'UPI';
    } else if (method === 'card') {
        cardForm.style.display = 'block';
        payButton.style.display = 'block';
        methodField.value = 'Card';
    }

    clearErrors();
}

function clearErrors() {
    document.querySelectorAll('.error').forEach(input => input.classList.remove('error'));
    document.querySelectorAll('.error-message').forEach(msg => msg.style.display = 'none');
}

function showError(id, message) {
    const input = document.getElementById(id);
    const errorMsg = document.getElementById(`${id}-error`);
    input.classList.add('error');
    errorMsg.textContent = message;
    errorMsg.style.display = 'block';
}

function validateUpi(input) {
    const value = input.value.trim();
    const upiRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+$/;
    if (!value) {
        showError('upi-id', 'UPI ID cannot be empty.');
    } else if (!upiRegex.test(value)) {
        showError('upi-id', 'Please enter a valid UPI ID.');
    } else {
        input.classList.remove('error');
        document.getElementById('upi-error').style.display = 'none';
    }
}

function formatCardNumber(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.match(/.{1,4}/g)?.join(' ') || value;
    input.value = value;
    if (!value) {
        showError('card-number', 'Card number cannot be empty.');
    } else if (value.replace(/\s/g, '').length !== 16) {
        showError('card-number', 'Please enter a valid 16-digit card number.');
    } else {
        input.classList.remove('error');
        document.getElementById('card-number-error').style.display = 'none';
    }
}

function formatExpiry(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 3) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    input.value = value;
    const expiryRegex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
    if (!value) {
        showError('expiry', 'Expiry date cannot be empty.');
    } else if (!expiryRegex.test(value)) {
        showError('expiry', 'Please enter a valid expiry date (MM/YY).');
    } else {
        input.classList.remove('error');
        document.getElementById('expiry-error').style.display = 'none';
    }
}

function validateCvv(input) {
    const value = input.value.trim();
    if (!value) {
        showError('cvv', 'CVV cannot be empty.');
    } else if (value.length !== 3 || isNaN(value)) {
        showError('cvv', 'Please enter a valid 3-digit CVV.');
    } else {
        input.classList.remove('error');
        document.getElementById('cvv-error').style.display = 'none';
    }
}

function showAlert(message, type = 'error') {
    const alert = document.getElementById('custom-alert');
    alert.textContent = message;
    alert.className = `custom-alert ${type}`;
    alert.style.display = 'block';
    setTimeout(() => {
        alert.style.display = 'none';
    }, 3000);
}

function showSuccessPopup() {
    const popup = document.getElementById('success-popup');
    const overlay = document.getElementById('overlay');
    popup.style.display = 'block';
    overlay.style.display = 'block';
}

function validateForm(paymentMethod, inputs) {
    clearErrors();
    let isValid = true;

    if (!paymentMethod) {
        showAlert('Please select a payment method.');
        return false;
    }

    if (paymentMethod === 'UPI') {
        const upiId = inputs.upiId.trim();
        const upiRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+$/;
        if (!upiId) {
            showError('upi-id', 'UPI ID cannot be empty.');
            isValid = false;
        } else if (!upiRegex.test(upiId)) {
            showError('upi-id', 'Please enter a valid UPI ID.');
            isValid = false;
        }
    } else if (paymentMethod === 'Card') {
        const cardNumber = inputs.cardNumber.replace(/\s/g, '');
        const expiry = inputs.expiry;
        const cvv = inputs.cvv.trim();

        if (!cardNumber) {
            showError('card-number', 'Card number cannot be empty.');
            isValid = false;
        } else if (cardNumber.length !== 16) {
            showError('card-number', 'Please enter a valid 16-digit card number.');
            isValid = false;
        }

        if (!expiry) {
            showError('expiry', 'Expiry date cannot be empty.');
            isValid = false;
        } else if (!/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(expiry)) {
            showError('expiry', 'Please enter a valid expiry date (MM/YY).');
            isValid = false;
        }

        if (!cvv) {
            showError('cvv', 'CVV cannot be empty.');
            isValid = false;
        } else if (cvv.length !== 3 || isNaN(cvv)) {
            showError('cvv', 'Please enter a valid 3-digit CVV.');
            isValid = false;
        }
    }

    return isValid;
}

function handleSubmit(event) {
    event.preventDefault();
    const paymentMethod = document.getElementById('payment-method').value;
    const inputs = {
        upiId: document.getElementById('upi-id').value,
        cardNumber: document.getElementById('card-number').value,
        expiry: document.getElementById('expiry').value,
        cvv: document.getElementById('cvv').value
    };
    const payButton = document.getElementById('pay-now');

    if (!validateForm(paymentMethod, inputs)) {
        return false;
    }

    // Simulate payment processing
    payButton.disabled = true;
    payButton.classList.add('loading');
    payButton.textContent = 'Processing...';

    setTimeout(() => {
        payButton.classList.remove('loading');
        payButton.textContent = 'Pay Now';
        payButton.disabled = false;
        document.getElementById('payment-form').submit();
        showSuccessPopup();
    }, 1500);

    return false;
}
</script>

</body>
</html>