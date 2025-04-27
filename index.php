<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarawak E-health Management System</title>
    <link rel="stylesheet" href="/home.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
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
            <!-- Moved buttons in the desired order -->
            <button onclick="navigateTo('home')">Home</button>
            <button onclick="navigateTo('about')">About Us</button>
            <button onclick="navigateTo('contact')">Contact</button>
            <a href="/views/signup.php"><button>Sign Up</button></a>
            <a href="/views/login.php"><button class="login-btn">Login</button></a>
        </div>
    </header>
    
    <aside class="sidebar" id="sidebar">
        <button class="close-btn" onclick="toggleMenu()">&times;</button>
        <a href="/views/admin.php"><button>Admin Login</button></a>
        <a href="/views/admin.php"><button>Immigration Staff Login</button></a>
        <a href="/views/login.php"><button>Booking Appointment</button></a>
        <a href="/views/login.php"><button>Check Records</button></a>
        <a href="/news.php"><button>News</button></a>
    </aside>
    <div class="overlay" id="overlay"></div>
    
    <main>
        <section id="home" class="welcome">
    <img src="/images/home.jpg" alt="welcome" class="welcome">
    <div class="home-text">
        <h2>Ensuring a Healthier Workforce for Sarawak's Future</h2>
    </div>
</section>
        
        <section id="about" class="about">
            <h3 class="heading"> <span> About </span> Us </h3>
            <p>Sarawak E-health Management System is a digital platform ensuring foreign workers meet health standards before obtaining work permits. In collaboration with the Sarawak immigration department and healthcare institutions, we provide secure health record management, appointment booking, and real-time reporting for a safer workforce.</p>
        </section>

        <section id="mission-vision" class="mission-vision">
    <div class="column">
        <h3>
            <img src="../images/mission-icon.jpg" alt="Mission Icon" class="icon"> 
            <span>Our Mission</span>
        </h3>
        <p>To ensure the health and safety of foreign workers by providing efficient health screening and monitoring services.</p>
    </div>
    <div class="column">
        <h3>
            <img src="../images/vision-icon.jpg" alt="Vision Icon" class="icon"> <!-- 添加图标 -->
            <span>Our Vision</span>
        </h3>
        <p>To be a leading platform in promoting a healthier and safer workforce through innovative health management solutions.</p>
    </div>
</section>

        <section id="information" class="about">
            <h3 class="heading"> <span> Information </span> </h3>
        </section>

        <section id="grid-container" class="three-column-layout">
    <!-- 前 3 个格子 -->
    <div class="column">
        <div class="image-container">
            <img src="/images/health-screening.jpg" alt="Health Screening">
            <div class="overlay">
                <h4>Health Screening</h4>
                <p>Sarawak manages its own foreign worker health checks. Workers must undergo mandatory screening at government-approved clinics before obtaining a work permit (PLKS). Health conditions like TB, HIV/AIDS, Hepatitis B, Syphilis, Malaria, and more could disqualify a worker.</p>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="image-container">
            <img src="/images/disease-monitoring.jpg" alt="Disease Monitoring">
            <div class="overlay">
                <h4>Disease Monitoring</h4>
                <p>The department monitors the health status of foreign workers to prevent infectious disease spread. Regular screenings are conducted for workers in high-risk industries, like plantations and construction, in collaboration with immigration authorities.</p>
            </div>
        </div>
    </div>
    
    <div class="column hidden">
        <div class="image-container">
            <img src="/images/medical-clearance.jpg" alt="Medical Clearance">
            <div class="overlay">
                <h4>Medical Clearance</h4>
                <p>After passing health checks, the Sarawak Health Department approves results for work permit issuance. If a serious illness is found, the employer and authorities are informed.</p>
            </div>
        </div>
    </div>

    <div class="column hidden">
        <div class="image-container">
            <img src="/images/public-health.jpg" alt="Public Health">
            <div class="overlay">
                <h4>Public Health</h4>
                <p>The department prevents disease outbreaks among foreign workers through vaccination programs and health awareness campaigns, ensuring workers receive necessary medical care when required.</p>
            </div>
        </div>
    </div>
    <div class="column hidden">
        <div class="image-container">
            <img src="/images/regulation.jpg" alt="Regulation">
            <div class="overlay">
                <h4>Regulation</h4>
                <p>Employers must follow health regulations when hiring workers. Inspections are conducted to check workplace hygiene, and authorities handle cases of illegal workers who fail health checks.</p>
            </div>
        </div>
    </div>
    <div class="column hidden">
        <div class="image-container">
            <img src="/images/health-screening-process.jpg" alt="Health Screening Process">
            <div class="overlay">
                <h4>Health Screening Process</h4>
                <p><strong>Step 1:</strong> Pre-Employment Medical Examination (PEME)<br>
                Conducted in the worker's home country at an approved medical facility.<br>
                Pass → Employer applies for Work Permit (PLKS).<br>
                Fail → Worker cannot enter Sarawak.<br><br>
                
                <strong>Step 2:</strong> Work Permit (PLKS) Application<br>
                Employer submits an application to the Sarawak Immigration Department.<br>
                Health report approval required → If approved, worker can enter Sarawak.<br><br>
                
                <strong>Step 3:</strong> Post-Arrival Medical Check-up (Within 30 Days)<br>
                Conducted at a Sarawak-approved hospital/clinic.<br>
                Pass → Issued Medical Clearance Certificate, Work Permit finalized.<br>
                Fail → Application rejected, worker may be deported.<br><br>
                
                <strong>Step 4:</strong> Health Monitoring & Renewal<br>
                Some industries require annual medical check-ups.<br>
                If a worker is found with a serious illness, the employer must report to the Health & Immigration Departments.<br>
                Employers must comply with health regulations or face penalties.
                </p>
            </div>
        </div>
    </div>
</section>
<div class="view-all-container">
    <button id="view-all-btn" onclick="toggleView()">View All</button>
</div>

        <section id="contact" class="contact">
            <h3>Contact Us</h3>
            <p>Reach out to us for more details and inquiries.</p>
            <p><strong>Email:</strong> ehealthmanagementsarawak@gmail.com</p>
            <p><strong>Phone:</strong> +60 82-123 4567</p>
        </section>        
    </main>

    <script>
        function navigateTo(sectionId) {
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' }); // 平滑滚动到目标区域
            }
        }
        function toggleContent(contentId) { //aboutUs.js
            const content = document.getElementById(contentId); // 获取目标内容
            if (content) { // 检查是否存在该元素
                if (content.style.display === "none" || content.style.display === "") {
                    content.style.display = "block"; // 显示内容
                } else {
                    content.style.display = "none"; // 隐藏内容
                }
            } else {
                console.error("No element found with ID:", contentId); // 调试日志
            }
        }

        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("open");
        }

        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");
            sidebar.classList.toggle("open");
            overlay.classList.toggle("active"); /* 显示或隐藏遮罩层 */
        }

        function toggleView() {
            const hiddenItems = document.querySelectorAll('.column.hidden');
            const viewAllBtn = document.getElementById('view-all-btn');

            if (!hiddenItems.length) {
                console.error("No hidden items found.");
                return;
            }

            hiddenItems.forEach(item => {
                if (item.style.display === 'block') {
                    item.style.display = 'none'; // 隐藏
                    viewAllBtn.textContent = 'View All'; // 按钮文字切换
                } else {
                    item.style.display = 'block'; // 显示
                    viewAllBtn.textContent = 'Show Less'; // 按钮文字切换
                }
            });
        }
        
    </script>
</body>
</html>