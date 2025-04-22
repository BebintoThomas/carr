<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentalX - Book Your Luxury Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-color: #ffcc00;
            --secondary-color: #ff6600;
            --dark-bg: #0a0a0a;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-light: #f5f5f5;
            --shadow-color: rgba(255, 204, 0, 0.3);
            --gradient: linear-gradient(135deg, #ffcc00, #ff6600);
            --error-color: #e74c3c;
            --success-color: #27ae60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: var(--dark-bg);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            transition: background 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(0, 0, 0, 0.95);
        }

        .navbar .logo {
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary-color);
            cursor: pointer;
        }

        .navbar a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            margin: 0 20px;
            position: relative;
            transition: color 0.3s ease;
        }

        .navbar a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .navbar a:hover::after {
            width: 100%;
        }

        .navbar a:hover {
            color: var(--primary-color);
        }

        .navbar a.logout {
            color: #ff4d4d;
        }

        .navbar a.logout:hover {
            color: #e63939;
        }

        /* Rental Section */
        .rental-section {
            padding: 120px 20px;
            background: linear-gradient(135deg, #1c1c1c, #000000);
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .rental-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 60px;
            max-width: 1400px;
            width: 100%;
            padding: 20px;
        }

        /* Car Preview */
        .car-preview {
            width: 500px;
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .car-preview:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px var(--shadow-color);
        }

        .car-preview img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.5s ease;
        }

        .car-preview:hover img {
            transform: scale(1.1);
        }

        .car-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 350px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), transparent);
            z-index: 1;
            border-radius: 15px 15px 0 0;
        }

        .car-preview h2 {
            font-size: 30px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 20px 0 10px;
            position: relative;
            z-index: 2;
        }

        .car-preview p {
            font-size: 18px;
            color: #ddd;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }

        .car-preview .badge {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-color);
            background: rgba(0, 0, 0, 0.7);
            padding: 5px 12px;
            border-radius: 5px;
            z-index: 2;
        }

        /* Form Box */
        .form-box {
            width: 550px;
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .form-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px var(--shadow-color);
        }

        .form-box h2 {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-light);
            display: block;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 10px var(--shadow-color);
            outline: none;
        }

        .form-group input:read-only {
            background: rgba(255, 255, 255, 0.03);
            color: #aaa;
        }

        .form-group .amount-note {
            font-size: 14px;
            color: #ff6666;
            margin-top: 8px;
            font-style: italic;
        }

        .form-group .icon {
            position: absolute;
            top: 40px;
            right: 15px;
            color: #aaa;
            font-size: 18px;
        }

        button {
            background: var(--gradient);
            padding: 15px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px var(--shadow-color);
        }

        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px var(--shadow-color);
            background: linear-gradient(135deg, #ff9900, #ff3300);
        }

        /* Popup */
        .custom-popup {
            position: fixed;
            top: 20px;
            right: -400px;
            width: 350px;
            padding: 20px;
            border-radius: 10px 0 0 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            z-index: 2000;
            color: #fff;
            font-size: 16px;
            display: none;
            animation: slideIn 0.5s forwards, slideOut 0.5s forwards 4s;
        }

        .custom-popup.error {
            background: var(--error-color);
        }

        .custom-popup.success {
            background: var(--success-color);
        }

        .custom-popup .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #ddd;
            transition: color 0.3s ease;
        }

        .custom-popup .close-btn:hover {
            color: #fff;
        }

        @keyframes slideIn {
            from { right: -400px; }
            to { right: 20px; }
        }

        @keyframes slideOut {
            from { right: 20px; }
            to { right: -400px; }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .rental-container {
                flex-direction: column;
                gap: 40px;
            }

            .car-preview, .form-box {
                width: 100%;
                max-width: 600px;
            }

            .car-preview img {
                height: 300px;
            }

            .car-preview::before {
                height: 300px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .navbar .logo {
                font-size: 22px;
            }

            .navbar a {
                margin: 0 10px;
                font-size: 14px;
            }

            .rental-section {
                padding: 80px 20px;
            }

            .car-preview img {
                height: 250px;
            }

            .car-preview::before {
                height: 250px;
            }

            .form-box {
                padding: 30px;
            }

            .custom-popup {
                width: 300px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">RentalX</div>
        <div>
            <a href="dashboard.php">Home</a>
            <a href="dashboard.php#cars-section">Cars</a>
            <a href="payment.php">Pay Now</a>
            <a href="login.php" class="logout">Logout</a>
        </div>
    </div>

    <!-- Rental Section -->
    <div class="rental-section">
        <div class="rental-container">
            <div class="car-preview animate-in">
                <span class="badge">PREMIUM</span>
                <img id="car-image" src="" alt="Car Image">
                <h2 id="car-title">Luxury Car Rental</h2>
                <p>Price Per Hour: ₹<span id="car-price"></span></p>
            </div>
            <div class="form-box animate-in" style="animation-delay: 0.2s;">
                <h2>Book Your Ride</h2>
                <form id="rental-form" method="POST" action="bookings.php">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                    <input type="hidden" name="car_name" id="car_name">
                    <input type="hidden" name="car_id" id="car_id">
                    <input type="hidden" name="total_amount" id="total_amount">

                    <div class="form-group">
                        <label for="pickup">Pickup Date & Time</label>
                        <input type="text" id="pickup" name="pickup_datetime" placeholder="Select pickup time" required>
                        <i class="fas fa-calendar-alt icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="dropoff">Drop-off Date & Time</label>
                        <input type="text" id="dropoff" name="dropoff_datetime" placeholder="Select drop-off time" required>
                        <i class="fas fa-calendar-alt icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="amount">Total Amount (₹)</label>
                        <input type="text" id="amount" readonly>
                        <p class="amount-note">50% advance payment required at booking</p>
                    </div>

                    <button type="submit">Book Now</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Popup -->
    <div class="custom-popup" id="customPopup">
        <span class="close-btn" onclick="closePopup()">×</span>
        <p id="popupMessage"></p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        // Car Data
        const cars = {
            "Toyota Fortuner": { id: 1, image: "for1.jpeg", price: 90 },
            "Mahindra XUV700": { id: 2, image: "xu2.jpeg", price: 100 },
            "Tata Safari": { id: 3, image: "tata1.jpeg", price: 120 },
            "Honda City Hybrid": { id: 4, image: "city1.jpg", price: 130 },
            "Hyundai i20 N Line": { id: 5, image: "nline1.jpeg", price: 110 },
            "Volkswagen Polo GT": { id: 6, image: "gt1.jpg", price: 115 },
            "Ford Endeavour": { id: 7, image: "end2.jpg", price: 125 },
            "MG Gloster": { id: 8, image: "mg1.jpeg", price: 135 }
        };

        // Initialize Car Details
        const urlParams = new URLSearchParams(window.location.search);
        const carName = urlParams.get('car');

        if (cars[carName]) {
            document.getElementById("car-title").textContent = carName;
            document.getElementById("car-image").src = cars[carName].image;
            document.getElementById("car-price").textContent = cars[carName].price.toFixed(2);
            document.getElementById("car_id").value = cars[carName].id;
            document.getElementById("car_name").value = carName;
        } else {
            showPopup("Invalid car selection. Redirecting to homepage.", true);
            setTimeout(() => { window.location.href = "dashboard.php"; }, 4000);
        }

        // Initialize Flatpickr
        flatpickr("#pickup", {
            enableTime: true,
            minDate: "today",
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            onChange: () => {
                validateDates();
                calculateAmount();
            }
        });

        flatpickr("#dropoff", {
            enableTime: true,
            minDate: "today",
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            onChange: () => {
                validateDates();
                calculateAmount();
            }
        });

        // Validate Dates
        function validateDates() {
            const pickup = document.getElementById("pickup").value;
            const dropoff = document.getElementById("dropoff").value;
            const now = new Date();

            if (!pickup || !dropoff) {
                showPopup("Please select both pickup and drop-off dates.", true);
                document.getElementById("amount").value = "";
                return false;
            }

            const pickupDate = new Date(pickup);
            const dropoffDate = new Date(dropoff);

            if (isNaN(pickupDate.getTime()) || pickupDate <= now) {
                showPopup("Pickup date must be in the future.", true);
                document.getElementById("pickup").value = "";
                document.getElementById("amount").value = "";
                return false;
            }

            if (isNaN(dropoffDate.getTime()) || dropoffDate <= pickupDate) {
                showPopup("Drop-off must be after pickup.", true);
                document.getElementById("dropoff").value = "";
                document.getElementById("amount").value = "";
                return false;
            }

            return true;
        }

        // Calculate Amount
        function calculateAmount() {
            const pickup = new Date(document.getElementById("pickup").value);
            const dropoff = new Date(document.getElementById("dropoff").value);

            if (pickup && dropoff && dropoff > pickup && cars[carName]) {
                const hours = (dropoff - pickup) / (1000 * 60 * 60);
                if (hours > 0) {
                    const total = (hours * cars[carName].price).toFixed(2);
                    document.getElementById("amount").value = total;
                    document.getElementById("total_amount").value = total;

                    // Animate amount display
                    gsap.fromTo(
                        "#amount",
                        { scale: 0.8, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 0.5, ease: "power2.out" }
                    );
                }
            } else if (!validateDates()) {
                document.getElementById("amount").value = "";
                document.getElementById("total_amount").value = "";
            }
        }

        // Form Submission
        document.getElementById("rental-form").addEventListener("submit", function(event) {
            if (!validateDates() || !document.getElementById("amount").value) {
                event.preventDefault();
                showPopup("Please complete all fields with valid dates.", true);
            } else {
                showPopup("Booking submitted successfully!", false);
            }
        });

        // Popup Handler
        function showPopup(message, isError = false) {
            const popup = document.getElementById("customPopup");
            const popupMessage = document.getElementById("popupMessage");
            popupMessage.textContent = message;
            popup.className = `custom-popup ${isError ? "error" : "success"}`;
            popup.style.display = "block";

            // GSAP Animation for Popup
            gsap.fromTo(
                popup,
                { x: 400, opacity: 0 },
                { x: 0, opacity: 1, duration: 0.5, ease: "power2.out" }
            );

            setTimeout(() => {
                gsap.to(popup, {
                    x: 400,
                    opacity: 0,
                    duration: 0.5,
                    ease: "power2.in",
                    onComplete: () => {
                        popup.style.display = "none";
                    }
                });
            }, 4000);
        }

        function closePopup() {
            const popup = document.getElementById("customPopup");
            gsap.to(popup, {
                x: 400,
                opacity: 0,
                duration: 0.5,
                ease: "power2.in",
                onComplete: () => {
                    popup.style.display = "none";
                }
            });
        }

        // Navbar Scroll Effect
        window.addEventListener("scroll", () => {
            const navbar = document.querySelector(".navbar");
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });

        // GSAP Animations
        gsap.utils.toArray(".animate-in").forEach((element, index) => {
            gsap.from(element, {
                y: 50,
                opacity: 0,
                duration: 0.8,
                delay: index * 0.2,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: element,
                    start: "top 80%",
                },
            });
        });
    </script>
</body>
</html>