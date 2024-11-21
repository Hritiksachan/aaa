<?php
 
session_start();

 
$conn = new mysqli('localhost', 'root', '', 'airline');
if ($conn->connect_error) {
    die("Database connection failed!");
}

 
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Secure the password with hashing

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows === 1) {
        $_SESSION['username'] = $username;
        setcookie('last_login', date('Y-m-d H:i:s'), time() + 86400 * 30, "/");
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}

 
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

 
if (isset($_POST['book_ticket'])) {
    if (!isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $departure = $_POST['departure_city'];
    $destination = $_POST['destination_city'];
    $travel_date = $_POST['travel_date'];

    $query = "INSERT INTO tickets (name, email, phone, departure_city, destination_city, travel_date) 
              VALUES ('$name', '$email', '$phone', '$departure', '$destination', '$travel_date')";

    if ($conn->query($query)) {
        $message = "Booking successful!";
    } else {
        $message = "Booking failed.";
    }
}
?>
