<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$subject_id = $_GET['id'];

// Fetch subject
$sql = "SELECT * FROM prj_study_subjects WHERE id=$subject_id LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("❌ Subject not found!");
}

$subject = mysqli_fetch_assoc($result);
$message = "";

// Update subject
if (isset($_POST['update'])) {

    $name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);

    $update = "UPDATE prj_study_subjects 
               SET subject_name='$name', syllabus_goal='$goal'
               WHERE id=$subject_id";

    if (mysqli_query($conn, $update)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "❌ Error updating subject!";
    }
}

// Delete subject
if (isset($_POST['delete'])) {
    $delete = "DELETE FROM prj_study_subjects WHERE id=$subject_id";

    if (mysqli_query($conn, $delete)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "❌ Error deleting subject!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #fdf1fdff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 450px;
            margin: 50px auto;
            background: #fdf7fdff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0px 4px 14px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        p {
            text-align: center;
            font-weight: 600;
            color: red;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0 18px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        form textarea {
            height: 120px;
            resize: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 12px;
            transition: 0.25s;
        }

        .btn-update {
            background: #e49ff5ff;
            color: white;
        }
        .btn-update:hover {
            background: #4c155aff;
        }

        .btn-delete {
            background: #ec8f8fff;
            color: white;
        }
        .btn-delete:hover {
            background: #792121ff;
        }

        a.back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            color: #2980b9;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Edit Subject</h2>

    <p><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="subject_name" value="<?php echo $subject['subject_name']; ?>">

        <textarea name="goal"><?php echo $subject['syllabus_goal']; ?></textarea>

        <button type="submit" name="update" class="btn btn-update">💾 Save Changes</button>
        <button type="submit" name="delete" class="btn btn-delete">🗑 Delete Subject</button>
    </form>

    <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>

</div>

</body>
</html>