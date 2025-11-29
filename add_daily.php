<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$subject_id = $_GET['id'];
$message = "";

// Handle form submission
if (isset($_POST['submit'])) {

    $date = $_POST['date'];
    $study = floatval($_POST['study_hours']);
    $sleep = floatval($_POST['sleep_hours']);
    $topics = intval($_POST['topics']);
    $assign = intval($_POST['assignments']);

    if (empty($date)) {
        $message = "⚠️ Please select a date!";
    } else {
        $sql = "INSERT INTO prj_study_daily_data 
                (subject_id, log_date, study_hours, sleep_hours, topics_covered, assignments_done)
                VALUES 
                ($subject_id, '$date', $study, $sleep, $topics, $assign)";

        if (mysqli_query($conn, $sql)) {
            header("Location: subject.php?id=$subject_id");
            exit();
        } 
        else {
            $message = "❌ Failed to add daily data!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Daily Data</title>
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">
    <style>
        /* Import soft, modern font */
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap');

        body {
            font-family: 'Nunito', sans-serif;
            background: #fef6f9; /* soft pastel pink background */
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 90%;
        }

        h2 {
            text-align: center;
            color: #e884fcff; /* pastel accent color */
            margin-bottom: 20px;
        }

        p.message {
            text-align: center;
            color: #ff6b81; /* message in pastel red */
            font-weight: 600;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #6b5b95; /* soft purple pastel */
        }

        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #dcd6f7;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        input[type="date"]:focus,
        input[type="number"]:focus {
            border-color: #a3c4f3;
            box-shadow: 0 0 5px rgba(163,196,243,0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #e5a0e7ff; /* pastel teal button */
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #d78ccdff;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #e884fcff;
            font-weight: 600;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Today's Data</h2>

    <?php if($message != ""): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Date:</label>
        <input type="date" name="date">

        <label>Study Hours:</label>
        <input type="number" step="0.1" name="study_hours">

        <label>Sleep Hours:</label>
        <input type="number" step="0.1" name="sleep_hours">

        <label>Topics Covered:</label>
        <input type="number" name="topics">

        <label>Assignments Done:</label>
        <input type="number" name="assignments">

        <button type="submit" name="submit">Save</button>
    </form>

    <a href="subject.php?id=<?php echo $subject_id; ?>">⬅ Back</a>
</div>

</body>
</html>