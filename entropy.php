<?php
function entropy($s) {
    // Nested function to calculate the range
    function calculate_range($s) {
        $sol = 0;
        $flags = ['lower' => false, 'upper' => false, 'number' => false, 'special' => false];
        $length = strlen($s);
        for ($i = 0; $i < $length; $i++) {
            $char = $s[$i];
            if (ctype_upper($char)) {
                $flags['upper'] = true;
            } elseif (ctype_lower($char)) {
                $flags['lower'] = true;
            } elseif (ctype_digit($char)) {
                $flags['number'] = true;
            } elseif (!ctype_alnum($char)) {
                $flags['special'] = true;
            }
        }

        if ($flags['upper'] && $flags['lower']) {
            $sol += 52;
        } else {
            $sol += 26; // Simplified to add either upper or lower case
        }
        
        if ($flags['number']) {
            $sol += 10;
        }
        
        if ($flags['special']) {
            $sol += 32;
        }

        return $sol;
    }

    return strlen($s) * log(calculate_range($s), 2);
}

function generate_strong_password($length) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    return $password;
}

function main($password) {
    $entropy_est = entropy($password);
    if ($entropy_est <= 30) {
        return "Weak! Suggested Password: " . generate_strong_password(12);
    } elseif ($entropy_est > 30 && $entropy_est <= 60) {
        return "Medium! Suggested Password: " . generate_strong_password(12);
    } else {
        return "Strong";
    }
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $message = main($password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Strength Checker</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/entropy.css">
</head>
<body>
  <div class="container mt-5">
    <h1>Password Strength Checker</h1>
    <div class="row">
      <div class="col-md-6">
        <h2>Check Your Password</h2>
        <form method="POST" action="entropy.php">
          <div class="form-group">
            <label for="passwordInput">Enter Password:</label>
            <input type="text" class="form-control" id="passwordInput" name="password" placeholder="Enter password" required>
          </div>
          <button type="submit" class="btn btn-primary">Check Password</button>
          <button onclick="window.location.href='dashboard1.php';" class="btn btn-secondary">Back to Dashboard</button>

        </form>
      </div>
      <div class="col-md-6">
        <h2>Results</h2>
        <p id="results">
          <!-- Results will be displayed here after submitting the form. -->
          <?php if (!empty($message)) { echo "<p class='alert alert-info'>$message</p>"; } ?>
        </p>
      </div>
    </div>
  </div>

  <script src="js/bootstrap.min.js"></script>
</body>
</html>
