<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentalX - Premium Luxury Car Rentals</title>
    <style>
        :root {
            --primary-color: #ffcc00;
            --secondary-color: #ff6600;
            --dark-bg: #0a0a0a;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-light: #f5f5f5;
            --shadow-color: rgba(255, 204, 0, 0.3);
            --gradient: linear-gradient(135deg, #ffcc00, #ff6600);
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
            overflow-x: hidden;
        }

        /* Advanced Navbar */
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

        .navbar .btn {
            background: var(--gradient);
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .navbar .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url('bac.jpg') no-repeat center center/cover;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
        }

        .hero h1 {
            font-size: 70px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 24px;
            font-weight: 300;
            margin-bottom: 30px;
            max-width: 600px;
        }

        .explore-btn {
            background: var(--gradient);
            padding: 15px 50px;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px var(--shadow-color);
            text-decoration: none;
        }

        .explore-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px var(--shadow-color);
            background: linear-gradient(135deg, #ff9900, #ff3300);
        }

        /* Cars Section */
        .cars-section {
            padding: 100px 50px;
            background: linear-gradient(135deg, #1c1c1c, #000000);
            text-align: center;
        }

        .cars-section h2 {
            font-size: 48px;
            font-weight: 800;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 50px;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .filter-btn {
            background: var(--card-bg);
            padding: 10px 25px;
            border: none;
            border-radius: 25px;
            color: var(--text-light);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--gradient);
            color: #fff;
            transform: translateY(-3px);
        }

        .cars-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            padding: 20px;
        }

        .car-box {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            cursor: pointer;
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .car-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px var(--shadow-color);
        }

        .car-box img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .car-box:hover img {
            transform: scale(1.1);
        }

        .car-info {
            padding: 20px;
            text-align: center;
        }

        .car-info h2 {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .car-info p {
            font-size: 18px;
            font-weight: 400;
            color: #ddd;
            margin-bottom: 15px;
        }

        .rent-btn {
            background: var(--gradient);
            padding: 10px 30px;
            border: none;
            border-radius: 25px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .rent-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        /* Footer */
        footer {
            background: #000;
            padding: 50px 20px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        footer p {
            margin-bottom: 10px;
        }

        footer .social-icons a {
            color: #777;
            margin: 0 10px;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        footer .social-icons a:hover {
            color: var(--primary-color);
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

            .hero h1 {
                font-size: 40px;
            }

            .hero p {
                font-size: 18px;
            }

            .cars-section {
                padding: 50px 20px;
            }

            .cars-section h2 {
                font-size: 36px;
            }

            .car-box img {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">RentalX</div>
        <div>
            <a href="#">Home</a>
            <a href="#cars-section">Cars</a>
            <a href="mybookings.php" class="btn btn-mybookings">My Bookings</a>
            <a href="login.php" style="color: #ff4d4d;">Logout</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1 class="animate-in">Premium Car Rentals</h1>
            <p class="animate-in" style="margin-left:200px;animation-delay: 0.2s;">Experience luxury with our exclusive collection of high-performance vehicles.</p>
            <a href="#cars-section" class="explore-btn animate-in" style="animation-delay: 0.4s;">Explore Now</a>
        </div>
    </div>

    <!-- Cars Section -->
    <div class="cars-section" id="cars-section">
        <h2 class="animate-in">Our Exclusive Fleet</h2>
        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="Automatic">Automatic</button>
            <button class="filter-btn" data-filter="Manual">Manual</button>
            <button class="filter-btn" data-filter="sport">Sport</button>
        </div>
        <div class="cars-container">
            <div class="car-box animate-in" data-category="Automatic" onclick="redirectToPage('Toyota Fortuner')">
                <img src="for1.jpeg" alt="Toyota Fortuner">
                <div class="car-info">
                    <h2>Toyota Fortuner</h2>
                    <p>Per Hour: ₹90</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="Manual" onclick="redirectToPage('Mahindra XUV700')">
                <img src="xu2.jpeg" alt="Mahindra XUV700">
                <div class="car-info">
                    <h2>Mahindra XUV700</h2>
                    <p>Per Hour: ₹100</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="Automatic" onclick="redirectToPage('Tata Safari')">
                <img src="tata1.jpeg" alt="Tata Safari">
                <div class="car-info">
                    <h2>Tata Safari</h2>
                    <p>Per Hour: ₹120</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="Manual" onclick="redirectToPage('Honda City Hybrid')">
                <img src="city1.jpg" alt="Honda City Hybrid">
                <div class="car-info">
                    <h2>Honda City Hybrid</h2>
                    <p>Per Hour: ₹130</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="sport" onclick="redirectToPage('Hyundai i20 N Line')">
                <img src="nline1.jpeg" alt="Hyundai i20 N Line">
                <div class="car-info">
                    <h2>Hyundai i20 N Line</h2>
                    <p>Per Hour: ₹110</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="sport" onclick="redirectToPage('Volkswagen Polo GT')">
                <img src="gt1.jpg" alt="Volkswagen Polo GT">
                <div class="car-info">
                    <h2>Volkswagen Polo GT</h2>
                    <p>Per Hour: ₹115</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="Automatic" onclick="redirectToPage('Ford Endeavour')">
                <img src="end2.jpg" alt="Ford Endeavour">
                <div class="car-info">
                    <h2>Ford Endeavour</h2>
                    <p>Per Hour: ₹125</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
            <div class="car-box animate-in" data-category="Manual" onclick="redirectToPage('MG Gloster')">
                <img src="mg1.jpeg" alt="MG Gloster">
                <div class="car-info">
                    <h2>MG Gloster</h2>
                    <p>Per Hour: ₹135</p>
                    <button class="rent-btn">Rent Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 RentalX. All Rights Reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // GSAP Animations
        gsap.utils.toArray('.animate-in').forEach((element, index) => {
            gsap.from(element, {
                y: 50,
                opacity: 0,
                duration: 0.8,
                delay: index * 0.1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: element,
                    start: 'top 80%',
                },
            });
        });

        // Car Filter Functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const carBoxes = document.querySelectorAll('.car-box');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');

                carBoxes.forEach(box => {
                    const category = box.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        box.style.display = 'block';
                        gsap.fromTo(
                            box,
                            { opacity: 0, y: 30 },
                            { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }
                        );
                    } else {
                        gsap.to(box, {
                            opacity: 0,
                            y: 30,
                            duration: 0.5,
                            ease: 'power2.in',
                            onComplete: () => {
                                box.style.display = 'none';
                            },
                        });
                    }
                });
            });
        });

        // Redirect Function
        function redirectToPage(car) {
            window.location.href = `rental.php?car=${encodeURIComponent(car)}`;
        }
    </script>
</body>
</html>