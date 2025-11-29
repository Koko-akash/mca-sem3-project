<?php
session_start();
require_once "db/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch user's subjects
$sql = "SELECT * FROM prj_study_subjects WHERE user_id=$user_id ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">

<style>
    body {
        font-family: "Poppins", sans-serif;
        background: #f9f5ff; /* soft pastel lavender */
        margin: 0;
        padding: 0;
        color: #444;
    }

    .container {
        width: 92%;
        max-width: 900px;
        margin: auto;
        padding-top: 40px;
    }

    /* Top Bar */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    h2 {
        font-size: 28px;
        font-weight: 700;
        color: #6a4caf; /* pastel purple */
    }

    .logout-btn {
        background: #ff7b7b;
        padding: 8px 15px;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
        transition: 0.25s;
        box-shadow: 0px 3px 6px rgba(255, 123, 123, 0.3);
    }

    .logout-btn:hover {
        background: #ff5c5c;
    }

    /* Add Subject Button */
    .add-btn {
        display: inline-block;
        background: #8ec5fc;
        background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
        padding: 12px 18px;
        color: #2b2b2b;
        text-decoration: none;
        border-radius: 12px;
        margin-bottom: 20px;
        font-weight: 600;
        transition: 0.25s;
        box-shadow: 0px 4px 10px rgba(160, 200, 255, 0.4);
    }

    .add-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0px 6px 14px rgba(150, 200, 255, 0.5);
    }

    /* Subject Card */
    .subject-card {
        background: #ffffff;
        padding: 20px;
        margin-bottom: 18px;
        border-radius: 18px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
        transition: 0.25s;
        border: 1px solid #eee;
    }

    .subject-card:hover {
        transform: translateY(-4px);
        box-shadow: 0px 6px 18px rgba(0,0,0,0.10);
    }

    .subject-title {
        font-size: 22px;
        font-weight: 700;
        color: #6a4caf;
    }

    .subject-info {
        font-size: 15px;
        margin-top: 8px;
        color: #555;
        line-height: 1.5;
    }

    .subject-actions a {
        text-decoration: none;
        font-weight: bold;
        color: #4c8bf5;
        margin-right: 18px;
        transition: 0.2s;
    }

    .subject-actions a:hover {
        color: #1f6ff0;
        text-decoration: underline;
    }

    /* Messages */
    .msg-success {
        background: #e2ffe8;
        color: #2f9e44;
        padding: 10px 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        border-left: 5px solid #2f9e44;
        font-weight: 600;
    }

    .msg-error {
        background: #ffe2e2;
        color: #d63031;
        padding: 10px 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        border-left: 5px solid #d63031;
        font-weight: 600;
    }

    .dash-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    }

    /* Responsive */
    @media (max-width: 768px) {
        .top-bar {
            flex-direction: column;
            gap: 12px;
            text-align: center;
        }
        .add-btn, .logout-btn {
            width: 100%;
            text-align: center;
        }
        .dash-logo {
        width: 36px;
        height: 36px;
        }
    }
</style>

</head>
<body>

<div class="container">

    <div class="top-bar">
        <div style="display:flex; align-items:center; gap:12px;">
        <img src="assets/ScorePath_M2.png" alt="ScorePath Logo" class="dash-logo">
        <h2 style="margin:0;">Welcome, <?php echo $name; ?> 👋</h2>
        </div>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


    <!-- Session Messages -->
    <?php
    if (isset($_SESSION["success"])) {
        echo "<p class='msg-success'>{$_SESSION['success']}</p>";
        unset($_SESSION["success"]);
    }
    if (isset($_SESSION["error"])) {
        echo "<p class='msg-error'>{$_SESSION['error']}</p>";
        unset($_SESSION["error"]);
    }
    ?>

    <h3 style="color:#6a4caf; font-size:22px; margin-bottom:10px;">Your Subjects 📘</h3>

    <a href="add_subject.php" class="add-btn">➕ Add New Subject</a>

    <?php
    if (mysqli_num_rows($result) > 0) {

        while ($sub = mysqli_fetch_assoc($result)) {

            // Fetch prediction
            $sid = $sub["id"];
            $sql_pred = "SELECT predicted_score FROM prj_study_predictions 
                         WHERE subject_id=$sid 
                         ORDER BY id DESC LIMIT 1";
            $pred_res = mysqli_query($conn, $sql_pred);

            $last_pred = "No prediction yet";
            if (mysqli_num_rows($pred_res) == 1) {
                $p = mysqli_fetch_assoc($pred_res);
                $last_pred = $p["predicted_score"] . "/100";
            }

            echo "
            <div class='subject-card'>
                <div class='subject-title'>{$sub['subject_name']}</div>

                <div class='subject-info'>
                    📌 Goal: {$sub['syllabus_goal']} <br>
                    🔮 Predicted Score: <strong>$last_pred</strong>
                </div>

                <br>

                <div class='subject-actions'>
                    <a href='subject.php?id={$sub['id']}'>📂 Open Workspace</a>
                    <a href='edit_subject.php?id={$sub['id']}'>✏️ Edit</a>
                </div>
            </div>
            ";
        }

    } else {
        echo "<p>No subjects added yet. Start by adding one!</p>";
    }
    ?>

</div>

</body>
</html>
