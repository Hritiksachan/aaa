<?php
 
session_start();

 
$conn = new mysqli('localhost', 'root', '', 'airline');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['username'] = $username;

         
        setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), "/"); // 30 days
        header('Location: index.php');
        exit();
    } else {
        $login_error = "Invalid username or password.";
    }
}

 
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_ticket'])) {
    if (!isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $departure_city = $_POST['departure_city'];
    $destination_city = $_POST['destination_city'];
    $travel_date = $_POST['travel_date'];
    $booking_date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO tickets (name, email, phone, departure_city, destination_city, travel_date, booking_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $name, $email, $phone, $departure_city, $destination_city, $travel_date, $booking_date);

    if ($stmt->execute()) {
        $message = "Booking successful!";
    } else {
        $message = "Booking failed. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Ticket Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, select, button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }
        .error {
            text-align: center;
            color: red;
            margin-top: 10px;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: #007bff;
            text-decoration: none;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['username'])): ?>
            
            <h1>Login</h1>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <?php if (isset($login_error)): ?>
                <p class="error"><?php echo $login_error; ?></p>
            <?php endif; ?>
        <?php else: ?>
             
            <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="departure_city" placeholder="Departure City" required>
                <input type="text" name="destination_city" placeholder="Destination City" required>
                <label for="travel_date">Travel Date:</label>
                <input type="date" name="travel_date" required>
                <button type="submit" name="book_ticket">Book Ticket</button>
            </form>
            <?php if (isset($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="logout">
                <p>Your last login: <strong><?php echo isset($_COOKIE['last_login']) ? $_COOKIE['last_login'] : 'Unknown'; ?></strong></p>
                <a href="?logout=true">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
