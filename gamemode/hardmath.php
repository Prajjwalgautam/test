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

if (!isset($_SESSION['points'])) $_SESSION['points'] = 0;
if (!isset($_SESSION['answered_q1'])) $_SESSION['answered_q1'] = false;
if (!isset($_SESSION['answered_q2'])) $_SESSION['answered_q2'] = false;
if (!isset($_SESSION['answered_q3'])) $_SESSION['answered_q3'] = false;
if (!isset($_SESSION['answered_q4'])) $_SESSION['answered_q4'] = false;
if (!isset($_SESSION['answered_q5'])) $_SESSION['answered_q5'] = false;

$popover_message = null;
$popover_class = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['question']) && isset($_POST['answer'])) {
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        $correct_answers = [
            "q1" => "0",
            "q2" => "32",
            "q3" => "6",
            "q4" => "50",
            "q5" => "9"
        ];

        if (isset($correct_answers[$question])) {
            if ($answer === $correct_answers[$question]) {
                $_SESSION['points'] += 5;
                $popover_message = "Correct answer! +5 point";
                $popover_class = "success";

                if ($question === "q1") $_SESSION['answered_q1'] = true;
                elseif ($question === "q2") $_SESSION['answered_q2'] = true;
                elseif ($question === "q3") $_SESSION['answered_q3'] = true;
                elseif ($question === "q4") $_SESSION['answered_q4'] = true;
                elseif ($question === "q5") $_SESSION['answered_q5'] = true;
            } else {
                $popover_message = "Wrong answer! -5 point";
                $popover_class = "error";
                $_SESSION['points'] = max(0, $_SESSION['points'] - 5);
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
    <?php endif; ?>

    <audio id="hoverSound" src="../audio/hover.mp3" preload="auto"></audio>

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
                <?php
                $total_questions = 5;
                $answered = 0;
                for ($i = 1; $i <= $total_questions; $i++) {
                    if ($_SESSION["answered_q$i"]) $answered++;
                }
                $progress_percent = ($answered / $total_questions) * 100;
                ?>
                <div class="progress-bar-container" style="width: 100%; background: #eee; border-radius: 8px; margin-bottom: 20px;">
                    <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%; height: 20px; background: #4caf50; border-radius: 8px; transition: width 0.5s;"></div>
                </div>
                <div style="text-align:center; margin-bottom:10px;"><?php echo $answered; ?> / <?php echo $total_questions; ?> questions completed</div>

                <?php if (!$game_completed): ?>
                    <!-- Question 1 -->
                    <?php if (!$_SESSION['answered_q1']): ?>
                        <div class="question">
                            <p><strong>Question 1:</strong> What is the value of 4 x 5 x 0 x 2?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q1">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="15" class="optionsnumber">15</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="20" class="optionsnumber">20</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="40" class="optionsnumber">40</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="0" class="optionsnumber">0</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 2 -->
                    <?php if ($_SESSION['answered_q1'] && !$_SESSION['answered_q2']): ?>
                        <div class="question">
                            <p><strong>Question 2:</strong>What is the next number in the sequence: 2, 4, 8, 16, ...?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q2">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="50" class="optionsnumber">50</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="32" class="optionsnumber">32</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="22" class="optionsnumber">22</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="12" class="optionsnumber">12</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 3 -->
                    <?php if ($_SESSION['answered_q2'] && !$_SESSION['answered_q3']): ?>
                        <div class="question">
                            <p><strong>Question 3:</strong> How many faces does a cube have?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q3">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="9" class="optionsnumber">9</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="8" class="optionsnumber">8</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="6" class="optionsnumber">6</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="5" class="optionsnumber">5</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 4 -->
                    <?php if ($_SESSION['answered_q3'] && !$_SESSION['answered_q4']): ?>
                        <div class="question">
                            <p><strong>Question 4:</strong> What is the value of 2500 divided by 50?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q4">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="60" class="optionsnumber">60</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="40" class="optionsnumber">40</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="50" class="optionsnumber">50</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="30" class="optionsnumber">30</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 5 -->
                    <?php if ($_SESSION['answered_q4'] && !$_SESSION['answered_q5']): ?>
                        <div class="question">
                            <p><strong>Question 5:</strong> What is the next number in the sequence: 1, 3, 5, 7, ?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q5">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="2" class="optionsnumber">2</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="1" class="optionsnumber">1</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="3" class="optionsnumber">3</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="9" class="optionsnumber">9</button></div>
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