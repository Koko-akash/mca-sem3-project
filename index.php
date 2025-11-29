<?php
session_start();
require_once "db/db.php";

$message = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check empty
    if (empty($email) || empty($password)) {
        $message = "⚠️ Both fields required!";
    } else {

        // Check for user
        $sql = "SELECT * FROM prj_study_users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];

                header("Location: dashboard.php");
                exit();

            } else {
                $message = "❌ Incorrect password!";
            }

        } else {
            $message = "❌ No account found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">
</head>

<body class="auth-body">

    <div class="auth-container">

        <!-- Logo -->
        <img src="assets/ScorePath_M2.png" alt="ScorePath Logo" class="auth-logo" 
        style="
        width: 100px;
        height: auto;
        display: block;
        margin: 0 auto 18px auto;
        ">


        <h2 class="auth-title">Login</h2>

        <?php if (!empty($message)) : ?>
            <p class="auth-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <input type="email" name="email" placeholder="Email" class="auth-input">
            <input type="password" name="password" placeholder="Password" class="auth-input">

            <button type="submit" name="login" class="auth-btn">Login</button>
        </form>
        <br>
        <!-- JavaScript use -->
        <p style="text-align:right; margin-top: -10px; margin-bottom: 10px;">
        <a href="#" id="togglePass" style="font-size: 13px;color: black;">Show Password</a>
        </p>

        <p class="auth-link">Don't have an account? 
            <a href="signup.php">Signup</a>
        </p>

    </div>

    <!-- JavaScript Animation -->
    <script>
    // Js animation
    document.addEventListener("DOMContentLoaded", () => {
        const box = document.querySelector(".auth-container");
        box.style.opacity = "0";
        box.style.transform = "translateY(20px)";

        setTimeout(() => {
            box.style.transition = "0.6s ease";
            box.style.opacity = "1";
            box.style.transform = "translateY(0)";
        }, 50);
    });

    // Js show password
    document.getElementById("togglePass").addEventListener("click", function(e){
    e.preventDefault();
    const pass = document.querySelector("input[name='password']");
    if (pass.type === "password") {
        pass.type = "text";
        this.textContent = "Hide Password";
    } else {
        pass.type = "password";
        this.textContent = "Show Password";
    }
    });

    // Enter-key animaton
    document.querySelectorAll(".auth-input").forEach(input => {
    input.addEventListener("focus", () => {
        input.style.transition = "0.3s";
        input.style.boxShadow = "0 0 8px rgba(0, 150, 255, 0.4)";
    });
    input.addEventListener("blur", () => {
        input.style.boxShadow = "none";
    });
    });
    </script>

</body>
</html>

