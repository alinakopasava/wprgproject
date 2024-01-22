<?php include "database.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="css/homepage.css">

<!--    TIMER-->

    <script>
        var timeLimit = 30;
        var timerInterval;

        function startTimer() {
            var timerElement = document.getElementById('timer');
            var timeRemaining = timeLimit;

            timerInterval = setInterval(function() {
                timeRemaining--;
                timerElement.textContent = formatTime(timeRemaining);


                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    submitAnswer();
                }
            }, 1000);
        }

        function formatTime(seconds) {
            var minutes = Math.floor(seconds / 60);
            var remainingSeconds = seconds % 60;

            var formattedTime =
                (minutes < 10 ? '0' + minutes : minutes) +
                ':' +
                (remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds);

            return formattedTime;
        }

        function submitAnswer() {
            document.getElementById('next_question').click();
        }
    </script>
<!--TIMER-->
</head>

<body>
<header>
    <div class="header-left">
        <h1 class="text-header">QuizWhiz</h1><br><br><br>
    </div>
    <div class="header-right">

        <!--        SPRAWDZENIE CZY UZYTKOWNIK JEST ZALOGOWANY-->

        <?php
        if(isset($_COOKIE['username']))
        {
        echo '<a href="homepage.php"  class="but1">Home</a>';
        ?>
        <a href="rating.php" class="but1">Rating</a>
        <a href="login.php" class="but1">Log out (<?php echo $_COOKIE['username'];  ?>)</a>
            <a href="homepage.php?deleteaccount=1" class="but1">Delete account</a>

            <?php
        }
        else
        {
          header("Location: login.php");
          exit;
        }
        ?>
    </div>
    <!--        SPRAWDZENIE CZY UZYTKOWNIK JEST ZALOGOWANY-->


</header>

<!--  USUNIECIE KONTA-->

<?php
if(isset($_GET['deleteaccount']) && $_GET['deleteaccount'] == 1)
{
   $id = $_COOKIE['userid'];
   $deleteQuery = "DELETE FROM users WHERE id='$id'";
   $deleteResult = mysqli_query($db, $deleteQuery);
   $deleteQuery1 = "DELETE FROM points WHERE user_id='$id'";
   $deleteResult1 = mysqli_query($db, $deleteQuery1);

   if($deleteResult && $deleteResult1)
   {
       echo "Your account was deleted succesfully!";
       $username = $_COOKIE['username'];
       setcookie('username', $username, time()-86400);
       exit;
   }

}
/////USUNIECIE KONTA


/////WYNIKI QUIZU

if(isset($_GET['results']))
{
    echo '<h1>Your results: </h1><br>';
    echo '<h1>' . $_GET['results'] . '</h1>';

}
/////WYNIKI QUIZU


/////QUIZ

else
{

if (isset($_GET['quiz_id']) && !(isset($_GET['results']))) {
    $id = $_GET['quiz_id'];
    $SESSION['quiz_id'] = $id;
    $number = "SELECT COUNT(id) AS numberOfQuestions FROM questions WHERE quiz_id=$id";
    $numberResult = mysqli_query($db, $number);
    $number1 = $numberResult->fetch_assoc();
    if (!isset($_SESSION['question_number']) || $_SESSION['question_number'] > $number1['numberOfQuestions']) {
        $_SESSION['question_number'] = 1;
    }

    $qn = $_SESSION['question_number'];

    $questionsQuery = "SELECT * FROM questions WHERE quiz_id = $id AND quest_number = $qn";
    $answersQuery = "SELECT * FROM answers WHERE quiz_id = $id and quest_number = $qn";

    $questionsResult = mysqli_query($db, $questionsQuery);
    $answersResult = mysqli_query($db, $answersQuery);

    /////////////WYSWIETLANIE PYTAN
    if ($questionsResult->num_rows > 0) {
        $question = $questionsResult->fetch_assoc();
        echo '<h3 class="questions">' . $qn .'.'.' '.  $question['question_text'] .  '</h3><br><br>';

        if ($answersResult->num_rows > 0) {
            $i = 1;

            while($answers = $answersResult->fetch_assoc())
            {
                $buttonName = 'answer' . $i;

                if($answers['is_correct'] == 1)
                {
                    $isCorrect = 1;
                    $correct = $i;

                    $isCorrect = 0;
                }
                else
                {
                    $isCorrect = 0;
                }

                echo '<form action="" method="post">';
                echo '<input type="submit" class="but12" id="next_question" name="' . $buttonName .'" value="' . $answers['answer_text'] . '">';
                $i++;
            }
        }
        /////////////WYSWIETLANIE PYTAN


        echo '</form>';

        echo '<form action="" method="post">';
        echo '<input type="hidden" name="quiz_id" value="' . $id . '">';
        echo '<input type="submit" class="but12" id="next_question1" name="next_question" value="Next Question">';
        echo '</form>';


        echo '<div class="timer" id="timer"></div>';
        echo '<script>startTimer();</script>';
    } else {

        echo 'No questions found.';
    }

    if (isset($_POST['next_question'])) {

        submitAnswer();
    }
    /////////////NACISNIECIE PIERWSZEJ ODPOWIEDZI

    if (isset($_POST['answer1'])) {
        $userid = $_COOKIE['userid'];

        $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
        $checkresult = mysqli_query($db, $check);

        if(mysqli_num_rows($checkresult)<0)
        {
            $new = "INSERT INTO points (user_id, quiz_id, points) VALUES ('$_COOKIE[userid]', '$_GET[quiz_id]', '0')";
            $newresult = mysqli_query($db, $new);
        }


        if ($correct == 1) {
            $userid = $_COOKIE['userid'];

            if(mysqli_num_rows($checkresult) == 0)
            {
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }
            else
            {
                $insert = "UPDATE points SET quiz_id='$_GET[quiz_id]' WHERE user_id='$_COOKIE[userid]' AND points = '0'";
                $insertresult = mysqli_query($db, $insert);
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }

        }

        $_SESSION['question_number']++;


        if ($_SESSION['question_number'] > $number1['numberOfQuestions'] ) {
            $resultpoints = "SELECT points FROM points where user_id='$userid' AND quiz_id='$id'";
            $resultpoints1 = mysqli_query($db, $resultpoints);
            $result2 = $resultpoints1->fetch_assoc();
            $points = $result2['points'];
            session_destroy();
            header("Location: homepage.php?quiz_id=" . $id . "&results=" . $points);
            exit;

        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    /////////////NACISNIECIE PIERWSZEJ ODPOWIEDZI


    /////////////NACISNIECIE DRUGIEJ ODPOWIEDZI

    else if (isset($_POST['answer2'])) {
        $userid = $_COOKIE['userid'];

        $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
        $checkresult = mysqli_query($db, $check);

        /////////SPRAWDZAMY CZY ISTNIEJE QUIZ W TABLICY POINTS
        if(mysqli_num_rows($checkresult)<0)
        {
            $new = "INSERT INTO points (user_id, quiz_id, points) VALUES ('$_COOKIE[userid]', '$_GET[quiz_id]', '0')";
            $newresult = mysqli_query($db, $new);
        }

        /////////SPRAWDZAMY CZY ISTNIEJE QUIZ W TABLICY POINTS


        ////////////JEZELI POPRAWNA ODPOWIEDZ
        if ($correct == 2) {
            $userid = $_COOKIE['userid'];

            if(mysqli_num_rows($checkresult) == 0)
            {
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }
            else
            {
                $insert = "UPDATE points SET quiz_id='$_GET[quiz_id]' WHERE user_id='$_COOKIE[userid]' AND points = '0'";
                $insertresult = mysqli_query($db, $insert);
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }
        }

        ////////////JEZELI POPRAWNA ODPOWIEDZ



        $_SESSION['question_number']++; //zwiekszamy numer pytania


        //////////JAK PYTANIA SIE SKONCZYLY
        if ($_SESSION['question_number'] > $number1['numberOfQuestions'] ) {
            $resultpoints = "SELECT points FROM points where user_id='$_COOKIE[userid]' AND quiz_id='$id'";
            $resultpoints1 = mysqli_query($db, $resultpoints);
            $result2 = $resultpoints1->fetch_assoc();
            $points = $result2['points'];
            if($points=="0")
            {
                $points = 0;
            }
            session_destroy();
            header("Location: homepage.php?quiz_id=" . $id . "&results=" . $points);
            exit;

        }
        while (ob_get_level()) {
            ob_end_clean();
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    //////////JAK PYTANIA SIE SKONCZYLY


    /////////////NACISNIECIE DRUGIEJ ODPOWIEDZI


    /////////////NACISNIECIE TRZECIEJ ODPOWIEDZI

    if (isset($_POST['answer3'])) {
        $userid = $_COOKIE['userid'];

        $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
        $checkresult = mysqli_query($db, $check);

        if(mysqli_num_rows($checkresult) == 0)
        {
            $new = "INSERT INTO points (user_id, quiz_id, points) VALUES ('$_COOKIE[userid]', '$_GET[quiz_id]', '0')";
            $newresult = mysqli_query($db, $new);
        }

        if ($correct == 3) {
            $userid = $_COOKIE['userid'];

            $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
            $checkresult = mysqli_query($db, $check);

            if(mysqli_num_rows($checkresult)>0)
            {
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }
            else
            {


                $insert = "UPDATE points SET quiz_id='$_GET[quiz_id]' WHERE user_id='$_COOKIE[userid]' AND points = '0'";
                $insertresult = mysqli_query($db, $insert);
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }

        }


        $_SESSION['question_number']++;



        if ($_SESSION['question_number'] > $number1['numberOfQuestions'] ) {
            $resultpoints = "SELECT points FROM points where user_id='$userid' AND quiz_id='$id'";
            $resultpoints1 = mysqli_query($db, $resultpoints);
            $result2 = $resultpoints1->fetch_assoc();
            $points = $result2['points'];
            session_destroy();
            header("Location: homepage.php?quiz_id=" . $id . "&results=" . $points);
            exit;
        }


        while (ob_get_level()) {
            ob_end_clean();
        }


        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    /////////////NACISNIECIE TRZECIEJ ODPOWIEDZI


    /////////////NACISNIECIE CZWARTEJ ODPOWIEDZI

    if (isset($_POST['answer4'])) {
      $userid = $_COOKIE['userid'];

        $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
        $checkresult = mysqli_query($db, $check);

        if(mysqli_num_rows($checkresult)<0)
        {
            $new = "INSERT INTO points (user_id, quiz_id, points) VALUES ('$_COOKIE[userid]', '$_GET[quiz_id]', '0')";
            $newresult = mysqli_query($db, $new);
        }


        if ($correct == 4) {
            $userid = $_COOKIE['userid'];

            $check = "SELECT * FROM points WHERE quiz_id='$_GET[quiz_id]' AND user_id='$_COOKIE[userid]'";
            $checkresult = mysqli_query($db, $check);

            if(mysqli_num_rows($checkresult) == 0)
            {
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }
            else
            {

                $insert = "UPDATE points SET quiz_id='$_GET[quiz_id]' WHERE user_id='$_COOKIE[userid]' AND points = '0'";
                $insertresult = mysqli_query($db, $insert);
                $rating = "UPDATE points SET points = points + 1 WHERE quiz_id = '$_GET[quiz_id]' AND user_id = '$_COOKIE[userid]'";
                $ratingResult = mysqli_query($db, $rating);
            }

        }


        $_SESSION['question_number']++;


        if ($_SESSION['question_number'] > $number1['numberOfQuestions'] ) {
            $resultpoints = "SELECT points FROM points where user_id='$userid' AND quiz_id='$id'";
            $resultpoints1 = mysqli_query($db, $resultpoints);
            $result2 = $resultpoints1->fetch_assoc();
            $points = $result2['points'];
            session_destroy();
            header("Location: homepage.php?quiz_id=" . $id . "&results=" . $points);
            exit;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

/////////////NACISNIECIE CZWARTEJ ODPOWIEDZI



///////////QUIZ
else
{
    ?>

<!--    <div class="container">-->
<!--        <h1 class="head">Let's QUIZ!</h1>-->
<!--        <div class="search-bar">-->
<!--            <input type="text" placeholder="Find Your Quiz">-->
<!--            <button class="but12" type="submit"><i class="fas fa-search"></i></button>-->
<!--        </div>-->
<!---->
<!--    </div>-->



<!--    WYSWITLANIE LISTY QUIZOW-->
    <?php

    $query = "SELECT * FROM quiz ORDER BY complexity";
    $result = mysqli_query($db, $query);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Quiz</th><th>Complexity</th><th>Number of questions</th></tr> ';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td><a href="homepage.php?quiz_id=' . $row['id']  . '">' . $row['topic'] . '</a></td>';
            echo '<td>' . $row['complexity'] . '</td>';
            echo '<td>' . $row['number_of_questions'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo 'Brak danych.';
    }
}
}
?>
<!--    WYSWITLANIE LISTY QUIZOW-->
</body>
</html>



