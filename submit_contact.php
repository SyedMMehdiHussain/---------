<?php
// Database connection settings
$host = 'localhost'; // Change to your database host
$dbname = 'your_database'; // Change to your database name
$username = 'your_username'; // Change to your database username
$password = 'your_password'; // Change to your database password

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$messageSent = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secretKey = 'YOUR_SECRET_KEY'; // Replace with your actual secret key
    $responseKey = $_POST['g-recaptcha-response'];
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Verify the reCAPTCHA response
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$responseKey}&remoteip={$userIP}");
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $messageSent = 'Please complete the reCAPTCHA';
    } else {
        // Sanitize input data
        $name = $conn->real_escape_string(trim($_POST['name']));
        $email = $conn->real_escape_string(trim($_POST['email']));
        $message = $conn->real_escape_string(trim($_POST['message']));

        // Insert data into the database
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
        
        if ($conn->query($sql) === TRUE) {
            $messageSent = 'Thank you for your message!';
        } else {
            $messageSent = 'Error: ' . $sql . '<br>' . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();

// Redirect back to the contact page with a message
header("Location: contact.php?message=" . urlencode($messageSent));
exit();
?>
