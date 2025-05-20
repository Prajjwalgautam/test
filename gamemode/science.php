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
            "q1" => "science",
            "q2" => "animal",
            "q3" => "pet",
            "q4" => "liquid",
            "q5" => "planet"
        ];

        if (isset($correct_answers[$question])) {
            if ($answer === $correct_answers[$question]) {
                $_SESSION['points'] += 1;
                $popover_message = "Correct answer! +1 point";
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
                $popover_message = "Wrong answer! -1 point";
                $popover_class = "error";
                $_SESSION['points'] = max(0, $_SESSION['points'] - 1);
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
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
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


                <?php if (!$game_completed): ?>
                    <!-- Question 1 -->
                    <?php if (!$_SESSION['answered_q1']): ?>
                        <div class="question">
                            <p><strong>Question 1:</strong> What is science?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q1">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="science" class="optionsnumber">subject</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="animal" class="optionsnumber"> animal</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="dinosaur" class="optionsnumber"> dinosaur</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="hill" class="optionsnumber">hill</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 2 -->
                    <?php if ($_SESSION['answered_q1'] && !$_SESSION['answered_q2']): ?>
                        <div class="question">
                            <p><strong>Question 2:</strong> What is a dog?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q2">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="subject" class="optionsnumber">A subject</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="river" class="optionsnumber">A river</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="animal" class="optionsnumber">An animal</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="hill" class="optionsnumber">A hill</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 3 -->
                    <?php if ($_SESSION['answered_q2'] && !$_SESSION['answered_q3']): ?>
                        <div class="question">
                            <p><strong>Question 3:</strong> What is a dinosaur?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q3">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="pet" class="optionsnumber">A pet</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="chair" class="optionsnumber">A chair</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="buffalo" class="optionsnumber">A buffalo</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="fame" class="optionsnumber">Fame</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 4 -->
                    <?php if ($_SESSION['answered_q3'] && !$_SESSION['answered_q4']): ?>
                        <div class="question">
                            <p><strong>Question 4:</strong> What is a river?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q4">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="solid" class="optionsnumber">Solid</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="liquid" class="optionsnumber">Liquid</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="air" class="optionsnumber">Air</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="hill" class="optionsnumber">A hill</button></div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Question 5 -->
                    <?php if ($_SESSION['answered_q4'] && !$_SESSION['answered_q5']): ?>
                        <div class="question">
                            <p><strong>Question 5:</strong> What is Earth?</p>
                            <form method="POST">
                                <input type="hidden" name="question" value="q5">
                                <div class="options">
                                    <div class="ansbox"><button type="submit" name="answer" value="alien" class="optionsnumber">An alien</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="planet" class="optionsnumber">A planet</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="eye" class="optionsnumber">An eye</button></div>
                                    <div class="ansbox"><button type="submit" name="answer" value="nose" class="optionsnumber">A nose</button></div>
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
    <script>

    </script>

</body>

</html>