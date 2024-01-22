<?php
include 'database.php';
session_start();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="css/registration.css">

    </head>

<!--    FORMULARZ LOGOWANIA-->
    <body>
    <div class="container">
        <form action="#" method="post">
            <div class="title">Log in</div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <div class="underline"></div>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <div class="underline"></div>
            </div>
            <div class="input-box button">
                <input type="submit" name="submit" value="Continue">
            </div>
        </form>
        <div class="option">Don't have an account?
            <a href="registration.php">Sign up</a></div>
    </div>
    <!--    FORMULARZ LOGOWANIA-->

<!--PO NACISNIECJI SIGN IN -->

    <?php
    if(isset($_POST['submit'])) {
        $query1 = "SELECT id FROM users WHERE email='$_POST[email]'";
        $result1 = mysqli_query($db, $query1);


        $query2 = "SELECT id FROM users WHERE password='$_POST[password]'";
        $result2 = mysqli_query($db, $query2);

        if (mysqli_num_rows($result1) && mysqli_num_rows($result2))
        {
            $row1 = mysqli_fetch_assoc($result1);
            $id1 = $row1['id'];

            $row2 = mysqli_fetch_assoc($result2);
            $id2 = $row2['id'];
            if($id1 ==3 && $id1== $id2)
            {
                header("Location:admin.php");
                exit;
            }

            else if ($id1 != 3 && $id1 == $id2)
            {
                header("Location:homepage.php");
                $usernamequery1 = "SELECT username FROM users WHERE id='$id1'";
                $usernameresult1 = mysqli_query($db, $usernamequery1);
                $username1 = $usernameresult1->fetch_assoc();
                $username = $username1['username'];
                $id = $id1;
                setcookie('username', $username, time() + 86400);
                setcookie('userid', $id, time() + 86400);
                exit;
            }
        }
        else
        {
            echo "Wrong password or email. Try again";
        }
    }
    ?>

    <!--PO NACISNIECJI SIGN IN -->


    </body>
    </html>
