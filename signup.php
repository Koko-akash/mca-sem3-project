<?php
require_once "db/db.php";

$message = "";

if (isset($_POST['signup'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check empty fields
    if (empty($name) || empty($email) || empty($password)) {
        $message = "⚠️ All fields are required!";
    } else {

        // Check if email already exists
        $check = "SELECT id FROM prj_study_users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $message = "❌ Email already registered!";
        } else {

            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql = "INSERT INTO prj_study_users (name, email, password)
                    VALUES ('$name', '$email', '$hashed')";

            if (mysqli_query($conn, $sql)) {
                $message = "✅ Registration successful! Redirecting...";
                header("refresh:2; url=index.php"); // send to login
            } else {
                $message = "❌ Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!-- SIMPLE HTML FORM -->
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon-32x32.png">

    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #ff849d, #db9dff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-box {
            background: #fff2fdff;
            width: 360px;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            color: #444;
            font-size: 26px;
            font-weight: 700;
        }

        .message {
            margin-bottom: 15px;
            font-size: 15px;
            color: #d9534f;
        }

        form input {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            font-size: 15px;
            background: #fff5ffff;
            transition: 0.2s;
        }

        form input:focus {
            border-color: #ff8bf0ff;
            background: #fff;
        }

        button {
            margin-top: 18px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #ff7bf4ff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #f25cd9ff;
        }

        .switch-link {
            margin-top: 18px;
            font-size: 14px;
        }

        .switch-link a {
            color: #ff63f2ff;
            text-decoration: none;
            font-weight: 600;
        }

        .switch-link a:hover {
            text-decoration: underline;
        }
        @keyframes rippleAnim {
            from { transform: scale(0); opacity: 1; }
            to   { transform: scale(2); opacity: 0; }
        }
    </style>
</head>

<body>

<div class="auth-box">

    <!-- Logo -->
    <img src="assets/ScorePath_M2.png" alt="ScorePath Logo" class="auth-logo" 
    style="
    width: 100px;
    height: auto;
    display: block;
    margin: 0 auto 18px auto;
    ">
    <p style="font-style: italic; font-size: 15px; text-align: center; color: #3b003f; letter-spacing: 0.5px; margin-top: 10px; opacity: 0.9;">
        Welcome to ScorePath
    </p>
    
    <p style="font-style: italic; font-size: 15px; text-align: center; color: #3b003f; letter-spacing: 0.5px; margin-top: 10px; opacity: 0.9;">
        Your daily path to peak performance.
    </p>
    <br><br>
    <h2>Create Account</h2>

    <p class="message"><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name">
        <input type="email" name="email" placeholder="Email Address">
        <input type="password" name="password" placeholder="Password">

        <button type="submit" name="signup">Signup</button>
    </form>

    <p class="switch-link">
        Already have an account? <a href="index.php">Login</a>
    </p>
</div>

<!-- JavaScript part -->
<script>
// -------------------------
// 1. Fade-in Animation
// -------------------------
document.addEventListener("DOMContentLoaded", () => {
    const box = document.querySelector(".auth-box");
    box.style.opacity = "0";
    box.style.transform = "translateY(20px)";

    setTimeout(() => {
        box.style.transition = "0.6s";
        box.style.opacity = "1";
        box.style.transform = "translateY(0)";
    }, 80);
});

// -------------------------
// 2. Input Glow Effect
// -------------------------
const inputs = document.querySelectorAll(".auth-box input");

inputs.forEach(input => {
    input.addEventListener("focus", () => {
        input.style.boxShadow = "0 0 10px rgba(255, 117, 240, 0.5)";
    });

    input.addEventListener("blur", () => {
        input.style.boxShadow = "none";
    });
});

// -------------------------
// 3. Show/Hide Password
// -------------------------
const pwdInput = document.querySelector("input[name='password']");

if (pwdInput) {
    const toggleBtn = document.createElement("span");
    toggleBtn.innerHTML = "👁️";
    toggleBtn.style.position = "absolute";
    toggleBtn.style.right = "12px";
    toggleBtn.style.top = "53%";
    toggleBtn.style.cursor = "pointer";
    toggleBtn.style.userSelect = "none";
    toggleBtn.style.fontSize = "18px";
    toggleBtn.style.opacity = "0.6";

    const wrapper = pwdInput.parentElement;
    wrapper.style.position = "relative";
    wrapper.appendChild(toggleBtn);

    toggleBtn.addEventListener("click", () => {
        const type = pwdInput.type === "password" ? "text" : "password";
        pwdInput.type = type;
        toggleBtn.innerHTML = type === "password" ? "👁️" : "🙈";
    });
}

// -------------------------
// 4. Style success/error messages
// -------------------------
const msg = document.querySelector(".message");
if (msg && msg.innerText.trim() !== "") {
    if (msg.innerText.includes("❌")) {
        msg.style.color = "#d9534f";
    } else if (msg.innerText.includes("✅")) {
        msg.style.color = "#28a745";
    }
    msg.style.fontWeight = "600";
}

// -------------------------
// 5. Button click ripple
// -------------------------
const btn = document.querySelector("button");
btn.addEventListener("click", (e) => {
    let ripple = document.createElement("span");
    ripple.classList.add("ripple");
    ripple.style.position = "absolute";
    ripple.style.borderRadius = "50%";
    ripple.style.background = "rgba(255,255,255,0.7)";
    ripple.style.pointerEvents = "none";
    ripple.style.width = ripple.style.height = "120px";
    ripple.style.left = e.offsetX - 60 + "px";
    ripple.style.top = e.offsetY - 60 + "px";
    ripple.style.animation = "rippleAnim 0.6s ease-out";
    btn.style.position = "relative";
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
});
</script>

</body>
</html>
