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
            <h2>Welcome to Sarawak Foreign Workers E-health Management System 2025</h2>
            <div class="flex-container">
                <!-- 图片容器 -->
                <div class="center-image">
                    <img src="/images/img1.jpg" alt="Welcome Image"> 
                </div>
                <!-- Text content -->
                <div class="text-content">
                    <h3>Mission</h3>
                    <p>
                        To establish a centralized, secure, and efficient digital health management system that optimizes the health screening and assessment process for foreign workers. Through digital technology and inter-agency collaboration, we aim to enhance public health governance, regulatory compliance, and workforce management efficiency, ensuring that all foreign workers meet Sarawak’s health standards before employment.
                    </p>
                    <h3>Vision</h3>
                    <p>
                        The core mission of the Sarawak E-Health Management System is to provide a digital health management platform that facilitates seamless collaboration between the Sarawak government, the Immigration Department, and healthcare institutions. This system ensures that all foreign workers entering Sarawak meet the required health standards, safeguarding public health and workforce integrity.
                    </p>
                    <!-- 注册按钮 -->
                    <div class="register">
                        <a href="/views/signup.php"><button>Foreign Workers Register</button></a>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="about" class="about">
            <h3>About Us</h3>
            <p>The Sarawak E-Health Management System is collaboration with the Sarawak Immigration Department, the Ministry of Health Sarawak, and government-recognized medical institutions. This centralized digital platform is designed to enhance the efficiency of foreign worker health screening and assessment, ensuring that all foreign workers in Sarawak meet the required health standards before obtaining work permits.</p>
            <div class="grid-container">
                <!-- 小格子 1 -->
                <div class="grid-item" onclick="toggleContent('content1')">
                    <h4>Health Screening</h4>
                    <p id="content1" class="hidden-content">
                        Sarawak manages its own foreign worker health checks. Workers must undergo mandatory screening at government-approved clinics before obtaining a work permit (PLKS). Health conditions like TB, HIV/AIDS, Hepatitis B, Syphilis, Malaria, and more could disqualify a worker.
                    </p>
                </div>
                <!-- 小格子 2 -->
                <div class="grid-item" onclick="toggleContent('content2')">
                    <h4>Disease Monitoring</h4>
                    <p id="content2" class="hidden-content">
                        The department monitors the health status of foreign workers to prevent infectious disease spread. Regular screenings are conducted for workers in high-risk industries, like plantations and construction, in collaboration with immigration authorities.
                    </p>
                </div>
                <!-- 小格子 3 -->
                <div class="grid-item" onclick="toggleContent('content3')">
                    <h4>Medical Clearance</h4>
                    <p id="content3" class="hidden-content">
                        After passing health checks, the Sarawak Health Department approves results for work permit issuance. If a serious illness is found, the employer and authorities are informed.
                    </p>
                </div>
                <!-- 小格子 4 -->
                <div class="grid-item" onclick="toggleContent('content4')">
                    <h4>Public Health</h4>
                    <p id="content4" class="hidden-content">
                        The department prevents disease outbreaks among foreign workers through vaccination programs and health awareness campaigns, ensuring workers receive necessary medical care when required.
                    </p>
                </div>
                <!-- 小格子 5 -->
                <div class="grid-item" onclick="toggleContent('content5')">
                    <h4>Regulation</h4>
                    <p id="content5" class="hidden-content">
                        Employers must follow health regulations when hiring workers. Inspections are conducted to check workplace hygiene, and authorities handle cases of illegal workers who fail health checks.
                    </p>
                </div>
                <!-- 小格子 6 -->
                <div class="grid-item" onclick="toggleContent('content6')">
                    <h4>Health Screening Process</h4>
                    <p id="content6" class="hidden-content">
                        <strong>Step 1: Pre-Employment Medical Examination (PEME)</strong><br>
                        Conducted in the worker's home country at an approved medical facility.<br>
                        Pass → Employer applies for Work Permit (PLKS).<br>
                        Fail → Worker cannot enter Sarawak.<br><br>
                        
                        <strong>Step 2: Work Permit (PLKS) Application</strong><br>
                        Employer submits an application to the Sarawak Immigration Department.<br>
                        Health report approval required → If approved, worker can enter Sarawak.<br><br>
                        
                        <strong>Step 3: Post-Arrival Medical Check-up (Within 30 Days)</strong><br>
                        Conducted at a Sarawak-approved hospital/clinic.<br>
                        Pass → Issued Medical Clearance Certificate, Work Permit finalized.<br>
                        Fail → Application rejected, worker may be deported.<br><br>
                        
                        <strong>Step 4: Health Monitoring & Renewal</strong><br>
                        Some industries require annual medical check-ups.<br>
                        If a worker is found with a serious illness, the employer must report to the Health & Immigration Departments.<br>
                        Employers must comply with health regulations or face penalties.
                    </p>
                </div>    
            </div>
        </section>
        
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
        
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("open");
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
    </script>
</body>
</html>
