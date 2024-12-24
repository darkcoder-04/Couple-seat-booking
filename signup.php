<?php
// Database connection configuration
$host = 'localhost';       // Database host
$dbname = 'couple_seat_booking';     // Database name
$username = 'root';        // Database username
$password = '';            // Database password

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $pass = trim($_POST['password']);
    $confirmPass = trim($_POST['confirmPassword']);

    // Validate inputs
    if (empty($user) || empty($phone) || empty($pass) || empty($confirmPass)) {
        echo "All fields are required.";
        exit;
    }

    if (!preg_match('/^\d{10}$/', $phone)) {
        echo "Invalid phone number format.";
        exit;
    }

    if ($pass !== $confirmPass) {
        echo "Passwords do not match.";
        exit;
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/', $pass)) {
        echo "Password must be at least 8 characters long and include letters, numbers, and symbols.";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

    try {
        // Check if the username or phone already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR phone = :phone");
        $stmt->execute(['username' => $user, 'phone' => $phone]);

        if ($stmt->rowCount() > 0) {
            echo "Username or phone number already exists.";
            exit;
        }

        // Insert user data into the database
        $sql = "INSERT INTO users (username, phone, password) VALUES (:username, :phone, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'username' => $user,
            'phone' => $phone,
            'password' => $hashedPassword,
        ]);

        echo "Signup successful!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
