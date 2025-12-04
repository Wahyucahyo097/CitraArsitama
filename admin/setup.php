<?php
/*
    Database Setup Script
    Run this script once to create all necessary tables
    Access: http://localhost/Dewi-1.0.0/admin/setup.php
*/

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS citra_arsitama";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("citra_arsitama");

// Create admin_users table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table admin_users created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create portfolio table
$sql = "CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255),
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table portfolio created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create services table
$sql = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT NOT NULL,
    icon VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table services created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create team table
$sql = "CREATE TABLE IF NOT EXISTS team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio LONGTEXT,
    image VARCHAR(255),
    social JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table team created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create testimonials table
$sql = "CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    position VARCHAR(100),
    company VARCHAR(100),
    content LONGTEXT NOT NULL,
    image VARCHAR(255),
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table testimonials created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create clients table
$sql = "CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table clients created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create news table
$sql = "CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table news created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    `value` LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table settings created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Insert default settings if not exist
$default_settings = [
    ['about_title', 'Tentang Kami'],
    ['about_text', 'Kami adalah tim profesional yang berdedikasi untuk memberikan solusi terbaik untuk kebutuhan bisnis Anda.'],
    ['about_image', 'about-1.jpg']
    ,['hero_images', '["1.jpg","hero-bg.jpg"]']
];

foreach ($default_settings as $setting) {
    $key = $setting[0];
    $value = $conn->real_escape_string($setting[1]);
    $check = $conn->query("SELECT * FROM settings WHERE `key` = '$key'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO settings (`key`, `value`) VALUES ('$key', '$value')";
        if ($conn->query($sql) === TRUE) {
            echo "Setting '$key' inserted successfully<br>";
        } else {
            echo "Error inserting setting '$key': " . $conn->error . "<br>";
        }
    }
}

// Check if admin user exists
$result = $conn->query("SELECT * FROM admin_users WHERE email = 'admin@citraarsitama.com'");

if ($result->num_rows == 0) {
    // Insert default admin user
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO admin_users (name, email, password) VALUES ('Admin', 'admin@citraarsitama.com', '$default_password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<br><strong>Default admin user created:</strong><br>";
        echo "Email: admin@citraarsitama.com<br>";
        echo "Password: admin123<br>";
        echo "<br><span style='color:red;'><strong>IMPORTANT: Change the password immediately after first login!</strong></span>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "Admin user already exists<br>";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
    <link href="../assets/img/company-logo.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>
<body>
    <h2>Setup Complete!</h2>
    <p>All tables have been created successfully.</p>
    <p><a href="login.php">Go to Admin Login</a></p>
    <p><strong>Note:</strong> Delete or rename this file (setup.php) after setup for security reasons.</p>
</body>
</html>
