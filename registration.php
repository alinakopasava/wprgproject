<?php
include 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link rel="stylesheet" href="css/registration.css">

</head>
<body>

<!--FORMULARZ REJESTRACYJNY-->
<div class="container">
    <form action="#" method="post">
        <div class="title">Register Now</div>
        <div class="input-box underline">
            <input type="text" name="name" placeholder="Enter Your Name" required>
            <div class="underline"></div>
        </div>
        <div class="input-box">
            <input type="text" name="surname" placeholder="Enter Your Last Name" required>
            <div class="underline"></div>
        </div>
        <div class="input-box">
            <input type="email" name="email" placeholder="Enter Your Email" required>
            <div class="underline"></div>
        </div>
        <div class="input-box underline">
            <input type="text" name="username" placeholder="Set Username" required>
            <div class="underline"></div>
        </div>
        <div class="input-box">
            <input type="password" name="password" placeholder="Set Password" required>
            <div class="underline"></div>
        </div>
        <div class="input-box button">
            <input type="submit" name="submit" value="Continue">
        </div>
    </form>
    <div class="option">Already have an account?
        <a href="login.php">Sign in</a></div>

    <div class="alert alert-success" id="success" style="display:none">
        <strong>Success!</strong> Account was registered succesfully
    </div>
    <div class="alert alert-danger" id="fail" style="display:none">
        <strong>Username or email is already exists!</strong> Please try again
    </div>
</div>

<!--FORMULARZ REJESTRACYJNY-->



<!--KOD PO NACISNIECIU SIGN UP-->

<?php
if(isset($_POST['submit']))
{

    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username='$_POST[username]'");
    $check_email = mysqli_query($db, "SELECT * FROM users WHERE email='$_POST[email]'");

    if(!mysqli_num_rows($check_user) && !mysqli_num_rows($check_email))
    {
        mysqli_query($db, "INSERT INTO users (name, surname, email, username, password) VALUES ('$_POST[name]', '$_POST[surname]','$_POST[email]', '$_POST[username]','$_POST[password]')");
        ?>
<!--        POWIADOMIENIE-->

        <script>
            document.getElementById("success").style.display="block";
        </script>
    <?php
    }
    else
    {
    ?>
        <script>
            document.getElementById("fail").style.display="block";
        </script>
        <!--        POWIADOMIENIE-->

        <?php
    }


}
?>
<!--KOD PO NACISNIECIU SIGN UP-->

</body>

</html>


