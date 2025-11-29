<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$subject_id = $_GET["id"];

// -------------------------
// Fetch all daily logs for this subject
// -------------------------
$sql = "SELECT study_hours, sleep_hours, topics_covered, assignments_done
        FROM prj_study_daily_data
        WHERE subject_id = $subject_id";
$res = mysqli_query($conn, $sql);

$study = [];
$sleep = [];
$topics = [];
$assign = [];

while ($row = mysqli_fetch_assoc($res)) {
    $study[] = floatval($row["study_hours"]);
    $sleep[] = floatval($row["sleep_hours"]);
    $topics[] = intval($row["topics_covered"]);
    $assign[] = intval($row["assignments_done"]);
}

$count = count($study);

// -------------------------
// Minimum data check
// -------------------------
if ($count < 2) {
    $_SESSION["error"] = "⚠️ Not enough data for prediction.";
    header("Location: subject.php?id=$subject_id");
    exit();
}

// -------------------------
// Target variable Y
// Weighted score combining all factors
// -------------------------
$Y = [];
for ($i = 0; $i < $count; $i++) {
    $score = ($study[$i] * 0.55) +
             ($sleep[$i] * 0.10) +
             ($topics[$i] * 2.0) +
             ($assign[$i] * 1.5); // historical weight for regression
    $Y[] = $score;
}

// -------------------------
// Multilinear regression calculation
// -------------------------
function mean($arr) { return array_sum($arr)/count($arr); }

$X1 = $study;
$X2 = $sleep;
$X3 = $topics;
$X4 = $assign;

$X = [$X1, $X2, $X3, $X4];

$B = [0,0,0,0,0]; // b0,b1,b2,b3,b4

$meanY = mean($Y);
$meanX = array_map('mean', $X);

// Compute coefficients B1..B4
for ($i=0; $i<4; $i++) {
    $num=0; $den=0;
    for ($j=0; $j<$count; $j++){
        $num += ($X[$i][$j]-$meanX[$i])*($Y[$j]-$meanY);
        $den += ($X[$i][$j]-$meanX[$i])**2;
    }
    $B[$i+1] = ($den==0)?0:($num/$den);
}

// Intercept B0
$B[0] = $meanY - ($B[1]*$meanX[0] + $B[2]*$meanX[1] + $B[3]*$meanX[2] + $B[4]*$meanX[3]);

// -------------------------
// Latest entry values
// -------------------------
$today_study = $study[$count-1];
$today_sleep = $sleep[$count-1];
$today_topics = $topics[$count-1];
$today_assign = $assign[$count-1];

// -------------------------
// Limit topics between 0 and 10
// -------------------------
$today_topics = max(0, min(10, $today_topics));

// -------------------------
// Enforce 24-hour constraint for study + sleep
// -------------------------
$min_sleep = 6; // minimum realistic sleep
if ($today_study + $today_sleep > 24) {
    if ($today_sleep < $min_sleep) {
        $today_study = 24 - $min_sleep;
        $today_sleep = $min_sleep;
    } else {
        $today_sleep = 24 - $today_study;
    }
}

// -------------------------
// Base prediction using multilinear regression
// Assignments act as bonus multiplier
// -------------------------
$base_pred = $B[0] + $B[1]*$today_study + $B[2]*$today_sleep + $B[3]*$today_topics;

// Bonus multiplier: each assignment adds 5% of base prediction
$bonus_factor = 0.05;
$bonus_multiplier = 1 + ($bonus_factor * $today_assign);

$pred = $base_pred * $bonus_multiplier;

// Normalize prediction to 0-100
$pred = max(0, min(100, round($pred,2)));

// -------------------------
// Store prediction in database
// -------------------------
$sql_insert = "INSERT INTO prj_study_predictions (subject_id, predicted_score)
               VALUES ($subject_id, $pred)";
mysqli_query($conn, $sql_insert);

// Set success message and redirect
$_SESSION["success"] = "🔮 New Prediction: $pred / 100";
header("Location: subject.php?id=$subject_id");
exit();
?>
