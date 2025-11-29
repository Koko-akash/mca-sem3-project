<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if (isset($_POST['add'])) {

    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);
    $user_id = $_SESSION['user_id'];

    if (empty($subject_name)) {
        $message = "⚠️ Subject name required!";
    } else {
        $sql = "INSERT INTO prj_study_subjects (user_id, subject_name, syllabus_goal)
                VALUES ($user_id, '$subject_name', '$goal')";

        if (mysqli_query($conn, $sql)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "❌ Error adding subject";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title> 
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f6fd;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background: #fff;
            width: 420px;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0px 4px 18px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 26px;
            font-weight: 600;
        }

        p {
            font-size: 14px;
            color: red;
            font-weight: 600;
        }

        input, textarea {
            width: 90%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccd2e0;
            margin-top: 10px;
            background: #f9faff;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        input:focus, textarea:focus {
            border-color: #6a5acd;
            box-shadow: 0 0 6px rgba(106, 90, 205, 0.3);
        }

        textarea {
            height: 90px;
            resize: none;
        }

        button {
            margin-top: 20px;
            width: 95%;
            padding: 12px;
            background: #6a5acd;
            border: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: 0.2s ease;
        }

        button:hover {
            background: #5a4bcf;
            transform: scale(1.03);
        }

        a.back-btn {
            display: inline-block;
            margin-top: 18px;
            color: #444;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        a.back-btn:hover {
            text-decoration: underline;
            color: #2c3e50;
        }
    </style>

</head>
<body>

<div class="container">

    <h2>Add New Subject</h2>

    <p><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="subject_name" placeholder="Subject name">

        <textarea name="goal" placeholder="Syllabus goal (optional)"></textarea>

        <button type="submit" name="add">Add Subject</button>
    </form>

    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</div>

</body>
</html>