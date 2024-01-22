<?php include "database.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rating</title>
    <link rel="stylesheet" href="css/homepage.css">
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
</header>

<!--        SPRAWDZENIE CZY UZYTKOWNIK JEST ZALOGOWANY-->


<!--WYSWEITLANIE RANKINGU-->
<h1>Rating</h1>
<?php
$query = "SELECT * FROM points ORDER BY points DESC";
$result = mysqli_query($db, $query);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Username</th><th>Quiz</th><th>Points</th></tr> ';
        while ($row = $result->fetch_assoc()) {
            $id = $row['user_id'];
            echo '<tr>';
            $selectquery = "SELECT username FROM users WHERE id='$id'";
            $selectresult = mysqli_query($db, $selectquery);
            $select = $selectresult->fetch_assoc();
            echo '<td>' . $select['username'] . '</td>';
            echo '<td>' . $row['quiz_id'] . '</td>';
            echo '<td>' . $row['points'] . '</td>';
            echo '</tr>';
            ?>
            </table>
<?php
        }
    }
    ?>
<!--WYSWEITLANIE RANKINGU-->


</body>
</html>
