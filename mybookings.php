<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "carren";
$port = 3307; // Adjust to 3306 if your MySQL uses the default port

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    header("Location: dashbord.php?error=" . urlencode("Database connection error."));
    exit;
}

// Fetch bookings for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.booking_id, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.total_amount, b.booking_status, c.car_name 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.car_id 
        WHERE b.user_id = ? AND b.booking_status != 'Cancelled'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error); // Log the exact error
    $conn->close();
    header("Location: dashbord.php?error=" . urlencode("Database query error."));
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Bookings - RentalX</title>
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
            background: #0a0a0a;
            color: #fff;
            padding: 40px;
            position: relative;
            overflow-x: hidden;
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 36px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            color: #ffcc00;
        }

        .booking-list {
            display: grid;
            gap: 20px;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .booking-card h3 {
            font-size: 22px;
            font-weight: 500;
            color: #ffcc00;
            margin-bottom: 10px;
        }

        .booking-card p {
            font-size: 16px;
            margin: 5px 0;
            color: #ccc;
        }

        .booking-card .status {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .booking-card .status.pending {
            background: rgba(255, 204, 0, 0.2);
            color: #ffcc00;
        }

        .booking-card .status.completed {
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 15px;
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }

        .btn-back {
            background: linear-gradient(135deg, #666, #555);
            color: #fff;
            display: block;
            width: 200px;
            margin: 30px auto 0;
            text-align: center;
        }

        .no-bookings {
            text-align: center;
            font-size: 18px;
            color: #ccc;
        }

        /* Confirmation Popup */
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

        .confirm-popup {
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

        .confirm-popup h3 {
            font-size: 24px;
            font-weight: 600;
            color: #e74c3c;
            margin-bottom: 15px;
        }

        .confirm-popup p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .confirm-popup .btn {
            width: 120px;
            margin: 0 10px;
            display: inline-block;
        }

        .confirm-popup .btn-confirm {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }

        .confirm-popup .btn-cancel {
            background: linear-gradient(135deg, #666, #555);
            color: #fff;
        }

        @keyframes popIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }

            h1 {
                font-size: 28px;
            }

            .booking-card h3 {
                font-size: 20px;
            }

            .booking-card p {
                font-size: 14px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<canvas class="particles" id="particles"></canvas>

<div class="container">
    <h1>My Bookings</h1>
    <div class="booking-list">
        <?php if (empty($bookings)): ?>
            <p class="no-bookings">You have no active bookings.</p>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <h3><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                    <p>Booking ID: <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                    <p>Pickup: <?php echo htmlspecialchars($booking['pickup_date'] . ' ' . $booking['pickup_time']); ?></p>
                    <p>Return: <?php echo htmlspecialchars($booking['return_date'] . ' ' . $booking['return_time']); ?></p>
                    <p>Total Amount: ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                    <p>Status: <span class="status <?php echo strtolower($booking['booking_status']); ?>">
                        <?php echo htmlspecialchars($booking['booking_status']); ?>
                    </span></p>
                    <?php if ($booking['booking_status'] === 'Pending'): ?>
                        <button class="btn btn-cancel" onclick="showCancelPopup(<?php echo $booking['booking_id']; ?>)">Cancel Booking</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button class="btn btn-back" onclick="window.location.href='dashbord.php'">Back to Dashboard</button>
</div>

<div class="overlay" id="overlay"></div>
<div class="confirm-popup" id="confirm-popup">
    <h3>Cancel Booking</h3>
    <p>Are you sure you want to cancel this booking? This action cannot be undone.</p>
    <button class="btn btn-confirm" id="confirm-cancel" onclick="cancelBooking()">Yes, Cancel</button>
    <button class="btn btn-cancel" onclick="hideCancelPopup()">No, Keep</button>
</div>

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

// Cancel Booking Functionality
let selectedBookingId = null;

function showCancelPopup(bookingId) {
    selectedBookingId = bookingId;
    const popup = document.getElementById('confirm-popup');
    const overlay = document.getElementById('overlay');
    popup.style.display = 'block';
    overlay.style.display = 'block';
}

function hideCancelPopup() {
    const popup = document.getElementById('confirm-popup');
    const overlay = document.getElementById('overlay');
    popup.style.display = 'none';
    overlay.style.display = 'none';
    selectedBookingId = null;
}

function cancelBooking() {
    if (!selectedBookingId) return;

    fetch('cancel_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + encodeURIComponent(selectedBookingId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const bookingCard = document.querySelector(`.booking-card:has([onclick="showCancelPopup(${selectedBookingId})"])`);
            if (bookingCard) bookingCard.remove();
            hideCancelPopup();
            if (document.querySelectorAll('.booking-card').length === 0) {
                document.querySelector('.booking-list').innerHTML = '<p class="no-bookings">You have no active bookings.</p>';
            }
        } else {
            alert('Error cancelling booking: ' + data.message); // Temporary alert
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error.message); // Temporary alert
    });
}
</script>

</body>
</html>