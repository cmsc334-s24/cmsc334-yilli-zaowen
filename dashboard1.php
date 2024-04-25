<?php
session_start();
if (!isset($_SESSION['user'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Encryption and decryption functions using OpenSSL
function encryptData($data, $secretKey) {
    $ivLength = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encryptedData = openssl_encrypt($data, $cipher, $secretKey, 0, $iv);
    $encryptedData = base64_encode($encryptedData . "::" . $iv);
    return $encryptedData;
}

function decryptData($encryptedData, $secretKey) {
    list($encryptedData, $iv) = explode("::", base64_decode($encryptedData), 2);
    $decryptedData = openssl_decrypt($encryptedData, "AES-128-CBC", $secretKey, 0, $iv);
    return $decryptedData;
}

// Secret key for encryption/decryption
$secretKey = 'your-secret-encryption-key'; // Ensure this key is securely managed and not hard-coded in production

// Function to store password in the database
function store_password($connection, $username, $webserver, $password, $secretKey) {
    $encrypted_password = encryptData($password, $secretKey); // Encrypt the password
    $query = "INSERT INTO Stored_Passwords (username, webserver, encrypted_password) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sss", $username, $webserver, $encrypted_password);
    if ($stmt->execute()) {
        echo "<p>Password stored successfully</p><br>";
    } else {
        echo "<p>Error storing password: " . $stmt->error . "</p><br>";
    }
    $stmt->close();
}

// Function to search for a password
function search_password($connection, $username, $webserver, $secretKey) {
    $stmt = $connection->prepare("SELECT encrypted_password FROM Stored_Passwords WHERE username = ? AND webserver = ?");
    $stmt->bind_param("ss", $username, $webserver);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $decrypted_password = decryptData($row['encrypted_password'], $secretKey);
        return "Password found: " . htmlspecialchars($decrypted_password);
    } else {
        return "No password found for this website.";
    }
}
require_once 'db_config.php'; // Include your database configuration file

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDbConnection();
    if (isset($_POST['webserver']) && isset($_POST['password'])) {
        store_password($conn, $_SESSION['user'], $_POST['webserver'], $_POST['password'], $secretKey);
    } elseif (isset($_POST['searchWebserver'])) {
        $message = search_password($conn, $_SESSION['user'], $_POST['searchWebserver'], $secretKey);
    }
    $conn->close();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <span class="navbar-brand">PasswordManager</span>
  </nav>

  <div class="container">
    <h1>Welcome <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
    <div class="row">
      <div class="col-md-6">
        <h2>Search for a Password</h2>
        <form method="POST" action="dashboard1.php">
          <div class="form-group">
            <label for="searchWebserver">Websie tURL:</label>
            <input type="text" class="form-control" id="searchWebserver" name="searchWebserver" required>
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <?php if (!empty($message)) { echo "<p>$message</p>"; } ?>
      </div>
      <div class="col-md-6">
        <h2>Save a New Password</h2>
        <form method="POST" action="dashboard1.php">
          <div class="form-group">
            <label for="webserver">Website URL:</label>
            <input type="text" class="form-control" id="webserver" name="webserver" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="text" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-success">Save</button>
          <button onclick="window.location.href='entropy.php';" class="btn btn-secondary">Password Checking</button>


        </form>
      </div>
    </div>
  </div>

  <script src="js/bootstrap.min.js"></script>
  <script src="js/dashboard.js"></script>
</body>
</html>
