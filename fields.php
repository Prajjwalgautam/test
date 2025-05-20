<?php
session_start();



if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}
?>
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
                <?php echo $_SESSION['username']; ?>
            </div>
        </nav>
        <div class="innercontainer">

            <div class="box-container">
                <button id="easy" class="sound-btn" data-href="gamemode/easy.php">
                    <div class="box">Maths</div>
                </button>
                <button id="medium" class="sound-btn" data-href="gamemode/science.php">
                    <div class="box">Science</div>
                </button>
            </div>

        </div>


    </div>
    <audio id="clickSound" src="audio/click.mp3" preload="auto"></audio>
    <audio id="hoverSound" src="audio/hover.mp3" preload="auto"></audio>
    <script src="js/script.js"></script>

</body>

</html>