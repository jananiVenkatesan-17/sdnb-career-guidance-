<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once("../config/db.php");

function replyJson($status, $data = []) {
    echo json_encode(array_merge(["status" => $status], $data), JSON_UNESCAPED_UNICODE);
    exit;
}

function normalizeText($text) {
    $text = strtolower($text);
    $text = str_replace(['&'], ' and ', $text);
    $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function detectCourseIdFromHint($courseHint) {
    $hint = normalizeText($courseHint);

    if (strpos($hint, "mca") !== false) return 41;
    if (strpos($hint, "mcom") !== false || strpos($hint, "m com") !== false) return 42;
    if (strpos($hint, "msc") !== false) return 43;
    if (strpos($hint, "ma english") !== false) return 44;
    if (strpos($hint, "ma tamil") !== false) return 45;

    if (strpos($hint, "cognitive systems") !== false || strpos($hint, "cgs") !== false) return 9;
    if (strpos($hint, "data science with artificial intelligence") !== false || strpos($hint, "ds ai") !== false || strpos($hint, "ai ds") !== false) return 12;
    if (strpos($hint, "data science") !== false || strpos($hint, "ds") !== false) return 10;
    if (strpos($hint, "artificial intelligence") !== false || strpos($hint, "ai") !== false) return 11;
    if (strpos($hint, "cloud computing") !== false) return 14;
    if (strpos($hint, "cyber security") !== false) return 15;
    if (strpos($hint, "computer science") !== false || strpos($hint, "cs") !== false) return 1;

    if (strpos($hint, "bca") !== false) return 3;
    if (strpos($hint, "bcom") !== false || strpos($hint, "commerce") !== false) return 4;
    if (strpos($hint, "bba") !== false) return 5;
    if (strpos($hint, "maths") !== false || strpos($hint, "mathematics") !== false) return 6;
    if (strpos($hint, "physics") !== false) return 7;
    if (strpos($hint, "chemistry") !== false) return 8;
    if (strpos($hint, "english") !== false) return 30;
    if (strpos($hint, "economics") !== false) return 32;
    if (strpos($hint, "psychology") !== false) return 39;

    return 0;
}

function getCourseName($conn, $courseId) {
    $stmt = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ? LIMIT 1");
    if (!$stmt) return "";

    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row["course_name"];
    }

    return "";
}

function getKeywordsForCourse($conn, $courseId) {
    $keywords = [];
    $stmt = $conn->prepare("SELECT keyword_name FROM ats_keywords WHERE course_id = ? ORDER BY keyword_name ASC");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $kw = trim($row["keyword_name"]);
        if ($kw !== "") {
            $keywords[] = $kw;
        }
    }

    return $keywords;
}

function keywordExistsInResume($resumeText, $keyword) {
    $resume = normalizeText($resumeText);
    $kw = normalizeText($keyword);

    if ($kw === "") return false;

    $pattern = '/\b' . preg_quote($kw, '/') . '\b/i';
    return preg_match($pattern, $resume) === 1;
}

function buildFeedback($score, $courseName) {
    if ($score >= 85) {
        return "Excellent resume for {$courseName}. Your resume includes most important ATS keywords.";
    }

    if ($score >= 65) {
        return "Good resume for {$courseName}. Add a few more relevant keywords and improve project details.";
    }

    if ($score >= 40) {
        return "Your resume is average for {$courseName}. Add more domain-related keywords, skills, and project points.";
    }

    return "Your resume needs improvement for {$courseName}. Add missing keywords, projects, tools, and achievements.";
}

$courseHint = trim($_POST["course_hint"] ?? "");
$resumeText = trim($_POST["resume_text"] ?? "");

if ($resumeText === "" && isset($_FILES["resume_file"])) {
    $fileTmp = $_FILES["resume_file"]["tmp_name"] ?? "";
    $fileName = $_FILES["resume_file"]["name"] ?? "";
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileExt === "txt") {
        $resumeText = file_get_contents($fileTmp);

    } elseif ($fileExt === "pdf") {
        $resumeText = @file_get_contents($fileTmp);

        if ($resumeText) {
            $resumeText = preg_replace('/[^(\x20-\x7F)]*/', ' ', $resumeText);
            $resumeText = preg_replace('/\s+/', ' ', $resumeText);
        }

    } elseif ($fileExt === "docx") {
        $zip = new ZipArchive;

        if ($zip->open($fileTmp) === TRUE) {
            $data = $zip->getFromName("word/document.xml");
            $zip->close();

            if ($data) {
                $resumeText = strip_tags($data);
                $resumeText = html_entity_decode($resumeText, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $resumeText = preg_replace('/\s+/', ' ', $resumeText);
            }
        }

        if (trim($resumeText) === "") {
            replyJson("error", [
                "message" => "❌ Could not read DOCX file. Please paste the resume text manually."
            ]);
        }

    } elseif ($fileExt === "doc") {
        replyJson("error", [
            "message" => "❌ DOC file is not supported. Please convert it to DOCX/TXT or paste the resume text."
        ]);

    } else {
        replyJson("error", [
            "message" => "❌ Only TXT, PDF, and DOCX files are supported now."
        ]);
    }
}

if ($resumeText === "") {
    replyJson("error", ["message" => "❌ Please paste resume text or upload a TXT/PDF/DOCX file."]);
}

if ($courseHint === "") {
    replyJson("error", ["message" => "❌ Please type ATS with your course first."]);
}

$courseId = detectCourseIdFromHint($courseHint);

if ($courseId <= 0) {
    replyJson("error", ["message" => "❌ Could not detect course. Try like: ats for cs, ats for ai, ats for mca."]);
}

$courseName = getCourseName($conn, $courseId);

if ($courseName === "") {
    replyJson("error", ["message" => "❌ Course not found in courses table."]);
}

$keywords = getKeywordsForCourse($conn, $courseId);

if (empty($keywords)) {
    replyJson("error", ["message" => "❌ No ATS keywords found for {$courseName}."]);
}

$matched = [];
$missing = [];

foreach ($keywords as $keyword) {
    if (keywordExistsInResume($resumeText, $keyword)) {
        $matched[] = $keyword;
    } else {
        $missing[] = $keyword;
    }
}

$totalKeywords = count($keywords);
$matchedCount = count($matched);
$score = $totalKeywords > 0 ? round(($matchedCount / $totalKeywords) * 100) : 0;
$feedback = buildFeedback($score, $courseName);

$matchedText = implode(", ", $matched);
$missingText = implode(", ", $missing);

$stmt = $conn->prepare("
    INSERT INTO ats_analysis (course_id, resume_text, score, matched_keywords, missing_keywords, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

if ($stmt) {
    $stmt->bind_param("isiss", $courseId, $resumeText, $score, $matchedText, $missingText);
    $stmt->execute();
}

replyJson("success", [
    "course_name" => $courseName,
    "score" => $score,
    "matched_keywords" => $matched,
    "missing_keywords" => $missing,
    "feedback" => $feedback
]);
?>