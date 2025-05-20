<?php

session_start();

unset($_SESSION['points']);
unset($_SESSION['answered_q1']);
unset($_SESSION['answered_q2']);
unset($_SESSION['answered_q3']);
unset($_SESSION['answered_q4']);
unset($_SESSION['answered_q5']);

exit();
