<?php
session_start();
$login_error = ''; // Variable to store error message
$connection_status = ''; // Variable to store connection status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = hash('sha256', $password);

    // Create a database connection
    $conn = new mysqli("localhost", "cmsc334", "FN(p2Sfzs;g-]Y9BH&E@c7v", "Zaowen'sTable");
    if ($conn->connect_error) {
        $connection_status = "Connection failed: " . $conn->connect_error;
    } else {
        $connection_status = "Connected to MySQL Database.";

        // Prepare statement to prevent SQL Injection
        $stmt = $conn->prepare("SELECT password_hash FROM User_Login WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $user['password_hash'] === $hashed_password) {
            // Successful login
            $_SESSION['user'] = $username;
            header("Location: dashboard1.php"); // Redirect to the dashboard
            exit(); // Make sure no further script is run after redirection
        } else {
            // Authentication failed
            $login_error = 'Invalid username or password.';
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS and Custom CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link href="css/login.css" rel="stylesheet" type="text/css">

  <!-- Browser Tab title -->
  <title>Login</title>
</head>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-light" style="background-color: #3EA055;">
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
      <!-- Placeholder for potential future navigation items -->
    </div>
  </nav>
  <div class="container" style="margin-top: 100px;">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h2 class="text-center" style="margin-bottom: 20px;">Password Manager</h2>
            <form method="POST">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <small class="form-text text-danger"><?php echo $login_error; ?></small>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="form-text text-danger"><?php echo $login_error; ?></small>
              </div>
              <button type="submit" class="btn btn-primary">Login</button>

              <button onclick="window.location.href='registration.php';" class="btn btn-secondary">Register</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="js/bootstrap.min.js"></script>
</body>
</html>
