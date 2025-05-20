<?php
session_start();
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../sign-in.php");
    exit();
}


if (!isset($_SESSION['username'])) {
    header("Location: ../sign-in.php");
    exit();
}
?>
<?php


// Handle reset request
if (isset($_GET['reset'])) {
    $_SESSION['points'] = 0;
    $_SESSION['answered_q1'] = false;
    $_SESSION['answered_q2'] = false;
    $_SESSION['answered_q3'] = false;
    $_SESSION['answered_q4'] = false;
    $_SESSION['answered_q5'] = false;
    header("Location: " . str_replace("?reset=1", "", $_SERVER['REQUEST_URI']));
    exit();
}

if (!isset($_SESSION['points'])) {
    $_SESSION['points'] = 0;
}
if (!isset($_SESSION['answered_q1'])) {
    $_SESSION['answered_q1'] = false;
}
if (!isset($_SESSION['answered_q2'])) {
    $_SESSION['answered_q2'] = false;
}
if (!isset($_SESSION['answered_q3'])) {
    $_SESSION['answered_q3'] = false;
}
if (!isset($_SESSION['answered_q4'])) {
    $_SESSION['answered_q4'] = false;
}
if (!isset($_SESSION['answered_q5'])) {
    $_SESSION['answered_q5'] = false;
}

$popover_message = null;
$popover_class = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['question']) && isset($_POST['answer'])) {
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        $correct_answers = [
            "q1" => "5",
            "q2" => "500",
            "q3" => "90",
            "q4" => "10000",
            "q5" => "0"
        ];

        if (isset($correct_answers[$question])) {
            if ($answer === $correct_answers[$question]) {
                $_SESSION['points'] += 3;
                $popover_message = "Correct answer! +3 point";
                $popover_class = "success";

                if ($question === "q1") {
                    $_SESSION['answered_q1'] = true;
                } elseif ($question === "q2") {
                    $_SESSION['answered_q2'] = true;
                } elseif ($question === "q3") {
                    $_SESSION['answered_q3'] = true;
                } elseif ($question === "q4") {
                    $_SESSION['answered_q4'] = true;
                } elseif ($question === "q5") {
                    $_SESSION['answered_q5'] = true;
                }
            } else {
                $popover_message = "Wrong answer! -3 point";
                $popover_class = "error";
                $_SESSION['points'] = max(0, $_SESSION['points'] - 3);
            }
        }
    }
}

$game_completed = $_SESSION['answered_q1'] && $_SESSION['answered_q2'] && $_SESSION['answered_q3'] && $_SESSION['answered_q4'] && $_SESSION['answered_q5'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Game</title>
    <link rel="stylesheet" href="../css/welcomepage.css">
    <link rel="stylesheet" href="../css/easy.css">

</head>

<body>
    <?php if ($popover_message): ?>
        <div class="popover-message <?php echo $popover_class; ?>">
            <?php echo $popover_message; ?>
        </div>
        <audio id="successSound" src="../audio/success.mp3" preload="auto"></audio>
        <audio id="errorSound" src="../audio/error.mp3" preload="auto"></audio>
        <audio id="hoverSound" src="../audio/hover.mp3" preload="auto"></audio>

        <script>
            // for sound 
            window.addEventListener('DOMContentLoaded', function() {
                var popover = document.querySelector('.popover-message');
                if (popover) {
                    if (popover.classList.contains('success')) {
                        document.getElementById('successSound').play();
                    } else if (popover.classList.contains('error')) {
                        document.getElementById('errorSound').play();
                    }
                }
            });
        </script>
        <script>
            setTimeout(function() {
                document.querySelector('.popover-message').remove();
            }, 4000);
        </script>
    <?php endif; ?>

    <div class="welcome-container">
        <nav>
            <div class="pointstable" id="pointstable">
                points: <?php echo $_SESSION['points']; ?>
            </div>
            <div class="loginname" id="loginname">
                <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : "Guest"; ?>
            </div>
        </nav>
        <?php
        $total_questions = 5;
        $answered = 0;
        for ($i = 1; $i <= $total_questions; $i++) {
            if ($_SESSION["answered_q$i"]) $answered++;
        }
        $progress_percent = ($answered / $total_questions) * 100;
        ?>
        <div class="progress-bar-container" style="margin-bottom: 10px;">
            <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%;"></div>
            <div class="progressbar" style="text-align:center; "><?php echo $answered; ?> / <?php echo $total_questions; ?> questions completed</div>
        </div>
        <div class="innercontainer">
            <div class="box-container">

                <?php if (!$game_completed): ?>
                    <!-- Question 1 -->
                    <?php if (!$_SESSION['answered_q1']): ?>
                        <div class="question">
                            <p><strong>Question 1:</strong> What is 2 × 2 + 1?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q1">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="5" class="optionsnumber">5</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="4" class="optionsnumber">4</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="6" class="optionsnumber">6</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="8" class="optionsnumber">8</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 2 -->
                    <?php if ($_SESSION['answered_q1'] && !$_SESSION['answered_q2']): ?>
                        <div class="question">
                            <p><strong>Question 2:</strong> What is 5 × 10 × 10?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q2">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="5000" class="optionsnumber">5000</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="500" class="optionsnumber">500</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="600" class="optionsnumber">600</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="800" class="optionsnumber">800</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 3 -->
                    <?php if ($_SESSION['answered_q2'] && !$_SESSION['answered_q3']): ?>
                        <div class="question">
                            <p><strong>Question 3:</strong> What is 9 × 10 + 10 - 10?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q3">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="90" class="optionsnumber">90</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="80" class="optionsnumber">80</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="70" class="optionsnumber">70</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="60" class="optionsnumber">60</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 4 -->
                    <?php if ($_SESSION['answered_q3'] && !$_SESSION['answered_q4']): ?>
                        <div class="question">
                            <p><strong>Question 4:</strong> What is 10 × 10 (10×10 )?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q4">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="10000" class="optionsnumber">10000</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="900" class="optionsnumber">900</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="80000" class="optionsnumber">80000</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="7000" class="optionsnumber">7000</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 5 -->
                    <?php if ($_SESSION['answered_q4'] && !$_SESSION['answered_q5']): ?>
                        <div class="question">
                            <p><strong>Question 5:</strong> What is 20 × 10 - 20 × 10?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q5">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="200" class="optionsnumber">200</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="100" class="optionsnumber">100</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="300" class="optionsnumber">300</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="0" class="optionsnumber">0</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <div class="question">
                        <div class="completion-message">
                            <div id="confetti-container"></div>
                            <h2>🎉 Congratulations! 🎉</h2>
                            <p>You've completed the game with <?php echo $_SESSION['points']; ?> points!</p>
                            <p>Now Sleep🔫!</p>
                            <button class="reset-btn" id="playAgainBtn" onclick="location.href='?reset=1'">Play Again</button>
                            <button class="reset-btn" id="nextGameBtn" style="margin-top: 10px;" onclick="location.href='../welcomepage.php?reset=1'">Next game</button>
                            <form action="" method="post" style="margin-top: 10px;">
                                <button type="submit" class="reset-btn" id="logoutBtn" name="logout">Logout</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</body>

</html>