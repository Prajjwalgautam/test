<?php
session_start();


if (!isset($_SESSION['username'])) {
  header("Location: sign-in.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Game</title>
  <link rel="stylesheet" href="css/welcomepage.css">
  <link rel="stylesheet" href="css/easy.css">
</head>

<body>

  <div class="welcome-container">
    <nav>
      <div class="loginname" id="loginname">
        <?php echo htmlspecialchars($_SESSION['username']); ?>
      </div>
      <audio id="hoverSound" src="audio/hover.mp3" preload="auto"></audio>
    </nav>
    <div class="innercontainer">
      <div class="welcome-message">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
      </div>
      <div class="box-container">
        <button id="easy" class="difficulty-btn" data-href="fields.php">
          <div class="box">Easy</div>
        </button>
        <button id="medium" class="difficulty-btn" data-href="medium.php">
          <div class="box">Medium</div>
        </button>
        <button id="hard" class="difficulty-btn" data-href="hard.php">
          <div class="box">Hard</div>
        </button>
      </div>
    </div>
  </div>

  <audio id="clickSound" src="audio/click.mp3" preload="auto"></audio>
  <script src="js/script.js"></script>
</body>

</html>