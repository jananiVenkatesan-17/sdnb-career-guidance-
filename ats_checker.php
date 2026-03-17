<?php
header("Content-Type: application/json");
include("../config/db.php");

$resume = isset($_POST["resume"]) ? strtolower(trim($_POST["resume"])) : "";
$courseId = isset($_POST["courseId"]) ? intval($_POST["courseId"]) : 0;

if ($resume === "" || $courseId <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Resume text or course not provided."
    ]);
    exit;
}

$sql = "SELECT keyword_name FROM ats_keywords WHERE course_id = $courseId ORDER BY keyword_name";
$result = mysqli_query($conn, $sql);

$keywords = [];
while ($row = mysqli_fetch_assoc($result)) {
    $keywords[] = strtolower($row["keyword_name"]);
}

if (count($keywords) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "No ATS keywords found for this course."
    ]);
    exit;
}

$matched = [];
$missing = [];

foreach ($keywords as $keyword) {
    if (strpos($resume, strtolower($keyword)) !== false) {
        $matched[] = $keyword;
    } else {
        $missing[] = $keyword;
    }
}

$total = count($keywords);
$score = $total > 0 ? round((count($matched) / $total) * 100) : 0;

$matchedText = mysqli_real_escape_string($conn, implode(", ", $matched));
$missingText = mysqli_real_escape_string($conn, implode(", ", $missing));
$resumeSafe = mysqli_real_escape_string($conn, $resume);

$insert = "INSERT INTO ats_analysis (course_id, resume_text, score, matched_keywords, missing_keywords)
           VALUES ($courseId, '$resumeSafe', $score, '$matchedText', '$missingText')";
mysqli_query($conn, $insert);

echo json_encode([
    "status" => "success",
    "score" => $score,
    "matched" => $matched,
    "missing" => $missing
]);
?>
