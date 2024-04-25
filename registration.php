<?php
session_start();
$registration_error = ''; // Variable to store registration error message
$password_error = ''; // Variable to store password match error
$connection_status = ''; // Variable to store connection status

// Create a database connection
$conn = new mysqli("localhost", "cmsc334", "FN(p2Sfzs;g-]Y9BH&E@c7v", "Zaowen'sTable");
if ($conn->connect_error) {
    $connection_status = "Connection failed: " . $conn->connect_error;
} else {
    $connection_status = "MySQL Database connection successful";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    if ($password !== $confirm_password) {
        $password_error = 'Passwords do not match.';
    } else {
        $hashed_password = hash('sha256', $password);

        $stmt = $conn->prepare("SELECT username FROM User_Login WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $registration_error = 'Username already exists.';
        } else {
            $stmt = $conn->prepare("INSERT INTO User_Login (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                header("Location: login.php"); // Redirect to the login page
                exit();
            } else {
                $registration_error = 'Error in registration.';
            }
        }
        $stmt->close();
    }
}
$conn->close();
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
  <title>Registration</title>
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
            <h2 class="text-center" style="margin-bottom: 20px;">Register</h2>
            <form method="POST">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <small class="form-text text-danger"><?php echo $registration_error; ?></small>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                <small class="form-text text-danger"><?php echo $password_error; ?></small>
              </div>
              <button type="submit" class="btn btn-primary">Register</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="js/bootstrap.min.js"></script>
</body>
</html>
