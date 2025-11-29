<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$subject_id = $_GET['id'];

// Fetch subject info
$sql_subject = "SELECT * FROM prj_study_subjects WHERE id = $subject_id LIMIT 1";
$res_subject = mysqli_query($conn, $sql_subject);

if (mysqli_num_rows($res_subject) != 1) {
    die("❌ Subject not found!");
}

$subject = mysqli_fetch_assoc($res_subject);

// Fetch last prediction
$sql_pred = "SELECT * FROM prj_study_predictions 
             WHERE subject_id = $subject_id 
             ORDER BY id DESC LIMIT 1";
$res_pred = mysqli_query($conn, $sql_pred);

$prediction = mysqli_num_rows($res_pred) ? mysqli_fetch_assoc($res_pred) : null;

// Fetch daily logs
$sql_logs = "SELECT * FROM prj_study_daily_data 
             WHERE subject_id = $subject_id 
             ORDER BY log_date DESC";
$res_logs = mysqli_query($conn, $sql_logs);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $subject['subject_name']; ?> - Workspace</title>
     <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/favicon.ico">

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f4f7fb;
            color: #333;
            padding: 30px;
        }

        .container {
            width: 95%;
            max-width: 900px;
            margin: auto;
        }

        h2 {
            font-size: 30px;
            color: #4d1274ff;
            margin-bottom: 10px;
        }

        h3 {
            margin-top: 30px;
            font-size: 22px;
            color: #303952;
        }

        a.back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #4c6ef5;
            font-weight: 600;
        }

        .prediction-box {
            background: #fff;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-right: 10px;
            transition: 0.2s;
        }

        .btn-add {
            background: #38c172;
            color: white;
        }
        .btn-add:hover { background: #2f9e5d; }

        .btn-predict {
            background: #9645e2ff;
            color: white;
        }
        .btn-predict:hover { background: #8f2dc9ff; }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-radius: 12px;
            overflow: hidden;
        }

        table th {
            background: #eef2ff;
            padding: 12px;
            font-weight: 600;
            color: #444;
            font-size: 15px;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        table tr:hover {
            background: #f9faff;
        }

        @media (max-width: 768px) {
            table td, table th {
                font-size: 13px;
                padding: 10px;
            }
        }
    </style>

</head>
<body>

<div class="container">

    <h2>📘 <?php echo $subject['subject_name']; ?> — Workspace</h2>
    <br><br>
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <h3>🔮 Last Prediction</h3>

    <div class="prediction-box">
        <?php 
        if ($prediction) {
            echo "<p><strong>Predicted Score:</strong> {$prediction['predicted_score']}</p>";
            echo "<p><strong>Date:</strong> {$prediction['created_at']}</p>";
        } else {
            echo "<p>No predictions yet.</p>";
        }
        ?>
    </div>

    <a class="btn btn-add" href="add_daily.php?id=<?php echo $subject_id; ?>">➕ Add Today's Data</a>

    <a class="btn btn-predict" href="predict.php?id=<?php echo $subject_id; ?>">🔮 Run Prediction</a>

    <h3>📅 Daily Logs</h3>

    <table>
        <tr>
            <th>Date</th>
            <th>Study Hours</th>
            <th>Sleep Hours</th>
            <th>Topics Covered</th>
            <th>Assignments Done</th>
        </tr>

        <?php
        if (mysqli_num_rows($res_logs) > 0) {
            while ($log = mysqli_fetch_assoc($res_logs)) {
                echo "
                <tr>
                    <td>{$log['log_date']}</td>
                    <td>{$log['study_hours']}</td>
                    <td>{$log['sleep_hours']}</td>
                    <td>{$log['topics_covered']}</td>
                    <td>{$log['assignments_done']}</td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No logs yet.</td></tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
