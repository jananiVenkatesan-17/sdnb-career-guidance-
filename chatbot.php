<?php
header("Content-Type: application/json");
include("../config/db.php");

$message = isset($_POST["message"]) ? trim($_POST["message"]) : "";
$courseId = isset($_POST["courseId"]) ? intval($_POST["courseId"]) : 0;
$currentModule = isset($_POST["currentModule"]) ? trim($_POST["currentModule"]) : "";

if ($message === "") {
    echo json_encode([
        "status" => "error",
        "reply" => "Please type a question."
    ]);
    exit;
}

$msg = strtolower($message);
$reply = "";

/* ================= MODULE LOCK MODE ================= */

if ($currentModule === "Aptitude") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for aptitude.";
    } elseif (
        strpos($msg, 'aptitude') !== false ||
        strpos($msg, 'question') !== false ||
        strpos($msg, 'sum') !== false ||
        strpos($msg, 'problem') !== false
    ) {
        $sql = "SELECT question, answer FROM aptitude_questions ORDER BY id LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = "Q: " . $row["question"] . "\nA: " . $row["answer"];
        }

        $reply = count($items)
            ? "Aptitude Questions:\n\n" . implode("\n\n", $items)
            : "No aptitude questions found.";
    } else {
        $sql = "SELECT title, url FROM study_materials WHERE (course_id = 0 OR course_id = $courseId) AND module = 'Aptitude' LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = '<a href="' . htmlspecialchars($row["url"]) . '" target="_blank">' . htmlspecialchars($row["title"]) . '</a>';
        }

        $reply = count($items)
            ? "Aptitude Study Links:<br>" . implode("<br>", $items)
            : "This page works only for aptitude.";
    }
}

elseif ($currentModule === "HR") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for HR.";
    } else {
        $sql = "SELECT question, tip FROM hr_questions ORDER BY id LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = "Q: " . $row["question"] . "\nTip: " . $row["tip"];
        }

        $reply = count($items)
            ? "HR Questions:\n\n" . implode("\n\n", $items)
            : "This page works only for HR.";
    }
}

elseif ($currentModule === "Resume") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for resume.";
    } else {
        $sql = "SELECT tip FROM resume_tips ORDER BY id LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = "• " . $row["tip"];
        }

        $reply = count($items)
            ? "Resume Tips:\n" . implode("\n", $items)
            : "This page works only for resume.";
    }
}

elseif ($currentModule === "GD") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for GD.";
    } else {
        $sql = "SELECT topic FROM gd_topics ORDER BY id LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = "• " . $row["topic"];
        }

        $reply = count($items)
            ? "GD Topics:\n" . implode("\n", $items)
            : "This page works only for GD.";
    }
}

elseif ($currentModule === "Skills") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for skills.";
    } else {
        $sql = "SELECT skill_type, skill_name FROM skills WHERE course_id = $courseId ORDER BY skill_type, skill_name LIMIT 20";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = $row["skill_type"] . ": " . $row["skill_name"];
        }

        $reply = count($items)
            ? "Skills for your course:\n" . implode("\n", $items)
            : "No skills found for this course.";
    }
}

elseif ($currentModule === "Coding") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for coding practice.";
    } else {
        $sql = "SELECT topic, description FROM coding_topics ORDER BY id LIMIT 8";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = $row["topic"] . " - " . $row["description"];
        }

        $reply = count($items)
            ? "Coding Practice Topics:\n" . implode("\n", $items)
            : "This page works only for coding practice.";
    }
}

elseif ($currentModule === "Mock Interview") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for mock interview.";
    } else {
        $sql = "SELECT question FROM mock_interview_questions ORDER BY id LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = "• " . $row["question"];
        }

        $reply = count($items)
            ? "Mock Interview Questions:\n" . implode("\n", $items)
            : "This page works only for mock interview.";
    }
}

elseif ($currentModule !== "") {
    $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
    $isGreeting = in_array($msg, $greetings);

    if ($isGreeting) {
        $reply = "This page works only for " . $currentModule . ".";
    } else {
        $moduleSafe = mysqli_real_escape_string($conn, $currentModule);
        $sql = "SELECT title, url FROM study_materials WHERE (course_id = 0 OR course_id = $courseId) AND module = '$moduleSafe' LIMIT 5";
        $res = mysqli_query($conn, $sql);

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = '<a href="' . htmlspecialchars($row["url"]) . '" target="_blank">' . htmlspecialchars($row["title"]) . '</a>';
        }

        $reply = count($items)
            ? $currentModule . " Links:<br>" . implode("<br>", $items)
            : "This page works only for " . $currentModule . ".";
    }
}

/* ================= NORMAL CHAT MODE ================= */
else {
  if (strpos($msg, "aptitude") !== false) {
        $reply = "Open the Aptitude section to view aptitude questions and links.";
    } elseif (strpos($msg, "hr") !== false) {
        $reply = "Open the HR section to view HR interview questions.";
    } elseif (strpos($msg, "resume") !== false) {
        $reply = "Open the Resume section to view resume tips.";
    } elseif (strpos($msg, "gd") !== false) {
        $reply = "Open the GD section to view GD topics.";
    } elseif (strpos($msg, "coding") !== false) {
        $reply = "Open the Coding Practice section to view coding topics.";
    } elseif (strpos($msg, "skill") !== false) {
        $reply = "Open the Skill Requirements section to view course skills.";
    } elseif (strpos($msg, "technical") !== false) {
        $reply = "Open the Technical section to view technical study materials.";
    } else {
        $reply = "Please choose a module first: Aptitude, HR, Resume, GD, Coding Practice, Technical, or Skill Requirements.";
    }
}

echo json_encode([
    "status" => "success",
    "reply" => $reply
]);
?>
