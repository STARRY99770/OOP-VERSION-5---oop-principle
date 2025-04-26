<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News - Sarawak E-health Management System</title>
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
    <link rel="stylesheet" href="/home.css">
    <link rel="stylesheet" href="/news.css">
</head>
<body>
    <div class="background"></div>
    <header>
        <button class="menu-btn" onclick="toggleMenu()">&#9776;</button>
        <div class="logo-title">
            <img src="/images/Sarawak.jpg" alt="Logo" class="logo">
            <h1>Sarawak E-health Management System</h1>
        </div>
        <div class="top-right-container">
            <a href="/index.php"><button>Home</button></a>
            <a href="/index.php"><button>About Us</button></a>
            <a href="/index.php"><button>Contact</button></a>
            <a href="/views/login.php"><button class="login-btn">Login</button></a>
        </div>
    </header>
    
    <aside class="sidebar" id="sidebar">
        <button class="close-btn" onclick="toggleMenu()">&times;</button>
        <a href="/views/admin.php"><button>Admin Login</button></a>
        <a href="/views/admin.php"><button>Immigration Staff Login</button></a>
        <a href="/views/login.php"><button>Booking Appointment</button></a>
        <a href="/views/login.php"><button>Check Records</button></a>
        <a href="news.php"><button>News</button></a>
    </aside>
    <div class="overlay" id="overlay"></div>
    
    <main>
        <div class="news-container">
            <div class="news-header">
                <h2>Latest News & Updates</h2>
                <p>Stay informed about the latest developments in Sarawak's E-health Management System</p>
            </div>
            
            <div class="news-grid">
                <!-- News Card 1 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/SUN_health-screening-impotance-to-male-and-female.jpg" alt="Health Screening Update" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Health</span>
                            <span class="news-tag">Policy</span>
                        </div>
                        <h3 class="news-title">New Health Screening Guidelines</h3>
                        <p class="news-date">April 8, 2025</p>
                        <p class="news-excerpt">The Sarawak Health Department has announced updated health screening requirements for foreign workers. These new guidelines aim to improve the efficiency and effectiveness of the screening process while ensuring the health and safety of all workers. The updates include new testing protocols, streamlined documentation procedures, and enhanced digital reporting systems. The new system will be implemented starting next month, with a transition period for all stakeholders to adapt to the changes. Training sessions will be provided to medical staff and administrators to ensure smooth implementation.</p>
                        <div class="news-footer">
                            <span class="news-author">Health Department</span>
                            <span class="news-views">1,234 views</span>
                        </div>
                    </div>
                </div>

                <!-- News Card 2 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/system-management.png" alt="System Upgrade" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Technology</span>
                            <span class="news-tag">System</span>
                        </div>
                        <h3 class="news-title">System Upgrade Announcement</h3>
                        <p class="news-date">April 5, 2025</p>
                        <p class="news-excerpt">The E-health Management System will undergo scheduled maintenance to implement new features and improvements. This upgrade will enhance system performance, security, and user experience. Key updates include improved appointment scheduling, enhanced data analytics, and new mobile-friendly features. The maintenance window is scheduled for the weekend to minimize disruption to daily operations. Users will be notified in advance about the exact timing and duration of the maintenance period.</p>
                        <div class="news-footer">
                            <span class="news-author">IT Department</span>
                            <span class="news-views">987 views</span>
                        </div>
                    </div>
                </div>

                <!-- News Card 3 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/medical-partner.jpg" alt="Partnership News" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Partnership</span>
                            <span class="news-tag">Healthcare</span>
                        </div>
                        <h3 class="news-title">New Medical Partner Added</h3>
                        <p class="news-date">April 1, 2025</p>
                        <p class="news-excerpt">Sarawak E-health Management System welcomes three new approved medical facilities to the network. These facilities have been carefully selected based on their expertise, equipment, and commitment to maintaining high standards of healthcare services for foreign workers. The new partners will help expand the coverage of health screening services across the region, reducing waiting times and improving access to quality healthcare. Each facility has undergone a rigorous evaluation process to ensure they meet our strict standards.</p>
                        <div class="news-footer">
                            <span class="news-author">Partnership Team</span>
                            <span class="news-views">756 views</span>
                        </div>
                    </div>
                </div>

                <!-- News Card 4 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/staff-training.jpg" alt="Training Program" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Training</span>
                            <span class="news-tag">Education</span>
                        </div>
                        <h3 class="news-title">Staff Training Program</h3>
                        <p class="news-date">March 28, 2025</p>
                        <p class="news-excerpt">Comprehensive training program launched for medical staff handling foreign worker health screenings. The program includes updated protocols, new testing procedures, and best practices for maintaining high standards of care. All participating medical facilities are required to complete this training. The program consists of both online modules and hands-on workshops, ensuring that staff are well-equipped to handle the new requirements. Certification will be provided upon successful completion of the training.</p>
                        <div class="news-footer">
                            <span class="news-author">Training Department</span>
                            <span class="news-views">543 views</span>
                        </div>
                    </div>
                </div>

                <!-- News Card 5 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/2X_Health_Screening_Passes_new.jpg" alt="Digital Health Pass" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Digital</span>
                            <span class="news-tag">Innovation</span>
                        </div>
                        <h3 class="news-title">New Digital Health Pass Implementation</h3>
                        <p class="news-date">March 25, 2025</p>
                        <p class="news-excerpt">Sarawak E-health Management System introduces a new Digital Health Pass for foreign workers. This innovative solution replaces traditional paper-based health records with a secure digital system. The Digital Health Pass will store all medical records, vaccination history, and screening results in one accessible platform. Employers and authorized medical personnel can access the information instantly, reducing paperwork and improving efficiency. The system includes QR code verification and real-time updates, ensuring the most current health information is always available.</p>
                        <div class="news-footer">
                            <span class="news-author">Digital Innovation Team</span>
                            <span class="news-views">432 views</span>
                        </div>
                    </div>
                </div>

                <!-- News Card 6 -->
                <div class="news-card" onclick="toggleExpand(this)">
                    <img src="/images/mobile-app.jpg" alt="Mobile App Launch" class="news-image">
                    <div class="news-content">
                        <div class="news-tags">
                            <span class="news-tag">Mobile</span>
                            <span class="news-tag">App</span>
                        </div>
                        <h3 class="news-title">E-health Mobile App Now Available</h3>
                        <p class="news-date">March 20, 2025</p>
                        <p class="news-excerpt">The official Sarawak E-health Management System mobile app is now available for download on both iOS and Android platforms. The app provides foreign workers with easy access to their health records, appointment scheduling, and important notifications. Features include real-time appointment booking, health status tracking, and direct communication with medical facilities. The app also includes multilingual support to ensure accessibility for all foreign workers. Regular updates will be provided to enhance functionality and user experience based on feedback from the community.</p>
                        <div class="news-footer">
                            <span class="news-author">Mobile Development Team</span>
                            <span class="news-views">321 views</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function navigateTo(sectionId) {
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");
            sidebar.classList.toggle("open");
            overlay.classList.toggle("active");
        }

        function toggleExpand(card) {
            // Close all other expanded cards
            const allCards = document.querySelectorAll('.news-card');
            allCards.forEach(otherCard => {
                if (otherCard !== card && otherCard.classList.contains('expanded')) {
                    otherCard.classList.remove('expanded');
                }
            });

            // Toggle the clicked card
            card.classList.toggle('expanded');
        }

        // Close expanded card when clicking outside
        document.addEventListener('click', function(event) {
            const cards = document.querySelectorAll('.news-card');
            const isClickInsideCard = Array.from(cards).some(card => card.contains(event.target));
            
            if (!isClickInsideCard) {
                cards.forEach(card => {
                    card.classList.remove('expanded');
                });
            }
        });
    </script>
</body>
</html> 