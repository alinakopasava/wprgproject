<?php include "database.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/homepage.css">
</head>
<body>
<h1 style="text-align: center; margin-top:460px">Admin Panel</h1>

<?php
///////////////SUKCES: EDYCJA

if (isset($_GET['editquiz']) && $_GET['editquiz'] == 1) {
    echo "Your quiz was edited successfully!";
    exit;
}
///////////////SUKCES: EDYCJA


///////////////NIE MA TAKIEGO QUIZU: EDYCJA

if (isset($_GET['editquiz']) && $_GET['editquiz'] == 0) {
    echo "Your quiz wasn't edited successfully. Please try again";
    exit;
}
///////////////NIE MA TAKIEGO QUIZU: EDYCJA


///////////////NIE MA TAKIEGO QUIZU: USUNIECIE

if (isset($_GET['deletequiz']) && $_GET['deletequiz'] == 0) {
    echo "Your quiz wasn't deleted successfully. Please try again";
    exit;
}
///////////////NIE MA TAKIEGO QUIZU: USUNIECIE

///////////////SUKCES: USUNIECIE

if (isset($_GET['deletequiz']) && $_GET['deletequiz'] == 1) {
    echo "Your quiz was deleted successfully!";
    exit;
}
///////////////SUKCES: USUNIECIE


///////////////SUKCES: DODAWANIE

if (isset($_GET['congr_id']) && $_GET['congr_id'] == $_SESSION['quiz_id']) {
    echo "Your quiz was added successfully!";
}

///////////////SUKCES: USUNIECIE


///////////////DODAWANIE PYTAN

elseif (isset($_GET['quiz_id']) && $_GET['quiz_id'] == $_SESSION['quiz_id']) {
    echo "<div><h2 class='add'>Add questions to your quiz</h2></div>";

    $qn = $_SESSION['qn'];

    echo '<form class="n" action="#" method="post">';

    for ($i = 0; $i < $qn; $i++) {
        echo "<h3>Question " . ($i+1) . "</h3>";
        echo 'Question: <input type="text" name="question_text[]" required><br><br>';
        echo 'Answer 1: <input type="text" name="answer1[]" required><br><br>';
        echo 'Answer 2: <input type="text" name="answer2[]" required><br><br>';
        echo 'Answer 3: <input type="text" name="answer3[]" required><br><br>';
        echo 'Answer 4: <input type="text" name="answer4[]" required><br><br>';
        echo 'Correct Answer: <input type="number" name="correct_answer[]" min="1" max="4" required><br><br>';
    }

    echo '<input type="submit" class="but12" name="add_question" value="Add Questions">';
    echo '</form>';

    if (isset($_POST['add_question'])) {
        $questions = $_POST['question_text'];
        $answers1 = $_POST['answer1'];
        $answers2 = $_POST['answer2'];
        $answers3 = $_POST['answer3'];
        $answers4 = $_POST['answer4'];
        $correctAnswers = $_POST['correct_answer'];
        $numQuestions = $_SESSION['qn'];

        for ($i = 0; $i < $numQuestions; $i++) {
            $questionText = $questions[$i];
            $questNumber = $i + 1;
            $insertQuestionQuery = "INSERT INTO questions (quiz_id, question_text, quest_number) VALUES ('$_SESSION[quiz_id]', '$questionText', '$questNumber')";
            mysqli_query($db, $insertQuestionQuery);
            $questionId = mysqli_insert_id($db);

            $correctAnswer = $correctAnswers[$i];

            $answer1 = $answers1[$i];
            $answer2 = $answers2[$i];
            $answer3 = $answers3[$i];
            $answer4 = $answers4[$i];

            $insertAnswer1Query = "INSERT INTO answers (question_id, quest_number, quiz_id, answer_text, is_correct) VALUES ('$questionId', '$questNumber', '$_SESSION[quiz_id]', '$answer1', " . ($correctAnswer == 1 ? '1' : '0') . ")";
            mysqli_query($db, $insertAnswer1Query);

            $insertAnswer2Query = "INSERT INTO answers (question_id, quest_number, quiz_id, answer_text, is_correct) VALUES ('$questionId', '$questNumber', '$_SESSION[quiz_id]', '$answer2', " . ($correctAnswer == 2 ? '1' : '0') . ")";
            mysqli_query($db, $insertAnswer2Query);

            $insertAnswer3Query = "INSERT INTO answers (question_id, quest_number, quiz_id, answer_text, is_correct) VALUES ('$questionId', '$questNumber', '$_SESSION[quiz_id]', '$answer3', " . ($correctAnswer == 3 ? '1' : '0') . ")";
            mysqli_query($db, $insertAnswer3Query);

            $insertAnswer4Query = "INSERT INTO answers (question_id, quest_number, quiz_id, answer_text, is_correct) VALUES ('$questionId', '$questNumber', '$_SESSION[quiz_id]', '$answer4', " . ($correctAnswer == 4 ? '1' : '0') . ")";
            mysqli_query($db, $insertAnswer4Query);
        }

        header("Location: admin.php?congr_id=" . $_SESSION['quiz_id']);
    }
}
///////////////DODAWANIE PYTAN
else
{
    ?>
<!--        WYSWIETLANIE DOSTEPNYCH RZECZY-->
    <h2>Add Quiz</h2>
    <form action="#" method="post">
        <label>Name:</label><br>
        <input type="text" name="topic" required><br>
        <label>Complexity:</label>
        <input type="text" name="complexity" required><br>
        <label>Number of Questions:</label>
        <input type="number" name="num_questions" min="1" required><br><br>
        <input type="submit" class="but12" name="add_quiz" value="Add">
    </form>

    <h2 class="nag">Delete Quiz</h2>
    <form class="cen" action="#" method="post">
        <label>Name: </label>
        <input type="text" name="topic" required><br><br>
        <input type="submit" class="but12" name="delete_quiz" value="Delete">
    </form>

    <h2>Edit Quiz</h2>
    <form action="#" method="post">
        <label>Name:</label><br>
        <input type="text" name="topic" placeholder="Enter name of the quiz" required><br><br>
        <label>Edited Name: </label>
        <input type="text" name="name" required><br><br>
        <label>Edited Complexity:</label>
        <input type="text" name="complexity" required><br>
        <input type="submit" class="but12" name="edit_quiz" value="Edit">
    </form>

    <!--        WYSWIETLANIE DOSTEPNYCH RZECZY-->

    <?php
}
///////////////UZYTKOWNIK DODAJE QUIZ

if (isset($_POST['add_quiz'])) {
    $topic = $_POST['topic'];
    $complexity = $_POST['complexity'];
    $numQuestions = $_POST['num_questions'];

    $addQuizQuery = "INSERT INTO quiz (topic, complexity, number_of_questions) VALUES ('$topic', '$complexity', '$numQuestions')";
    mysqli_query($db, $addQuizQuery);

    $quizId = mysqli_insert_id($db);
    $_SESSION['quiz_id'] = $quizId;
    $_SESSION['qn'] = $numQuestions;

    header("Location: admin.php?quiz_id=" . $_SESSION['quiz_id']);
}
///////////////UZYTKOWNIK DODAJE QUIZ


///////////////UZYTKOWNIK USUWA QUIZ

if (isset($_POST['delete_quiz'])) {
    $topic = $_POST['topic'];
    $selectquery = "SELECT id FROM quiz WHERE topic='$topic'";
    $selectqueryresult = mysqli_query($db, $selectquery);
    $select = $selectqueryresult->fetch_assoc();
    $id = $select['id'];
    if($id)
    {
        $deleteQuizQuery = "DELETE FROM quiz WHERE topic='$topic'";
        mysqli_query($db, $deleteQuizQuery);
        $deleteQuizQuery1 = "DELETE FROM questions WHERE quiz_id='$id'";
        mysqli_query($db, $deleteQuizQuery1);
        $deleteQuizQuery2 = "DELETE FROM answers WHERE quiz_id='$id'";
        mysqli_query($db, $deleteQuizQuery2);

        header("Location: admin.php?deletequiz=1");
    }

    else
    {
        header("Location: admin.php?deletequiz=0");

    }
}

///////////////UZYTKOWNIK USUWA QUIZ


///////////////UZYTKOWNIK EDUTYJE QUIZ

if (isset($_POST['edit_quiz'])) {
    $topic = $_POST['topic'];
    $name = $_POST['name'];
    $complexity = $_POST['complexity'];

    $selectquery = "SELECT id FROM quiz WHERE topic='$topic'";
    $selectqueryresult = mysqli_query($db, $selectquery);
    $select = $selectqueryresult->fetch_assoc();
    $id = $select['id'];

    if ($id) {

        echo "dskdlskd";
        $editQuizQuery = "UPDATE quiz SET complexity='$complexity', topic='$name' WHERE id='$id'";
        $result = mysqli_query($db, $editQuizQuery);
        header("Location: admin.php?editquiz=1");
        }
    else
    {
        header("Location: admin.php?editquiz=0");
    }
///////////////UZYTKOWNIK EDYTUJE QUIZ

}
?>
</body>
</html>
