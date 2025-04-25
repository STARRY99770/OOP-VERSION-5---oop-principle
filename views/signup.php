<?php
class Database {
    protected $servername, $username, $password, $database;

    public function __construct($servername, $username, $password, $database) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function getConnection() {
        return new mysqli($this->servername, $this->username, $this->password, $this->database);
    }
}

class User {
    protected $medicalID, $fullName, $dob, $gender, $nationality, $passportNumber, $phoneNumber;

    public function __construct($data) {
        $this->medicalID = $data['medicalID'] ?? '';
        $this->fullName = $data['fullName'] ?? '';
        $this->dob = $data['dob'] ?? '';
        $this->gender = $data['gender'] ?? '';
        $this->nationality = $data['nationality'] ?? '';
        $this->passportNumber = $data['passportNumber'] ?? '';
        $this->phoneNumber = $data['phoneNumber'] ?? '';
    }

    public function validate() {
        $errors = [];
        if (empty($this->fullName)) $errors[] = "Full name is required";
        if (empty($this->dob)) $errors[] = "Date of birth is required";
        if (empty($this->gender)) $errors[] = "Gender is required";
        if (empty($this->nationality)) $errors[] = "Nationality is required";
        if (empty($this->passportNumber)) $errors[] = "Passport number is required";
        if (empty($this->phoneNumber)) $errors[] = "Phone number is required";

        return $errors;
    }

    // Getters...
    public function getMedicalID() { return $this->medicalID; }
    public function getFullName() { return $this->fullName; }
    public function getDob() { return $this->dob; }
    public function getGender() { return $this->gender; }
    public function getNationality() { return $this->nationality; }
    public function getPassportNumber() { return $this->passportNumber; }
    public function getPhoneNumber() { return $this->phoneNumber; }
}

class ForeignWorker extends User {
    private $companyName, $companyAddress, $employerName, $employerPhone, $officePhone, $email, $userID, $password;

    public function __construct($data) {
        parent::__construct($data);
        $this->companyName = $data['companyName'] ?? '';
        $this->companyAddress = $data['companyAddress'] ?? '';
        $this->employerName = $data['employerName'] ?? '';
        $this->employerPhone = $data['employerPhone'] ?? '';
        $this->officePhone = $data['officePhone'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->userID = $data['userID'] ?? '';
        $this->password = $data['password'] ?? '';
    }

    public function validate() {
        $errors = parent::validate();  // Validate common fields from the User class
        if (empty($this->companyName)) $errors[] = "Company name is required";
        if (empty($this->companyAddress)) $errors[] = "Company address is required";
        if (empty($this->employerName)) $errors[] = "Employer name is required";
        if (empty($this->email)) $errors[] = "Email is required";
        if (empty($this->userID)) $errors[] = "User ID is required";
        if (empty($this->password)) $errors[] = "Password is required";
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";

        return $errors;
    }

    // Getters...
    public function getCompanyName() { return $this->companyName; }
    public function getCompanyAddress() { return $this->companyAddress; }
    public function getEmployerName() { return $this->employerName; }
    public function getEmployerPhone() { return $this->employerPhone; }
    public function getOfficePhone() { return $this->officePhone; }
    public function getEmail() { return $this->email; }
    public function getUserID() { return $this->userID; }
    public function getPassword() { return $this->password; }
}

class DatabaseOperations {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateMedicalID() {
        do {
            $id = "MED" . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
            $check = $this->conn->query("SELECT id FROM registration WHERE medical_id = '$id'");
        } while ($check && $check->num_rows > 0);
        return $id;
    }

    public function userExists($email, $userID, $passportNumber) {
        $stmt = $this->conn->prepare("SELECT * FROM registration WHERE email = ? OR user_id = ? OR passport_number = ?");
        $stmt->bind_param("sss", $email, $userID, $passportNumber);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function registerUser(ForeignWorker $user) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO registration (medical_id, full_name, dob, gender, nationality, passport_number, phone_number, company_name, company_address, employer_name, employer_phone, office_phone, email, user_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssss",
                $user->getMedicalID(), $user->getFullName(), $user->getDob(), $user->getGender(), $user->getNationality(),
                $user->getPassportNumber(), $user->getPhoneNumber(), $user->getCompanyName(),
                $user->getCompanyAddress(), $user->getEmployerName(), $user->getEmployerPhone(),
                $user->getOfficePhone(), $user->getEmail(), $user->getUserID(), $user->getPassword()
            );
            if (!$stmt->execute()) throw new Exception("Error registering user: " . $stmt->error);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

try {
    $dbConfig = new Database("sql313.infinityfree.com", "if0_38830936", "IxMNMzyIdNj", "if0_38830936_ehealth");
    $conn = $dbConfig->getConnection();

    if ($conn->connect_error) throw new Exception("Connection failed: " . $conn->connect_error);

    $dbOps = new DatabaseOperations($conn);
    $generatedMedicalID = $dbOps->generateMedicalID();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new ForeignWorker($_POST);
        $errors = $user->validate();

        if ($_POST['password'] !== $_POST['confirmPassword']) $errors[] = "Passwords do not match";
        if (empty($errors)) {
            if ($dbOps->userExists($user->getEmail(), $user->getUserID(), $user->getPassportNumber())) {
                throw new Exception("User already exists.");
            }
            if ($dbOps->registerUser($user)) {
                echo "<script>alert('Registration successful. Please sign in.'); window.location.href = 'login.php';</script>";
                exit();
            }
        } else {
            throw new Exception(implode("<br>", $errors));
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href = 'signup.php';</script>";
}
?>

<!-- Signup HTML Form (same as before) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/pageFW/foreign-worker-login.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
    <header>
        <div class="logo-title centered-title">
            <button onclick="history.back()" class="back-button">Back</button>
            <img src="/images/srw.png" alt="Logo" class="logo">
            <h1>Sarawak E-health Management System</h1>
        </div>
    </header>

    <main class="login-main">
        <div class="login-container">
            <h2>Sign Up for E-health Management System</h2>

            <form id="signupForm" action="" method="post">
                <div class="scrollable-form">
                    <div class="input-group">
                        <label for="medicalID">Medical ID</label>
                        <input type="text" id="medicalID" name="medicalID" value="<?php echo $generatedMedicalID; ?>" class="gray-input" readonly required>
                    </div>
                    <div class="input-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>
                    <div class="input-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="input-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="gender-input" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" required>
                    </div>
                    <div class="input-group">
                        <label for="passportNumber">Passport Number</label>
                        <input type="text" id="passportNumber" name="passportNumber" required>
                    </div>
                    <div class="input-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" required>
                    </div>

                    <h3>Employer Information</h3>

                    <div class="input-group">
                        <label for="companyName">Company Name</label>
                        <input type="text" id="companyName" name="companyName" required>
                    </div>
                    <div class="input-group">
                        <label for="companyAddress">Company Address</label>
                        <input type="text" id="companyAddress" name="companyAddress" required>
                    </div>
                    <div class="input-group">
                        <label for="employerName">Employer Name</label>
                        <input type="text" id="employerName" name="employerName" required>
                    </div>
                    <div class="input-group">
                        <label for="employerPhone">Employer Phone</label>
                        <input type="text" id="employerPhone" name="employerPhone" required>
                    </div>
                    <div class="input-group">
                        <label for="officePhone">Office Phone</label>
                        <input type="text" id="officePhone" name="officePhone" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="userID">User ID</label>
                        <input type="text" id="userID" name="userID" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="input-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="sign-up-btn">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
