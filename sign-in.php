<?php
include("config/db_connection.php");
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Game Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>

  <?php
  include("config/db_connection.php");
  session_start();

  $jwt_secret = '&$#%*@#12xjsdjiedejid';

  if (isset($_COOKIE['token'])) {
    try {
      $decoded = JWT::decode($_COOKIE['token'], new Key($jwt_secret, 'HS256'));
    } catch (Exception $e) {
      // Handle token expiration or invalid token
      setcookie('token', '', time() - 3600, "/", "", false, true); // Delete the cookie
    }
  }

  $username = $email = $password = '';
  $username_error = $email_error = $password_error = '';
  $error = false;

  if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');


    $stmt = $conn->prepare("SELECT id, username, password FROM gamelogin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

      $stmt->bind_result($id, $db_username, $hashed_password);
      $stmt->fetch();

      if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $db_username;

        $payload = [
          'user_id' => $id,
          'username' => $db_username,
          'exp' => time() + 3600 // 1 hour expiration
        ];
        $jwt = JWT::encode($payload, $jwt_secret, 'HS256');
        setcookie('token', $jwt, time() + 3600, "/", "", false, true); // HttpOnly cookie

        header("Location: welcomepage.php");
        exit;
      } else {
        $password_error = "Incorrect password!";
      }
    } else {

      if (empty($username)) {
        $username_error = "Please enter a username!";
        $error = true;
      } elseif (strlen($username) < 3) {
        $username_error = "Username must be at least 3 characters!";
        $error = true;
      }

      if (empty($email)) {
        $email_error = "Please enter an email!";
        $error = true;
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format!";
        $error = true;
      } elseif (strpos($email, "\n") !== false || strpos($email, "\r") !== false) {
        $email_error = "Email contains invalid characters.";
        $error = true;
      }

      if (empty($password)) {
        $password_error = "Please enter a password!";
        $error = true;
      } elseif (!preg_match('/^(?=.*[A-Z])(?=(?:.*\d){2,}).{6,}$/', $password)) {
        $password_error = "Password must be 6+ chars, 1 uppercase, and 2 digits";
        $error = true;
      }

      if (!$error) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare("INSERT INTO gamelogin (username, email, password) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $username, $email, $hash);

        try {
          if ($insert_stmt->execute()) {
            $_SESSION['user_id'] = $insert_stmt->insert_id;
            $_SESSION['username'] = $username;
            header("Location: welcomepage.php");
            exit;
          }
        } catch (mysqli_sql_exception $e) {
          $email_error = "That email is already taken.";
        }
        $insert_stmt->close();
      }
    }
    $stmt->close();
  }
  ?>


  <form action="" method="POST">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 logincontainer">
          <div class="login-card p-4 text-white">
            <h2 class="text-center mb-4">🎮 Game Login</h2>
            <div class="form-floating mb-3">
              <input
                type="text"
                name="username"
                id="username"
                class="form-control"
                placeholder="Enter username"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
              <label for="username">Username</label>
              <?php if ($username_error) echo "<p class='loginerror'>$username_error</p>"; ?>
            </div>

            <div class="form-floating mb-3">
              <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                placeholder="Enter email"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
              <label for="email">Email</label>
              <?php if ($email_error) echo "<p class='loginerror'>$email_error</p>"; ?>
            </div>

            <div class="form-floating mb-3 position-relative">
              <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Password" />
              <label for="password">Password</label>
              <i class="fas fa-eye-slash  position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;"></i>
              <?php if ($password_error) echo "<p class='loginerror'>$password_error</p>"; ?>
            </div>

            <div class="loginbutton">
              <button type="submit" class="btn btn-primary w-100 mb-3 loginbtn">Login</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function() {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);

      if (type === "password") {
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
      } else {
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
      }
    });


    document.getElementById("username").addEventListener("input", function() {
      const error = this.closest(".form-floating").querySelector(".text-danger");
      if (error) error.textContent = "";
    });

    document.getElementById("email").addEventListener("input", function() {
      const error = this.closest(".form-floating").querySelector(".text-danger");
      if (error) error.textContent = "";
    });

    document.getElementById("password").addEventListener("input", function() {
      const error = this.closest(".form-floating").querySelector(".text-danger");
      if (error) error.textContent = "";
    });


    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
    const emailInput = document.getElementById("email");

    emailInput.addEventListener("input", function() {
      const emailValue = emailInput.value.trim();
      const emailErrorElement = this.closest(".form-floating").querySelector(".text-danger");

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(emailValue)) {
        if (emailErrorElement) {
          emailErrorElement.textContent = "Please enter a valid email!";
        } else {
          const error = document.createElement("p");
          error.classList.add("text-danger");
          error.textContent = "Please enter a valid email!";
          this.closest(".form-floating").appendChild(error);
        }
      } else {
        if (emailErrorElement) {
          emailErrorElement.textContent = "";
        }
      }
    });
  </script>

</body>

</html>