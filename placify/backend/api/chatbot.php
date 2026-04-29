<?php
// ============================================================
// PLACIFY CHATBOT — FULLY CORRECTED chat.php
// Matches dataset.sql schema exactly
// ============================================================

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

session_start();
include("../config/db.php");

// ============================================================
// HELPER: Send JSON reply and exit
// ============================================================
function sendReply($message, $extra = []) {
    $res = array_merge(["reply" => $message], $extra);
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
// FILE TEXT EXTRACTION — PDF, DOCX, DOC, TXT
// ============================================================
function extractResumeText($filePath, $origName) {
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    // ── TXT ──────────────────────────────────────────────────
    if ($ext === 'txt') {
        return file_get_contents($filePath);
    }

    // ── DOCX — unzip and parse word/document.xml ─────────────
    if ($ext === 'docx') {
        if (!class_exists('ZipArchive')) return false;
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) return false;
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if (!$xml) return false;
        // Replace paragraph/line-break tags with newline, then strip all tags
        $xml  = str_replace(['</w:p>','</w:r>','<w:br/>'], "\n", $xml);
        $text = strip_tags($xml);
        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    // ── PDF — pdftotext first, then raw byte fallback ─────────
    if ($ext === 'pdf') {
        // Try pdftotext (poppler-utils — available on most Linux servers)
        $escaped = escapeshellarg($filePath);
        $text    = @shell_exec("pdftotext $escaped - 2>/dev/null");
        if ($text && strlen(trim($text)) > 30) return $text;

        // Raw PDF byte extraction fallback
        $raw = file_get_contents($filePath);
        preg_match_all('/BT(.+?)ET/s', $raw, $blocks);
        $text = '';
        foreach ($blocks[1] as $block) {
            preg_match_all('/\(([^)]+)\)\s*Tj/', $block, $tj);
            foreach ($tj[1] as $t) $text .= $t . ' ';
        }
        if (strlen(trim($text)) > 30) return $text;

        // Last resort: grab readable ASCII strings
        preg_match_all('/[\x20-\x7E]{4,}/', $raw, $m);
        return implode(' ', $m[0]);
    }

    // ── DOC (old binary Word) — extract readable strings ──────
    if ($ext === 'doc') {
        $raw = file_get_contents($filePath);
        preg_match_all('/[\x20-\x7E\xC0-\xFF]{4,}/', $raw, $m);
        $text = implode(' ', $m[0]);
        return strlen($text) > 50 ? $text : false;
    }

    return false;
}

// ============================================================
// FILE UPLOAD HANDLER — intercept BEFORE JSON parsing
// Triggered when ATS is in waiting_resume state and a file is sent
// ============================================================
$uploadedResumeText = null;
if (!empty($_FILES['resume']['tmp_name'])) {
    $file     = $_FILES['resume'];
    $allowed  = ['pdf','docx','doc','txt'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["reply" => "⚠️ <b>Unsupported file type.</b>\nPlease upload a <b>PDF, DOCX, DOC, or TXT</b> file only."]);
        exit;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(["reply" => "⚠️ File too large. Please upload a file under 5 MB."]);
        exit;
    }

    $extracted = extractResumeText($file['tmp_name'], $file['name']);
    if ($extracted && strlen(trim($extracted)) > 50) {
        $uploadedResumeText = $extracted;
    } else {
        echo json_encode(["reply" =>
            "⚠️ <b>Could not read your file.</b>\n\n" .
            "This can happen with scanned/image-only PDFs.\n" .
            "👉 Please <b>copy and paste your resume text</b> directly into the chat instead."
        ]);
        exit;
    }
}

// ============================================================
// STREAM DEFINITIONS  (matches courses in dataset.sql)
// ============================================================
$scienceStreams  = ["CS","BCA","MCA","MSC CS","COGNITIVE","DS","AI","DSAI",
                    "CLOUD","CYBER","MATHS","PHYSICS","CHEM","PSY","CND","NFSM","EVS",
                    // New courses from images
                    "STATS","PBPB","MSPB","MSBIO","MSPHY","MSCHEM","MSHSN","MSDAI","MSCP","MSAM"];
$commerceStreams = ["BCOM","CORP","AF","HONS","BIM","BCOM CA","PA","ISM",
                    "FINTECH","IB","BF","MCOM","BBA","DM","FASHION","AIRPORT",
                    // New courses from images
                    "MAHRM","MCOMAF","MBA"];
$artsStreams     = ["ENG","HISTORY","ECO","TAMIL","HINDI","SANSKRIT",
                    "FRENCH","TOURISM","MA ENG","MA TAMIL","VISCOM","SOC",
                    // New courses from images
                    "MSW","MAECO"];

function getStream($short, $scienceStreams, $commerceStreams, $artsStreams) {
    $short = strtoupper(trim($short));
    if (in_array($short, $scienceStreams))  return "science";
    if (in_array($short, $commerceStreams)) return "commerce";
    if (in_array($short, $artsStreams))     return "arts";
    return "general";
}

function getStreamEmoji($stream) {
    $map = ["science" => "🔬", "commerce" => "💼", "arts" => "🎨"];
    return $map[$stream] ?? "🎓";
}

// ============================================================
// RESOURCE LINKS
// ============================================================
function getLinks($topic, $stream = "") {
    $links = [
        "hr" => [
            ["HR Interview Questions & Answers", "https://www.indeed.com/career-advice/interviewing/hr-interview-questions"],
            ["Common Interview Questions",        "https://www.indeed.com/career-advice/interviewing/common-interview-questions"],
            ["Tell Me About Yourself",            "https://www.indeed.com/career-advice/interviewing/tell-me-about-yourself"]
        ],
        "mock" => [
            ["Mock Interview Tips",     "https://www.themuse.com/advice/how-to-do-a-mock-interview"],
            ["STAR Method Explained",   "https://www.indeed.com/career-advice/interviewing/how-to-use-the-star-interview-response-technique"],
            ["Top Interview Questions", "https://www.ambitionbox.com/interviews"]
        ],
        "technical" => [
            ["GeeksforGeeks Interview Prep", "https://www.geeksforgeeks.org/must-do-coding-questions-company-wise/"],
            ["LeetCode Practice",            "https://leetcode.com/"],
            ["HackerRank Challenges",        "https://www.hackerrank.com/"]
        ],
        "coding" => [
            ["GeeksforGeeks DSA",   "https://www.geeksforgeeks.org/data-structures/"],
            ["LeetCode Problems",   "https://leetcode.com/problemset/"],
            ["HackerRank Practice", "https://www.hackerrank.com/domains/algorithms"]
        ],
        "aptitude" => [
            ["IndiaBix Aptitude",      "https://www.indiabix.com/aptitude/questions-and-answers/"],
            ["PrepInsta Aptitude",     "https://prepinsta.com/aptitude/"],
            ["Freshersworld Aptitude", "https://placement.freshersworld.com/aptitude-questions"]
        ],
        "gd" => [
            ["GD Topics 2025",        "https://www.jagranjosh.com/articles/group-discussion-topics"],
            ["How to Prepare for GD", "https://www.shiksha.com/mba/articles/group-discussion-preparation-tips/"],
            ["GD Tips & Tricks",      "https://testbook.com/blog/group-discussion-tips/"]
        ],
        "resume" => [
            ["Resume Tips — Indeed",         "https://www.indeed.com/career-advice/resumes-cover-letters/how-to-make-a-resume"],
            ["ATS Resume Checker — Jobscan", "https://www.jobscan.co/"],
            ["Resume Templates — Canva",     "https://www.canva.com/resumes/templates/"]
        ],
        "company" => [
            ["Glassdoor Company Reviews",  "https://www.glassdoor.co.in/"],
            ["Naukri — Campus Placements", "https://www.naukri.com/campus/"],
            ["Ambitionbox Salaries",       "https://www.ambitionbox.com/"]
        ],
        "skills" => [
            ["LinkedIn Learning",     "https://www.linkedin.com/learning/"],
            ["Coursera Free Courses", "https://www.coursera.org/"],
            ["NPTEL Courses",         "https://nptel.ac.in/"]
        ]
    ];

    // Stream-specific overrides
    if ($topic === "technical" && $stream === "commerce") {
        $links["technical"] = [
            ["Tally Learning",    "https://tallysolutions.com/tally/tally-prime/learn-tally-prime/"],
            ["Excel for Finance", "https://corporatefinanceinstitute.com/resources/excel/"],
            ["SAP Training",      "https://training.sap.com/"]
        ];
    }
    if ($topic === "technical" && $stream === "arts") {
        $links["technical"] = [
            ["MS Office Tutorials",      "https://support.microsoft.com/en-us/office"],
            ["Content Writing Tips",     "https://blog.hubspot.com/marketing/content-writing"],
            ["Digital Marketing Basics", "https://learndigital.withgoogle.com/digitalgarage"]
        ];
    }
    if ($topic === "coding" && $stream === "commerce") {
        $links["coding"] = [
            ["Excel VBA Basics", "https://www.excel-easy.com/vba.html"],
            ["SQL for Finance",  "https://mode.com/sql-tutorial/"],
            ["Python for Excel", "https://www.datacamp.com/courses/intermediate-python"]
        ];
    }

    $found = $links[$topic] ?? $links["hr"];
    $out   = "\n\n🔗 <b>Useful Resources:</b>\n";
    foreach ($found as $link) {
        $out .= "👉 <a href=\"{$link[1]}\" target=\"_blank\">{$link[0]}</a>\n";
    }
    return $out;
}

// ============================================================
// READ INPUT
// ============================================================
$rawInput = file_get_contents("php://input");
$input    = json_decode($rawInput, true);
$message  = trim($input['message'] ?? '');

// If a file was uploaded, use its extracted text as the message
if ($uploadedResumeText) {
    $message = trim($uploadedResumeText);
}

$msg = mb_strtolower($message, 'UTF-8');

if (empty($message)) sendReply("Please type a message.");

// ============================================================
// PARSE "MODULE for COURSE" PATTERN  e.g. "gd for ds"
// ============================================================
$parsedModule = null;
$parsedCourse = null;
if (preg_match('/^(\w[\w\s]*?)\s+for\s+(.+)$/i', $msg, $m)) {
    $parsedModule = strtolower(trim($m[1]));
    $parsedCourse = strtoupper(trim($m[2]));
}

// ============================================================
// LOAD ALL COURSES FROM DB + SESSION TRACKING
// ============================================================
$allCoursesResult = $conn->query("SELECT course_id, course_name, short_name FROM courses");
$allCoursesMap    = [];
if ($allCoursesResult && $allCoursesResult->num_rows > 0) {
    while ($c = $allCoursesResult->fetch_assoc()) {
        $allCoursesMap[strtoupper(trim($c['short_name']))] = $c;
    }
}

// Detect course from the "for COURSE" pattern first — exact short name
$detectedCourse = null;
if ($parsedCourse && isset($allCoursesMap[$parsedCourse])) {
    $detectedCourse = $allCoursesMap[$parsedCourse];
    $_SESSION['current_course'] = $detectedCourse;
}

// If "for COURSE" was typed but short name didn't match exactly,
// try matching against full course name OR short name as substring
// e.g. "hr for english"  → parsedCourse=ENGLISH → finds "English Literature" (ENG)
// e.g. "hr for baa english" → parsedCourse=BAA ENGLISH → "baa english" contains "eng"
if (!$detectedCourse && $parsedCourse) {
    $parsedLower = mb_strtolower($parsedCourse, 'UTF-8');
    foreach ($allCoursesMap as $short => $c) {
        $shortLower = mb_strtolower($short, 'UTF-8');
        $nameLower  = mb_strtolower(trim($c['course_name']), 'UTF-8');
        if (
            strpos($nameLower, $parsedLower) !== false ||   // "english literature" contains "english"
            strpos($parsedLower, $nameLower) !== false ||   // parsedCourse contains full name
            strpos($parsedLower, $shortLower) !== false     // "baa english" contains "eng"
        ) {
            $detectedCourse = $c;
            $_SESSION['current_course'] = $c;
            break;
        }
    }
}

// Fallback: scan the entire message for any course short-name or full name
if (!$detectedCourse) {
    foreach ($allCoursesMap as $short => $c) {
        $shortLower = mb_strtolower($short, 'UTF-8');
        $nameLower  = mb_strtolower(trim($c['course_name']), 'UTF-8');
        if (
            $msg === $shortLower ||
            preg_match('/\b' . preg_quote($shortLower, '/') . '\b/', $msg) ||
            strpos($msg, $nameLower) !== false
        ) {
            $detectedCourse = $c;
            $_SESSION['current_course'] = $c;
            break;
        }
    }
}

$sessionCourse = $_SESSION['current_course'] ?? null;

// IMPORTANT: If user explicitly typed "X for Y" but Y didn't match any course,
// do NOT silently fall back to the old session course — that causes wrong dept shown.
// Only use session course when no explicit "for Y" was given.
$activeCourse = $detectedCourse ?? ($parsedCourse ? null : $sessionCourse);

// ============================================================
// HELPER: Build course info array
// ============================================================
function getCourseInfo($course, $scienceStreams, $commerceStreams, $artsStreams) {
    if (!$course) return null;
    $short  = $course['short_name'];
    $stream = getStream($short, $scienceStreams, $commerceStreams, $artsStreams);
    return [
        'id'     => $course['course_id'],
        'name'   => $course['course_name'],
        'short'  => $short,
        'stream' => $stream,
        'emoji'  => getStreamEmoji($stream)
    ];
}

// ============================================================
// MODULE DETECTION
// ============================================================
function detectModule($msg, $parsedModule) {
    // Map parsed "module" word to internal module key
    if ($parsedModule) {
        $map = [
            'gd'              => 'gd',
            'group discussion'=> 'gd',
            'hr'              => 'hr',
            'human resource'  => 'hr',
            'interview'       => 'hr',
            'mock'            => 'mock',
            'mock interview'  => 'mock',
            'aptitude'        => 'aptitude',
            'quant'           => 'aptitude',
            'technical'       => 'technical',
            'tech'            => 'technical',
            'coding'          => 'coding',
            'code'            => 'coding',
            'dsa'             => 'coding',
            'skills'          => 'skills',
            'skill'           => 'skills',
            'resume'          => 'resume',
            'cv'              => 'resume',
            'ats'             => 'ats',
            'company'         => 'company',
            'companies'       => 'company',
        ];
        foreach ($map as $kw => $mod) {
            if (strpos($parsedModule, $kw) !== false) return $mod;
        }
    }

    // NOTE: faq checked first to catch natural language placement questions
    // NOTE: coding checked BEFORE technical to avoid keyword clash
    $triggers = [
        'faq' => [
            'minimum cgpa','cgpa required','arrear','backlog','attendance required','gap year',
            'english communication necessary','english communication required','resume mandatory',
            'who is eligible','discipline important','multiple company','10th marks','12th marks',
            'school marks','average salary','highest salary','highest package','average package',
            'placement guaranteed','guarantee placement','when placement start','placement drive begin',
            'off campus','off-campus','startup opportunit','internship before placement',
            'which companies visit','what companies visit','mnc recruit','campus placement available',
            'excel important','tally important','gst important','accounting skill',
            'first year training','second year training','final year training',
            'aptitude class conducted','workshop conducted','industry expert','guest lecture',
            'what is hr interview','what is technical interview','body language important',
            'confidence important','ask question at end','dont know answer',
            'what include in resume','what should resume contain','when prepare resume',
            'what question asked in interview','type of question asked',
        ],
        'gd'        => ['gd','group discussion','gd topic','discussion'],
        'hr'        => ['hr','human resource','hr question','hr interview','behavioral','strengths','weakness'],
        'mock'      => ['mock','mock interview','practice interview','simulate','full interview'],
        'aptitude'  => ['aptitude','quant','numerical','practice question','aptitude test'],
        'coding'    => ['coding','code','dsa','data structure','algorithm','leetcode','hackerrank','program'],
        'technical' => ['technical','tech question','java','python','sql','machine learning'],
        'skills'    => ['skill','what skill','which skill','soft skill','hard skill','technical skill'],
        'resume'    => ['resume','cv','make resume','build resume','resume tips','resume format'],
        'ats'       => ['ats','resume scan','applicant tracking','ats score','ats guide','check my resume','scan my resume'],
        'company'   => ['company','companies','which company','top company','who recruits','placement drive','hiring'],
        'courses'   => ['courses','course','all courses','departments','list courses'],
        'urgent'    => ['tomorrow','tonight','urgent','help me fast','quick help','last minute','interview today'],
    ];

    foreach ($triggers as $mod => $kwList) {
        foreach ($kwList as $kw) {
            if (fuzzyContains($msg, $kw)) return $mod;
        }
    }
    return null;
}

// ============================================================
// ATS RESUME ANALYSIS — Must run BEFORE module detection
// Handles the second step where the user pastes their resume
// ============================================================
$atsStep          = $_SESSION['ats_step']   ?? null;
$atsCourseSession = $_SESSION['ats_course'] ?? null;

if ($atsStep === 'waiting_resume' && $atsCourseSession && strlen($message) > 80) {
    $_SESSION['ats_step']   = null;
    $_SESSION['ats_course'] = null;

    $cid    = (int)$atsCourseSession['course_id'];
    $short  = $atsCourseSession['short_name'];
    $stream = getStream($short, $scienceStreams, $commerceStreams, $artsStreams);

    // Fetch keywords — column is 'keyword' (matches schema exactly)
    $kwResult    = $conn->query("SELECT keyword FROM ats_keywords WHERE course_id = $cid LIMIT 100");
    $allKeywords = [];
    if ($kwResult && $kwResult->num_rows > 0) {
        while ($row = $kwResult->fetch_assoc()) {
            $kw = strtolower(trim($row['keyword']));
            if ($kw && !in_array($kw, $allKeywords)) $allKeywords[] = $kw;
        }
    }

    // Fallback keywords if none in DB for this course
    if (empty($allKeywords)) {
        $fallbacks = [
            "science"  => ["python","java","sql","html","css","javascript","data structures",
                           "algorithms","problem solving","communication","leadership","teamwork","git"],
            "commerce" => ["accounting","tally","gst","excel","ms excel","communication",
                           "leadership","marketing","finance","analytical thinking","presentation","teamwork"],
            "arts"     => ["communication","presentation","ms office","writing","research",
                           "analytical thinking","leadership","teamwork","english","content"],
        ];
        $allKeywords = $fallbacks[$stream] ?? $fallbacks["science"];
    }

    $resumeText = mb_strtolower($message, 'UTF-8');
    $found      = [];
    $missing    = [];
    foreach ($allKeywords as $kw) {
        if (strpos($resumeText, $kw) !== false) $found[]   = $kw;
        else                                     $missing[] = $kw;
    }

    $total  = count($allKeywords);
    $score  = $total > 0 ? round((count($found) / $total) * 100) : 0;
    $filled = (int)($score / 10);
    $bar    = str_repeat("█", $filled) . str_repeat("░", 10 - $filled);

    $grade  = $score >= 80 ? "🟢 <b>Excellent!</b> Your resume is well-optimised."
            : ($score >= 60 ? "🟡 <b>Good.</b> A few keywords are missing."
            : ($score >= 40 ? "🟠 <b>Average.</b> Add more relevant keywords."
            :                 "🔴 <b>Needs Work.</b> Many important keywords are missing."));

    $reply  = "📄 <b>ATS Analysis Results — $short</b>\n━━━━━━━━━━━━━━━━━\n\n";
    $reply .= "🎯 <b>ATS Score: $score%</b>\n$bar\n$grade\n\n";

    if (!empty($found)) {
        $reply .= "✅ <b>Keywords Found (" . count($found) . "):</b>\n";
        $reply .= implode(", ", array_slice($found, 0, 15)) . "\n\n";
    }
    if (!empty($missing)) {
        $reply .= "❌ <b>Missing Keywords (" . count($missing) . "):</b>\n";
        $reply .= implode(", ", array_slice($missing, 0, 12)) . "\n\n";
    }

    $reply .= "💡 <b>How to Improve:</b>\n";
    $reply .= "✅ Add missing keywords naturally in your Skills section\n";
    $reply .= "✅ Match exact keywords from job descriptions\n";
    $reply .= "✅ Use standard headings: Education, Skills, Experience\n";
    $reply .= "✅ No images or fancy tables — keep it plain text\n";
    $reply .= "✅ Save as PDF or DOCX, font: Arial or Calibri, size 11–12\n\n";
    $reply .= "💡 Type <b>ats for $short</b> to check another resume!";
    $reply .= getLinks("resume");
    sendReply($reply);
}

// ============================================================
// MODULE DETECTION
// ============================================================
$module = detectModule($msg, $parsedModule);

// ============================================================
// GREETING
// ============================================================
$greetWords = ["hi","hello","hey","hai","help","start","begin","namaste"];
$isGreeting = false;
foreach ($greetWords as $g) {
    if (strpos($msg, $g) !== false) { $isGreeting = true; break; }
}
if ($isGreeting && !$module) {
    $_SESSION['current_course'] = null;
    $_SESSION['ats_step']       = null;
    $_SESSION['ats_course']     = null;
    sendReply(
        "👋 <b>Hello! Welcome to Placify — Your Placement Assistant!</b>\n\n" .
        "I can help you with:\n\n" .
        "🔬 <b>Science & Tech:</b> BCA, MCA, CS, AI, DS, CYBER, CLOUD\n" .
        "💼 <b>Commerce:</b> BBA, BCOM, FINTECH, BIM, DM\n" .
        "🎨 <b>Arts:</b> ENG, TAMIL, HISTORY, TOURISM, VISCOM\n\n" .
        "📌 <b>Available Modules:</b>\n" .
        "• <b>GD</b> → Group Discussion Topics\n" .
        "• <b>HR</b> → HR Interview Questions\n" .
        "• <b>MOCK</b> → Mock Interview\n" .
        "• <b>APTITUDE</b> → Practice Questions\n" .
        "• <b>TECHNICAL</b> → Technical Topics\n" .
        "• <b>CODING</b> → DSA & Coding Problems\n" .
        "• <b>SKILLS</b> → Skills Needed\n" .
        "• <b>RESUME</b> → Resume Tips\n" .
        "• <b>ATS</b> → ATS Resume Checker\n" .
        "• <b>COMPANY</b> → Placement Companies\n" .
        "• <b>COURSES</b> → All Courses List\n\n" .
        "💡 <b>Try:</b> GD for DS · HR for TAMIL · ATS for BCA · CODING for MCA"
    );
}

// ============================================================
// COURSE-ONLY input (e.g. user types just "BCA" or "I am from BCA")
// ============================================================
$courseKeywords  = ["i am","i'm","i study","i'm from","i am from","my course","my department","prepare for","i belong"];
$hasCourseIntent = false;
foreach ($courseKeywords as $kw) {
    if (strpos($msg, $kw) !== false) { $hasCourseIntent = true; break; }
}

if ($detectedCourse && !$module && ($hasCourseIntent || $msg === mb_strtolower($detectedCourse['short_name'], 'UTF-8'))) {
    $ci     = getCourseInfo($detectedCourse, $scienceStreams, $commerceStreams, $artsStreams);
    $short  = $ci['short'];
    $stream = $ci['stream'];

    $advice = [
        "science"  => "1️⃣ Master DSA (Arrays, Trees, Graphs)\n" .
                      "2️⃣ Practice on LeetCode / HackerRank daily\n" .
                      "3️⃣ Learn SQL & DBMS concepts\n" .
                      "4️⃣ Build 2–3 mini projects with GitHub\n" .
                      "5️⃣ Prepare aptitude + technical + HR rounds\n" .
                      "🏢 Target: TCS, Infosys, Wipro, Zoho, Cognizant\n" .
                      "💰 Package: ₹3.5–₹8 LPA",
        "commerce" => "1️⃣ Strengthen Accounting & Finance basics\n" .
                      "2️⃣ Learn Excel, Tally, SAP\n" .
                      "3️⃣ Practice business aptitude\n" .
                      "4️⃣ Prepare HR & GD rounds\n" .
                      "5️⃣ Stay updated with financial news\n" .
                      "🏢 Target: Deloitte, HDFC, ICICI, EY, KPMG\n" .
                      "💰 Package: ₹2.5–₹5 LPA",
        "arts"     => "1️⃣ Build communication & presentation skills\n" .
                      "2️⃣ Practice GD topics regularly\n" .
                      "3️⃣ Learn MS Office & basic digital tools\n" .
                      "4️⃣ Build confidence for HR interviews\n" .
                      "5️⃣ Explore content writing, HR, tourism roles\n" .
                      "🏢 Target: Concentrix, EY, Sutherland, MakeMyTrip\n" .
                      "💰 Package: ₹2–₹4 LPA",
    ];
    $tip   = $advice[$stream] ?? "Type hr, gd, aptitude, mock, skills, coding to get started!";
    $reply = "{$ci['emoji']} <b>Course Detected: {$ci['name']} ($short)</b>\n";
    $reply .= "🎯 Stream: " . ucfirst($stream) . "\n\n";
    $reply .= "💡 <b>Preparation Roadmap:</b>\n$tip\n\n";
    $reply .= "📌 Try: hr for $short · gd for $short · coding for $short · ats for $short";
    sendReply($reply);
}

// ============================================================
// MODULE HANDLERS
// ============================================================

// ============================================================
// FAQ HANDLER — Smart keyword match against placement_faq table
// ============================================================

/**
 * Map a course short_name to the FAQ dept string used in placement_faq table.
 */
function getFaqDept($short) {
    $short = strtoupper(trim($short));
    // Tech / CS courses
    $cs = ['CS','BCA','MCA','MSC CS','COGNITIVE','DS','AI','DSAI','CLOUD','CYBER'];
    if (in_array($short, $cs)) return 'cs';
    // Basic sciences (UG + PG science/research courses)
    $sci = ['MATHS','PHYSICS','CHEM','PSY','CND','NFSM','EVS',
            'STATS','PBPB','MSPB','MSBIO','MSPHY','MSCHEM','MSHSN','MSDAI','MSCP','MSAM'];
    if (in_array($short, $sci)) return 'basic_science';
    // Management (UG + PG management)
    $mgmt = ['BBA','DM','MCOM','MAHRM','MCOMAF','MBA'];
    if (in_array($short, $mgmt)) return 'management';
    // Vocational
    $voc = ['FASHION','AIRPORT'];
    if (in_array($short, $voc)) return 'vocational';
    // Commerce
    $com = ['BCOM','CORP','AF','HONS','BIM','BCOM CA','PA','ISM','FINTECH','IB','BF'];
    if (in_array($short, $com)) return 'commerce';
    // Arts (UG + PG arts/social science)
    $arts = ['ENG','HISTORY','ECO','TAMIL','HINDI','SANSKRIT','FRENCH',
             'TOURISM','MA ENG','MA TAMIL','VISCOM','SOC','MSW','MAECO'];
    if (in_array($short, $arts)) return 'arts';
    return 'general';
}

/**
 * Fuzzy word-level match: checks if every word in $needle appears in $haystack
 * either exactly (strpos) or within levenshtein distance proportional to word length.
 * Tolerates common typos like "trainig"→"training", "palcemet"→"placement".
 */
function fuzzyContains($haystack, $needle) {
    // Fast path: exact substring match
    if (strpos($haystack, $needle) !== false) return true;

    $needleWords    = preg_split('/\s+/', trim($needle));
    $haystackWords  = preg_split('/\s+/', trim($haystack));

    foreach ($needleWords as $nw) {
        $nLen = strlen($nw);
        if ($nLen < 4) {
            // Short words must match exactly as substring
            if (strpos($haystack, $nw) === false) return false;
            continue;
        }
        // Allow levenshtein 1 for length 4-6, 2 for length 7+
        $maxDist = $nLen >= 7 ? 2 : 1;
        $matched = false;
        foreach ($haystackWords as $hw) {
            if (strlen($hw) < 3) continue;
            if (levenshtein($nw, $hw) <= $maxDist) { $matched = true; break; }
        }
        if (!$matched) return false;
    }
    return count($needleWords) > 0;
}

/**
 * Score a FAQ row by counting how many of its keywords appear in $msg.
 * Uses fuzzyContains so typos like "trainig", "palcemet" still score.
 * Returns int score (higher = better match).
 */
function scoreFaqRow($row, $msg) {
    if (empty($row['keywords'])) return 0;
    $keywords = array_map('trim', explode(',', strtolower($row['keywords'])));
    $score = 0;
    foreach ($keywords as $kw) {
        if ($kw !== '' && fuzzyContains($msg, $kw)) $score++;
    }
    // Bonus: if the full question (lowercased) is a substring of msg
    if (strpos($msg, mb_strtolower($row['question'],'UTF-8')) !== false) $score += 5;
    return $score;
}

if ($module === 'faq') {
    $faqDept = $activeCourse ? getFaqDept($activeCourse['short_name']) : 'general';
    $short   = $activeCourse ? $activeCourse['short_name'] : '';

    // Build prioritised dept list: course-specific dept → general
    $depts = array_unique([$faqDept, 'general']);
    $deptIn = "'" . implode("','", array_map(fn($d) => $conn->real_escape_string($d), $depts)) . "'";

    $result = $conn->query("SELECT * FROM placement_faq WHERE dept IN ($deptIn) LIMIT 200");
    $rows   = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) $rows[] = $row;
    }

    // Score each row
    $best      = null;
    $bestScore = 0;
    foreach ($rows as $row) {
        $s = scoreFaqRow($row, $msg);
        if ($s > $bestScore) { $bestScore = $s; $best = $row; }
    }

    // If no DB match, use hardcoded fallback answers keyed by keyword
    if (!$best || $bestScore === 0) {
        $hardcoded = [
            ['salary|package|lpa|pay|income',
             '💰 <b>Salary Information</b>',
             "Science/CS: ₹3–₹6 LPA (top performers up to ₹10 LPA)\nCommerce: ₹2–₹4 LPA (audit firms up to ₹7 LPA)\nArts: ₹2–₹4 LPA\nBasic Sciences: ₹2–₹4 LPA\nManagement: ₹3–₹5 LPA\n\n💡 Salary depends on your skills, CGPA, and company."],
            ['cgpa|percentage|marks required|cutoff',
             '📊 <b>CGPA / Eligibility</b>',
             "Most companies require CGPA 6.0+.\nSchool marks (10th & 12th) should be 60%+.\nNo active arrears at the time of the interview."],
            ['arrear|backlog|pending subject|standing arrear',
             '⚠️ <b>Arrears Policy</b>',
             "Most companies DO NOT allow students with active arrears.\nClear all backlogs before your final year.\nSome BPO/KPO companies may be flexible — check individually."],
            ['attendance',
             '✅ <b>Attendance Requirement</b>',
             "Minimum 75% attendance is required to be eligible for placement drives.\nPoor attendance may disqualify you — maintain regularity throughout your degree."],
            ['gap year',
             '📌 <b>Gap Year Policy</b>',
             "Some companies allow a 1-year gap with a valid reason.\nBe ready to explain your gap positively during interviews.\nCertifications or courses done during the gap strengthen your profile."],
            ['training|first year|second year|final year|what train',
             '📚 <b>Placement Training</b>',
             "Year 1: Communication, personality development, basics.\nYear 2: Aptitude, technical skills, internship guidance.\nFinal Year: Mock interviews, GD practice, resume building, company-specific prep.\nThe college also conducts workshops and invites industry experts."],
            ['body language|posture|eye contact',
             '✅ <b>Body Language in Interviews</b>',
             "Body language is very important!\n• Firm handshake\n• Maintain eye contact\n• Sit straight and upright\n• Smile naturally\n• Avoid fidgeting or crossing arms\n• Nod to show you are listening"],
            ['confidence|nervous|fear|shy',
             '✅ <b>Confidence in Interviews</b>',
             "Confidence plays a very important role in selection.\n• Practice mock interviews regularly\n• Record yourself and review\n• Know your resume inside-out\n• Prepare answers for common HR questions\n• Breathe deeply before entering"],
            ['don\'t know|dont know|blank|no answer',
             '💡 <b>If You Don\'t Know an Answer</b>',
             "Be honest! Say:\n\"I'm not sure about this right now, but I believe it works like...\"\nor: \"I don't know this currently, but I'm eager to learn.\"\n✅ Honesty is respected. Never try to bluff — interviewers can tell immediately."],
        ];

        foreach ($hardcoded as $hc) {
            $patterns = explode('|', $hc[0]);
            foreach ($patterns as $p) {
                if (strpos($msg, $p) !== false) {
                    $label = $short ? " for <b>$short</b>" : "";
                    sendReply("{$hc[1]}$label\n━━━━━━━━━━━━━━━━━\n\n{$hc[2]}");
                }
            }
        }

        // Still no match — let it fall to default fallback
        $module = null;
    }

    if ($best && $bestScore > 0) {
        $catEmoji = [
            'general'     => '🏢',
            'eligibility' => '📋',
            'skills'      => '🛠️',
            'training'    => '📚',
            'interview'   => '🎤',
        ];
        $emoji = $catEmoji[$best['category']] ?? '💡';
        $label = $short ? " — <b>$short</b>" : "";
        $reply = "$emoji <b>" . ucfirst($best['category']) . "$label</b>\n━━━━━━━━━━━━━━━━━\n\n";
        $reply .= $best['answer'];
        $reply .= "\n\n💡 Ask another question or type <b>hi</b> to see all modules.";
        sendReply($reply);
    }
}

// ---- COURSES LIST ----
if ($module === 'courses') {
    $reply    = "📚 <b>Available Courses at SDNBVC</b>\n\n";
    $result   = $conn->query("SELECT course_name, short_name FROM courses ORDER BY course_id");
    $science  = "🔬 <b>Science & Technology:</b>\n";
    $commerce = "💼 <b>Commerce & Management:</b>\n";
    $arts     = "🎨 <b>Arts & Humanities:</b>\n";

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stream = getStream($row['short_name'], $scienceStreams, $commerceStreams, $artsStreams);
            $line   = "   • {$row['course_name']} ({$row['short_name']})\n";
            if ($stream === "science")       $science  .= $line;
            elseif ($stream === "commerce")  $commerce .= $line;
            else                             $arts     .= $line;
        }
    }
    $reply .= $science . "\n" . $commerce . "\n" . $arts;
    $reply .= "\n💡 Type your short name like <b>BCA</b>, <b>MCA</b>, <b>BCOM</b> for a prep plan!";
    sendReply($reply);
}

// ---- GD ----
if ($module === 'gd') {
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label  = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid    = $ci ? (int)$ci['id'] : null;
    $stream = $ci ? $ci['stream'] : null;
    $topics = [];

    $reply = "🗣️ <b>Group Discussion Topics$label</b>\n━━━━━━━━━━━━━━━━━\n";

    if ($cid) {
        $r = $conn->query("SELECT topic FROM gd_topics WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $topics[] = $row['topic'];
    }
    if (empty($topics) && $stream) {
        $safeStream = $conn->real_escape_string($stream);
        $r = $conn->query("SELECT topic FROM gd_topics WHERE stream = '$safeStream' ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $topics[] = $row['topic'];
    }
    if (empty($topics)) {
        $r = $conn->query("SELECT topic FROM gd_topics ORDER BY RAND() LIMIT 6");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $topics[] = $row['topic'];
    }
    // Last fallback: use course-specific or stream-specific hardcoded topics
    if (empty($topics)) {
        $courseTopics = [
            // Science
            'CS'      => ["Is AI replacing software engineers?","Open source vs proprietary software","Should coding be mandatory in schools?","Cloud computing: the future of data storage","Cybersecurity threats in the digital age"],
            'BCA'     => ["Is a BCA degree enough to get a software job?","Mobile apps vs web apps: which has a better future?","Are certifications more valuable than degrees in IT?","Work from home vs office for developers","Gaming industry: career opportunities for tech graduates"],
            'MCA'     => ["Agile vs Waterfall: which is more effective?","Big Data: opportunity or threat to privacy?","Is a master's degree necessary for a software career?","Role of IoT in transforming smart cities","Remote work: boon or bane for IT?"],
            'DS'      => ["Data privacy: whose responsibility is it?","Is data science a bubble that will burst?","Ethical concerns in AI and data-driven decisions","Can data predict human behavior accurately?","Role of data science in healthcare"],
            'AI'      => ["Will AI cause mass unemployment?","Autonomous vehicles: are we ready?","AI in healthcare: should machines make medical decisions?","Deepfakes and democracy","Should AI development be regulated?"],
            'CYBER'   => ["Is ethical hacking a legitimate career?","Ransomware: are companies doing enough?","Privacy vs security: finding the right balance","Social engineering: the biggest cybersecurity threat","Should governments have backdoors in encryption?"],
            'CLOUD'   => ["Is cloud making on-premise servers obsolete?","Data sovereignty: who owns your cloud data?","Multi-cloud vs single cloud strategy","Security risks in cloud adoption","Cloud gaming: the future of the industry"],
            // Commerce
            'BBA'     => ["Leadership: born or made?","Startup ecosystem in India: challenges and opportunities","Social media marketing vs traditional advertising","Is an MBA necessary after BBA?","Women in leadership: breaking the glass ceiling"],
            'BCOM'    => ["GST: has it simplified or complicated Indian taxation?","E-commerce: threat or opportunity for traditional retail?","Cryptocurrency: should India legalize it?","Financial literacy: why is it lacking in India?","Corporate social responsibility: obligation or opportunity?"],
            'FINTECH' => ["UPI and digital payments: transforming India's economy","Will cryptocurrency replace traditional banking?","BNPL: financial freedom or debt trap?","Role of AI in personal finance","Fintech startups: disrupting or complementing banks?"],
            'DM'      => ["Is influencer marketing more effective than traditional ads?","Short-form content: the future of digital marketing?","Data privacy vs personalized advertising","Can small businesses compete with big brands online?","AI in content creation: threat or tool?"],
            // Arts
            'TAMIL'   => ["Tamil language: preserving classical roots in the modern era","Should Tamil be made compulsory in Tamil Nadu schools?","Classical languages and their relevance in today's job market","Digital preservation of Tamil literature and heritage","The global recognition of Tamil as a classical language","Role of Tamil media in shaping public opinion"],
            'ENG'     => ["Is English literature still relevant in the digital age?","Should regional languages replace English in education?","The power of storytelling in shaping society","Social media and the decline of formal writing skills","Can literature create social change?"],
            'HINDI'   => ["Should Hindi be the official language of India?","The influence of English on Hindi culture","Bollywood: a catalyst for Hindi spread globally?","Hindi literature in the digital age","Is Hindi becoming a symbol of cultural dominance?"],
            'HISTORY' => ["Should colonial history be taught critically in schools?","Preservation of historical monuments: whose responsibility?","Can we learn from history to avoid future conflicts?","Role of historians in shaping national identity","Digital archives: revolutionizing historical research"],
            'ECO'     => ["Universal Basic Income: solution to poverty or economic disaster?","Is India's GDP growth benefiting the common man?","Green economy: balancing growth and sustainability","Informal economy: challenge or strength for India?","Privatization of public sector banks: good or bad?"],
            'TOURISM' => ["Is sustainable tourism possible in India?","Over-tourism: destroying the places we love?","Should India invest more in heritage tourism?","The impact of social media on travel trends","Medical tourism: India as a global destination"],
            'VISCOM'  => ["Is digital media making print media obsolete?","Ethical boundaries in photojournalism","Role of visual communication in social change","OTT platforms vs traditional cinema: who wins?","Can design thinking solve real-world problems?"],
        ];

        $streamTopics = [
            "science"  => ["Is AI replacing jobs across industries?","Should coding be mandatory in schools?","Cloud computing: the future of data storage","Big Data: opportunity or threat to privacy?","Cybersecurity: the biggest challenge of the digital age"],
            "commerce" => ["GST: simplified or complicated Indian taxation?","E-commerce: threat or opportunity for retail?","Leadership: born or made?","Women in leadership: breaking the glass ceiling","Startup ecosystem in India: challenges and opportunities"],
            "arts"     => ["Should regional languages be preserved digitally?","Role of media in shaping public opinion","Social media: tool for social change or echo chamber?","Classical languages and their relevance today","Importance of arts and humanities in the modern job market"],
        ];

        // Try course-specific first, then stream, then true generic
        if ($ci && isset($courseTopics[$ci['short']])) {
            $topics = $courseTopics[$ci['short']];
        } elseif ($stream && isset($streamTopics[$stream])) {
            $topics = $streamTopics[$stream];
        } else {
            $topics = [
                "Is Artificial Intelligence a threat to jobs?",
                "Work from home vs. office — which is better?",
                "Social media: impact on youth",
                "Digital India — reality or dream?",
                "Should college education be free?",
                "Climate change: whose responsibility?",
            ];
        }
    }

    foreach ($topics as $t) $reply .= "❓ $t\n";
    $reply .= "\n\n💡 <b>GD Tips:</b>\n";
    $reply .= "✅ Start with a strong opening statement\n";
    $reply .= "✅ Use facts and real-world examples\n";
    $reply .= "✅ Listen actively before responding\n";
    $reply .= "✅ Be assertive, not aggressive\n";
    $reply .= "✅ Summarise at the end if given the chance\n";
    $reply .= getLinks("gd", $stream ?? "");
    sendReply($reply);
}

// ---- HR ----
if ($module === 'hr') {
    $ci        = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label     = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid       = $ci ? (int)$ci['id'] : null;
    $stream    = $ci ? $ci['stream'] : null;
    $questions = [];

    $reply = "🤝 <b>HR Interview Questions$label</b>\n━━━━━━━━━━━━━━━━━\n";

    if ($cid) {
        $r = $conn->query("SELECT question FROM hr_questions WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $questions[] = $row['question'];
    }
    if (empty($questions)) {
        $r = $conn->query("SELECT question FROM hr_questions ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $questions[] = $row['question'];
    }
    if (empty($questions)) {
        $questions = [
            "Tell me about yourself.",
            "What are your strengths and weaknesses?",
            "Where do you see yourself in 5 years?",
            "Why should we hire you?",
            "How do you handle pressure or stress?",
        ];
    }

    foreach ($questions as $q) $reply .= "❓ $q\n\n";
    $reply .= "\n💡 <b>HR Round Tips:</b>\n";
    $reply .= "✅ Prepare a confident 2-minute self-introduction\n";
    $reply .= "✅ Research the company before the interview\n";
    $reply .= "✅ Use the STAR method for behavioral questions\n";
    $reply .= "✅ Maintain eye contact and positive body language\n";
    $reply .= getLinks("hr", $stream ?? "");
    sendReply($reply);
}

// ---- MOCK INTERVIEW ----
if ($module === 'mock') {
    $ci        = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label     = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid       = $ci ? (int)$ci['id'] : null;
    $stream    = $ci ? $ci['stream'] : null;
    $questions = [];

    $reply  = "🎤 <b>Mock Interview Session$label</b>\n━━━━━━━━━━━━━━━━━\n";
    $reply .= "Answer each question as if you're in a real interview:\n\n";

    if ($cid) {
        $r = $conn->query("SELECT question FROM mock_interview_questions WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $questions[] = $row['question'];
    }
    if (empty($questions)) {
        $r = $conn->query("SELECT question FROM mock_interview_questions ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $questions[] = $row['question'];
    }
    if (empty($questions)) {
        $questions = [
            "Tell me about yourself and your background.",
            "Why should we hire you for this role?",
            "Explain one project you are proud of.",
            "What are your technical strengths?",
            "How do you handle a situation where your solution is not working?",
        ];
    }

    foreach ($questions as $q) $reply .= "❓ $q\n\n";
    $reply .= "\n💡 <b>Mock Interview Tips:</b>\n";
    $reply .= "✅ Use STAR method (Situation, Task, Action, Result)\n";
    $reply .= "✅ Keep each answer under 2 minutes\n";
    $reply .= "✅ End with a question for the interviewer\n";
    $reply .= "✅ Speak clearly and maintain eye contact\n";
    $reply .= getLinks("mock", $stream ?? "");
    sendReply($reply);
}

// ---- APTITUDE ----
if ($module === 'aptitude') {
    $ci    = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid   = $ci ? (int)$ci['id'] : null;
    $rows  = [];

    $reply = "🧮 <b>Aptitude Practice Questions$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    if ($cid) {
        $r = $conn->query("SELECT question, answer, topic FROM aptitude_questions WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $rows[] = $row;
    }
    if (empty($rows)) {
        $r = $conn->query("SELECT question, answer, topic FROM aptitude_questions ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $rows[] = $row;
    }
    if (empty($rows)) {
        sendReply("📌 No aptitude questions found in the database yet." . getLinks("aptitude"));
    }

    $i = 1;
    foreach ($rows as $row) {
        $topic  = !empty($row['topic']) ? "[{$row['topic']}] " : "";
        $reply .= "$i. {$topic}{$row['question']}\n";
        $reply .= "   ✅ <b>Answer:</b> {$row['answer']}\n\n";
        $i++;
    }
    $reply .= "💡 Type <b>aptitude</b> again for more questions!";
    $reply .= getLinks("aptitude");
    sendReply($reply);
}

// ---- TECHNICAL ----
if ($module === 'technical') {
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label  = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid    = $ci ? (int)$ci['id'] : null;
    $stream = $ci ? $ci['stream'] : null;
    $rows   = [];

    $reply = "💻 <b>Technical Questions$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    if ($cid) {
        $r = $conn->query("SELECT question FROM technical_questions WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $rows[] = $row['question'];
    }

    // If no course-specific technical questions, show coding topics from the general table
    if (empty($rows)) {
        $r = $conn->query("SELECT topic, description FROM coding_topics ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                $reply .= "🔹 <b>{$row['topic']}</b>\n   {$row['description']}\n\n";
            }
            $reply .= getLinks("technical", $stream ?? "");
            sendReply($reply);
        }
    }

    if (!empty($rows)) {
        foreach ($rows as $q) $reply .= "❓ $q\n\n";
    } else {
        $reply .= "❓ Explain the concepts of Object-Oriented Programming.\n\n";
        $reply .= "❓ What is the difference between a stack and a queue?\n\n";
        $reply .= "❓ Explain database normalization with an example.\n\n";
        $reply .= "❓ What is time complexity? Give an example.\n\n";
        $reply .= "❓ Describe a project you built and the technologies used.\n\n";
    }
    $reply .= getLinks("technical", $stream ?? "");
    sendReply($reply);
}

// ---- CODING ----
if ($module === 'coding') {
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label  = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid    = $ci ? (int)$ci['id'] : null;
    $stream = $ci ? $ci['stream'] : null;
    $rows   = [];

    $reply = "👨‍💻 <b>Coding Practice Problems$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    // Schema: coding_problems (id, course_id, problem, difficulty, hint)
    if ($cid) {
        $r = $conn->query("SELECT problem, difficulty, hint FROM coding_problems WHERE course_id = $cid ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $rows[] = $row;
    }
    if (empty($rows)) {
        $r = $conn->query("SELECT problem, difficulty, hint FROM coding_problems ORDER BY RAND() LIMIT 5");
        if ($r && $r->num_rows > 0) while ($row = $r->fetch_assoc()) $rows[] = $row;
    }

    if (!empty($rows)) {
        $i = 1;
        foreach ($rows as $row) {
            $diff   = !empty($row['difficulty']) ? " [{$row['difficulty']}]" : "";
            $hint   = !empty($row['hint'])        ? "\n   💡 Hint: {$row['hint']}" : "";
            $reply .= "$i. 🔸 {$row['problem']}$diff$hint\n\n";
            $i++;
        }
    } else {
        // Hard fallback by stream
        $problemsByStream = [
            "science"  => [
                ["Reverse a string without using built-in functions",   "Easy",   "Use a loop and swap characters"],
                ["Find the second largest element in an array",          "Easy",   "One pass with two variables"],
                ["Check if a number is a palindrome",                    "Easy",   "Compare reversed digits"],
                ["Implement a stack using arrays",                        "Medium", "Use push/pop with a top pointer"],
                ["Write SQL to find duplicate records",                   "Medium", "GROUP BY with HAVING COUNT > 1"],
            ],
            "commerce" => [
                ["Excel formula to calculate compound interest",          "Easy",   "Use the FV() function"],
                ["SQL: find top 5 customers by total sales",              "Easy",   "ORDER BY sales DESC LIMIT 5"],
                ["Python script to read and summarise a CSV file",        "Medium", "Use pandas read_csv()"],
                ["Calculate GST on a product list using Excel",           "Easy",   "Multiply price × tax rate"],
                ["SQL: find average salary by department",                "Easy",   "GROUP BY dept, AVG(salary)"],
            ],
            "arts"     => [
                ["Python: count word frequency in a paragraph",           "Easy",   "split() + dictionary"],
                ["Create a basic HTML resume page",                       "Easy",   "Use h1, p, ul tags"],
                ["SQL: search records by keyword",                        "Easy",   "LIKE '%keyword%'"],
                ["Build a to-do list with HTML + JavaScript",             "Medium", "Use DOM manipulation"],
                ["Sort a list of names alphabetically in Python",         "Easy",   "Use sorted() or .sort()"],
            ],
        ];
        $list = $problemsByStream[$stream] ?? $problemsByStream["science"];
        $i = 1;
        foreach ($list as $p) {
            $reply .= "$i. 🔸 {$p[0]} [{$p[1]}]\n   💡 Hint: {$p[2]}\n\n";
            $i++;
        }
    }

    $reply .= "💡 <b>Coding Tips:</b>\n";
    $reply .= "✅ Always analyse time & space complexity\n";
    $reply .= "✅ Write pseudocode before coding\n";
    $reply .= "✅ Test with edge cases (empty input, large numbers)\n";
    $reply .= "✅ Practice daily on LeetCode or HackerRank\n";
    $reply .= "✅ Explain your approach clearly in interviews\n";
    $reply .= getLinks("coding", $stream ?? "");
    sendReply($reply);
}

// ---- SKILLS ----
if ($module === 'skills') {
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label  = $ci ? " for <b>{$ci['short']}</b>" : "";
    $cid    = $ci ? (int)$ci['id'] : null;
    $stream = $ci ? $ci['stream'] : null;

    $reply = "🛠️ <b>Skills Needed for Placement$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    // Schema: skills (id, course_id, skill_name, skill_type)
    $query  = $cid
        ? "SELECT skill_name, skill_type FROM skills WHERE course_id = $cid ORDER BY skill_type, skill_name LIMIT 30"
        : "SELECT skill_name, skill_type FROM skills ORDER BY skill_type, skill_name LIMIT 30";
    $result = $conn->query($query);
    $grouped = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sName = trim($row['skill_name']);
            $sType = trim($row['skill_type'] ?? 'General');
            if ($sName) $grouped[$sType][] = $sName;
        }
    }

    if (!empty($grouped)) {
        foreach ($grouped as $type => $skillList) {
            $lower  = strtolower($type);
            $emoji  = (strpos($lower,'technical') !== false || strpos($lower,'hard') !== false) ? "🔬"
                    : (strpos($lower,'soft') !== false ? "💬" : "🎯");
            $reply .= "$emoji <b>$type:</b>\n";
            foreach (array_unique($skillList) as $sk) $reply .= "   ✅ $sk\n";
            $reply .= "\n";
        }
    } else {
        $defaults = [
            "science"  => ["🔬 Technical" => ["Java","Python","SQL","HTML","CSS","Data Structures","Git"],
                           "💬 Soft"      => ["Communication","Teamwork","Problem Solving"]],
            "commerce" => ["💼 Technical" => ["Excel","Tally","SAP Basics","Accounting","GST"],
                           "💬 Soft"      => ["Communication","Presentation","Leadership"]],
            "arts"     => ["🎨 Core"      => ["MS Office","Content Writing","Communication"],
                           "💬 Soft"      => ["Presentation","Creativity","Critical Thinking"]],
        ];
        $set = $defaults[$stream] ?? $defaults["science"];
        foreach ($set as $type => $skillList) {
            $reply .= "<b>$type:</b>\n";
            foreach ($skillList as $sk) $reply .= "   ✅ $sk\n";
            $reply .= "\n";
        }
    }
    $reply .= getLinks("skills");
    sendReply($reply);
}

// ---- RESUME ----
if ($module === 'resume') {
    $ci    = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $label = $ci ? " for <b>{$ci['short']}</b>" : "";

    $reply = "📝 <b>Resume Building Guide$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    // Schema: resume_tips (id, tip)
    $result = $conn->query("SELECT tip FROM resume_tips ORDER BY id LIMIT 12");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reply .= "✅ {$row['tip']}\n";
        }
    } else {
        $reply .= "✅ Keep your resume to 1 page for freshers.\n";
        $reply .= "✅ Use ATS-friendly fonts: Arial or Calibri, size 11–12.\n";
        $reply .= "✅ Start bullets with action verbs: Developed, Built, Designed.\n";
        $reply .= "✅ Quantify achievements with numbers wherever possible.\n";
        $reply .= "✅ Avoid photos, tables, and graphics — they confuse ATS.\n";
    }

    $reply .= "\n📌 <b>Ideal Resume Sections (in order):</b>\n";
    $reply .= "1. Name & Contact Info\n2. Professional Objective\n3. Education\n";
    $reply .= "4. Skills\n5. Projects\n6. Certifications\n7. Extra-curriculars\n\n";
    $reply .= "💡 Type <b>ats</b> to check your resume's ATS score!";
    $reply .= getLinks("resume");
    sendReply($reply);
}

// ---- ATS (Step 1: Set course, then ask for resume text) ----
if ($module === 'ats') {
    $atsCourse = $activeCourse;

    // User said "ats for BCA" — course is already resolved in $atsCourse
    if ($parsedModule === 'ats' && $parsedCourse && $atsCourse) {
        $_SESSION['ats_step']   = 'waiting_resume';
        $_SESSION['ats_course'] = $atsCourse;
        $short = $atsCourse['short_name'];
        sendReply(
            "📄 <b>ATS Resume Checker — Course: $short</b>\n━━━━━━━━━━━━━━━━━\n\n" .
            "✅ Course set to <b>$short</b>.\n\n" .
            "📋 <b>How to check your resume:</b>\n\n" .
            "✏️ Open your resume (PDF, Word, or any file), select all the text, copy it, and paste it directly into the chat box below.\n\n" .
            "I will then:\n" .
            "   🔍 Check it against ATS keywords for <b>$short</b>\n" .
            "   📊 Give you an ATS score out of 100%\n" .
            "   ❌ List which important keywords are missing\n\n" .
            "👇 <b>Paste your resume text now:</b>"
        );
    }

    // Generic ATS prompt — no course specified yet
    sendReply(
        "📄 <b>ATS Resume Checker</b>\n━━━━━━━━━━━━━━━━━\n\n" .
        "To give you accurate results, I need to know your course first!\n\n" .
        "👉 <b>Type:</b> ats for BCA · ats for BCOM · ats for TAMIL\n\n" .
        "🔬 Science: BCA, MCA, CS, AI, DS, CYBER, CLOUD, MATHS\n" .
        "💼 Commerce: BBA, BCOM, FINTECH, BIM, DM, IB, AF\n" .
        "🎨 Arts: ENG, TAMIL, HINDI, HISTORY, TOURISM, VISCOM\n\n" .
        "💡 After typing your course, paste your resume text to get your ATS score!"
    );
}

// ---- COMPANY ----
if ($module === 'company') {

    // ── Step 1: Resolve stream alias if user typed stream name ────────────────
    $streamAliasMap = [
        'science'   => 'science', 'tech'      => 'science',
        'it'        => 'science', 'computer'  => 'science',
        'commerce'  => 'commerce','business'  => 'commerce',
        'finance'   => 'commerce','management'=> 'commerce',
        'arts'      => 'arts',    'language'  => 'arts',
        'humanity'  => 'arts',    'humanities'=> 'arts',
        'vocational'=> 'arts',
    ];

    $directStream = null;
    if ($parsedCourse) {
        $parsedLower = strtolower(trim($parsedCourse));
        if (isset($streamAliasMap[$parsedLower])) $directStream = $streamAliasMap[$parsedLower];
    }
    if (!$directStream) {
        foreach ($streamAliasMap as $alias => $sv) {
            if (strpos($msg, $alias) !== false) { $directStream = $sv; break; }
        }
    }

    // ── Step 2: Course info ───────────────────────────────────────────────────
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $cid    = $ci ? (int)$ci['id'] : null;
    $stream = $directStream ?? ($ci ? $ci['stream'] : null);

    // ── Step 3: Build label ───────────────────────────────────────────────────
    $streamEmojis = ['science' => '🔬', 'commerce' => '💼', 'arts' => '🎨'];
    if ($ci && !$directStream) {
        $label = " for <b>{$ci['short']}</b> ({$ci['name']})";
        $emoji = $ci['emoji'];
    } elseif ($directStream) {
        $label = " for <b>" . ucfirst($directStream) . " Stream</b>";
        $emoji = $streamEmojis[$directStream] ?? '🎓';
    } else {
        $label = ""; $emoji = "🏢";
    }

    $reply = "$emoji <b>Companies Visiting for Placement$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    // ── Step 4: Query — course_id first, then stream fallback ─────────────────
    $result = null; $found = false;

    // Priority 1: Specific course companies (course_id match)
    if ($cid && !$directStream) {
        $result = $conn->query(
            "SELECT company_name, role, salary, type
             FROM companies
             WHERE course_id = $cid
             ORDER BY company_name LIMIT 15"
        );
    }

    // Priority 2: Stream-wide (user typed stream name OR no course-specific results)
    if (!$result || $result->num_rows === 0) {
        if ($stream) {
            $safeStream = $conn->real_escape_string($stream);
            // When showing stream-wide, exclude companies tied to unrelated courses
            $result = $conn->query(
                "SELECT company_name, role, salary, type
                 FROM companies
                 WHERE stream = '$safeStream'
                   AND (course_id IS NULL OR course_id = " . ($cid ?? 0) . ")
                 ORDER BY company_name LIMIT 15"
            );
            // If that returns nothing, show all stream companies
            if (!$result || $result->num_rows === 0) {
                $result = $conn->query(
                    "SELECT company_name, role, salary, type
                     FROM companies
                     WHERE stream = '$safeStream'
                     ORDER BY company_name LIMIT 15"
                );
            }
        } else {
            // No stream — show all grouped by stream
            $result = $conn->query(
                "SELECT company_name, role, salary, type, stream
                 FROM companies
                 WHERE course_id IS NULL
                 ORDER BY stream, company_name LIMIT 20"
            );
        }
    }

    // ── Step 5: Render DB results ─────────────────────────────────────────────
    if ($result && $result->num_rows > 0) {
        $currentStream = null;
        while ($row = $result->fetch_assoc()) {
            if (!$stream && isset($row['stream']) && $row['stream'] !== $currentStream) {
                $currentStream = $row['stream'];
                $secEmoji = $streamEmojis[$currentStream] ?? '🏢';
                $reply .= "\n$secEmoji <b>" . ucfirst($currentStream) . " Companies:</b>\n";
            }
            $reply .= "🔹 <b>{$row['company_name']}</b>\n";
            if (!empty($row['role']))   $reply .= "   💼 Role: {$row['role']}\n";
            if (!empty($row['salary'])) $reply .= "   💰 Package: {$row['salary']}\n";
            if (!empty($row['type']))   $reply .= "   📋 Drive Type: {$row['type']}\n";
            $reply .= "\n";
            $found = true;
        }
    }

    // ── Step 6: Hardcoded fallback if DB empty ────────────────────────────────
    if (!$found) {
        $courseDefaults = [
            // Science/Tech
            'CS'     => [['TCS','Software Developer','₹3.5 LPA','On-Campus'],['Infosys','Systems Engineer','₹3.6 LPA','On-Campus'],['Wipro','Project Engineer','₹3.5 LPA','On-Campus'],['Cognizant','Programmer Analyst','₹4 LPA','On-Campus'],['Zoho','Software Developer','₹6–8 LPA','Referral']],
            'BCA'    => [['TCS','Software Developer','₹3.5 LPA','On-Campus'],['Infosys','Systems Engineer','₹3.6 LPA','On-Campus'],['Wipro','Project Engineer','₹3.5 LPA','On-Campus'],['HCL Technologies','Graduate Engineer Trainee','₹3.5 LPA','On-Campus'],['Capgemini','Analyst','₹3.8 LPA','On-Campus']],
            'MCA'    => [['TCS','Systems Engineer','₹3.6 LPA','On-Campus'],['Infosys','Software Engineer','₹4 LPA','On-Campus'],['Accenture','Associate Software Engineer','₹4.5 LPA','On-Campus'],['Zoho','Software Developer','₹6 LPA','Referral'],['Freshworks','Junior Developer','₹7 LPA','Off-Campus']],
            'AI'     => [['Google India','Data Scientist','₹18–25 LPA','Off-Campus'],['Amazon','Applied Scientist','₹15–22 LPA','Off-Campus'],['Wipro AI360','ML Engineer','₹8–12 LPA','Off-Campus'],['TCS Research','AI Engineer','₹6–8 LPA','On-Campus'],['Zoho','AI Developer','₹7 LPA','Referral']],
            'DS'     => [['Mu Sigma','Data Analyst','₹4–6 LPA','On-Campus'],['Fractal Analytics','Business Analyst','₹5–7 LPA','Off-Campus'],['Latent View Analytics','Data Analyst','₹4–6 LPA','Off-Campus'],['TCS','Data Analyst','₹3.6 LPA','On-Campus'],['Infosys','Data Engineer','₹4 LPA','On-Campus']],
            'MSDAI'  => [['Google India','Data Scientist','₹18–25 LPA','Off-Campus'],['Amazon','Applied Scientist','₹15–22 LPA','Off-Campus'],['Microsoft','ML Engineer','₹15–20 LPA','Off-Campus'],['Fractal Analytics','Senior Analyst','₹8–10 LPA','Off-Campus'],['Wipro AI360','ML Engineer','₹8–12 LPA','Off-Campus']],
            'CYBER'  => [['TCS Cyber Security','Security Analyst','₹4.5 LPA','On-Campus'],['Wipro','Cyber Analyst','₹4 LPA','On-Campus'],['IBM Security','Security Engineer','₹5 LPA','Off-Campus'],['HCL','Security Operations','₹4 LPA','On-Campus'],['Infosys','Cyber Security Analyst','₹4.2 LPA','On-Campus']],
            'CLOUD'  => [['AWS India','Cloud Support Engineer','₹6 LPA','Off-Campus'],['TCS','Cloud Engineer','₹4 LPA','On-Campus'],['Accenture','Cloud Analyst','₹4.5 LPA','On-Campus'],['Infosys','Cloud Developer','₹4 LPA','On-Campus'],['Wipro','Cloud Associate','₹3.8 LPA','On-Campus']],
            'MSBIO'  => [['IQVIA','Biostatistician I','₹6–9 LPA','Off-Campus'],['Parexel','Statistical Programmer','₹5–8 LPA','Off-Campus'],['Quintiles','Clinical Data Analyst','₹5–7 LPA','Off-Campus']],
            'MSCHEM' => [['Cipla','Research Associate','₹4–6 LPA','Off-Campus'],['Dr. Reddy\'s','Analytical Chemist','₹4.5–6.5 LPA','Off-Campus'],['Sun Pharma','QC Chemist','₹3.5–5.5 LPA','Off-Campus']],
            'MSHSN'  => [['Apollo Hospitals','Clinical Dietician','₹4–6 LPA','Off-Campus'],['Nestlé India','Nutrition Specialist','₹5–7 LPA','Off-Campus'],['ITC Foods','Food Technologist','₹5–7 LPA','Off-Campus']],
            'MSCP'   => [['Vandrevala Foundation','Counsellor','₹4–6 LPA','Off-Campus'],['iCall (TISS)','Mental Health Counsellor','₹4–5.5 LPA','Off-Campus'],['Apollo Hospitals','Clinical Psychologist Trainee','₹4–6 LPA','Off-Campus']],
            'PBPB'   => [['Biocon','Research Trainee','₹3–5 LPA','Off-Campus'],['Syngenta India','Plant Scientist Trainee','₹3.5–5 LPA','Off-Campus'],['National Seeds Corporation','Field Scientist','₹3–4 LPA','Off-Campus']],
            // Commerce
            'BCOM'   => [['Deloitte','Business Analyst','₹5 LPA','On-Campus'],['HDFC Bank','Banking Associate','₹3–4 LPA','On-Campus'],['ICICI Bank','Relationship Manager','₹3.5 LPA','On-Campus'],['KPMG','Audit Associate','₹5 LPA','On-Campus'],['EY','Associate','₹4.5 LPA','On-Campus']],
            'BBA'    => [['Amazon','Operations Trainee','₹4 LPA','Off-Campus'],['Deloitte','Business Analyst','₹5 LPA','On-Campus'],['HDFC Bank','Banking Associate','₹3.5 LPA','On-Campus'],['Gartner','Research Associate','₹4 LPA','Off-Campus'],['Infosys BPO','Process Executive','₹2.5 LPA','On-Campus']],
            'FINTECH'=> [['Razorpay','Fintech Analyst','₹5–7 LPA','Off-Campus'],['Paytm','Operations Analyst','₹4–6 LPA','Off-Campus'],['HDFC Bank','Digital Banking Associate','₹4 LPA','On-Campus'],['ICICI Bank','Fintech Executive','₹3.5 LPA','On-Campus']],
            'MBA'    => [['McKinsey & Company','Business Analyst','₹20–30 LPA','Off-Campus'],['HUL','Management Trainee','₹10–14 LPA','On-Campus'],['Amazon India','Operations Manager','₹12–18 LPA','Off-Campus'],['HDFC Bank','Asst Manager','₹7–10 LPA','On-Campus']],
            'MAHRM'  => [['Deloitte','HR Associate','₹5–6 LPA','On-Campus'],['Amazon','HR Coordinator','₹5–7 LPA','Off-Campus'],['Infosys BPO','HR Executive','₹3.5–5 LPA','On-Campus']],
            'MCOMAF' => [['Deloitte','Senior Audit Associate','₹6–8 LPA','On-Campus'],['EY','Senior Assurance Associate','₹6.5–8 LPA','On-Campus'],['KPMG','Senior Tax Associate','₹6–8 LPA','On-Campus']],
            // Arts
            'ENG'    => [['Concentrix','Customer Support Executive','₹2.5 LPA','On-Campus'],['Sutherland','Process Associate','₹2.5 LPA','On-Campus'],['EY','Content & Communications','₹3.5 LPA','Off-Campus'],['Times of India','Content Writer Trainee','₹2.5 LPA','Off-Campus']],
            'TAMIL'  => [['Concentrix','Tamil Support Executive','₹2.5 LPA','On-Campus'],['Sutherland','Language Support','₹2.5 LPA','On-Campus'],['HCL BPO','Support Executive','₹2.5 LPA','On-Campus']],
            'TOURISM'=> [['MakeMyTrip','Travel Consultant','₹3 LPA','Off-Campus'],['Thomas Cook','Tour Executive','₹2.5 LPA','Off-Campus'],['Cox & Kings','Travel Associate','₹2.5 LPA','Off-Campus']],
            'VISCOM' => [['Times of India','Visual Content Trainee','₹2.5 LPA','Off-Campus'],['Ogilvy','Junior Creative','₹3 LPA','Off-Campus'],['Hotstar','Content Associate','₹3 LPA','Off-Campus']],
            'MSW'    => [['CRY India','Programme Officer','₹3–4.5 LPA','Off-Campus'],['UNICEF India','Programme Associate','₹3.5–5 LPA','Off-Campus'],['Aga Khan Foundation','Field Coordinator','₹3–4 LPA','Off-Campus']],
            'MAECO'  => [['NITI Aayog','Research Officer','₹6–9 LPA','Off-Campus'],['RBI','Research Officer','₹7–10 LPA','Off-Campus'],['McKinsey & Company','Business Analyst','₹12–16 LPA','Off-Campus']],
        ];

        $streamDefaults = [
            "science"  => [['TCS','Software Developer','₹3.5 LPA','On-Campus'],['Infosys','Systems Engineer','₹3.6 LPA','On-Campus'],['Wipro','Project Engineer','₹3.5 LPA','On-Campus'],['Cognizant','Programmer Analyst','₹4 LPA','On-Campus'],['Accenture','Associate Software Engineer','₹4.5 LPA','On-Campus']],
            "commerce" => [['Deloitte','Business Analyst','₹5 LPA','On-Campus'],['HDFC Bank','Banking Associate','₹3–4 LPA','On-Campus'],['ICICI Bank','Relationship Manager','₹3.5 LPA','On-Campus'],['EY','Associate','₹4.5 LPA','On-Campus'],['KPMG','Audit Associate','₹5 LPA','On-Campus']],
            "arts"     => [['Concentrix','Customer Support Executive','₹2.5 LPA','On-Campus'],['Sutherland','Process Associate','₹2.5 LPA','On-Campus'],['EY','Content & Communications','₹3.5 LPA','Off-Campus'],['MakeMyTrip','Travel Consultant','₹3 LPA','Off-Campus'],['HCL BPO','Support Executive','₹2.5 LPA','On-Campus']],
        ];

        $short = $ci ? $ci['short'] : null;
        if ($short && isset($courseDefaults[$short])) {
            $list = $courseDefaults[$short];
        } elseif ($stream && isset($streamDefaults[$stream])) {
            $list = $streamDefaults[$stream];
        } else {
            $list = array_merge($streamDefaults['science'], $streamDefaults['commerce']);
        }
        foreach ($list as $c) {
            $reply .= "🔹 <b>{$c[0]}</b>\n   💼 Role: {$c[1]}\n   💰 Package: {$c[2]}\n   📋 Type: {$c[3]}\n\n";
        }
    }

    // ── Step 7: Footer ────────────────────────────────────────────────────────
    $reply .= "━━━━━━━━━━━━━━━━━\n";
    $reply .= "💡 Try: company for BCA · company for BCOM · company for MBA · company for ENG\n";
    $reply .= getLinks("company");
    sendReply($reply);
}

// ---- URGENT ----
if ($module === 'urgent') {
    sendReply(
        "⚡ <b>Quick Placement Prep Guide!</b>\n\n" .
        "📌 <b>Tonight's Checklist:</b>\n" .
        "1️⃣ Prepare your 2-minute self-introduction\n" .
        "2️⃣ Review your resume top to bottom\n" .
        "3️⃣ Know your project inside-out\n" .
        "4️⃣ Revise 5 HR questions\n" .
        "5️⃣ Sleep early — be fresh!\n\n" .
        "📌 <b>Tomorrow Morning:</b>\n" .
        "✅ Dress professionally\n" .
        "✅ Carry 3 printed copies of your resume\n" .
        "✅ Arrive 15 minutes early\n" .
        "✅ Keep your phone on silent\n\n" .
        "💬 Try now: hr · mock · aptitude\n\n" .
        "<b>All the best! You've got this! 💪🎯</b>" .
        getLinks("hr")
    );
}

// ============================================================
// DEFAULT FALLBACK — Try FAQ fuzzy match first, then natural reply
// ============================================================
$fallbackCourse = $activeCourse ? $activeCourse['short_name'] : null;

// Attempt a loose FAQ match on any unhandled message
$faqDeptFb  = $fallbackCourse ? getFaqDept($fallbackCourse) : 'general';
$deptsInFb  = array_unique([$faqDeptFb, 'general']);
$deptInFb   = "'" . implode("','", array_map(fn($d) => $conn->real_escape_string($d), $deptsInFb)) . "'";
$resultFb   = $conn->query("SELECT * FROM placement_faq WHERE dept IN ($deptInFb) LIMIT 200");
$rowsFb     = [];
if ($resultFb && $resultFb->num_rows > 0) {
    while ($row = $resultFb->fetch_assoc()) $rowsFb[] = $row;
}
$bestFb = null; $bestScoreFb = 0;
foreach ($rowsFb as $row) {
    $s = scoreFaqRow($row, $msg);
    if ($s > $bestScoreFb) { $bestScoreFb = $s; $bestFb = $row; }
}
if ($bestFb && $bestScoreFb >= 1) {
    $catEmoji = ['general'=>'🏢','eligibility'=>'📋','skills'=>'🛠️','training'=>'📚','interview'=>'🎤'];
    $emoji    = $catEmoji[$bestFb['category']] ?? '💡';
    $lbl      = $fallbackCourse ? " — <b>$fallbackCourse</b>" : "";
    $reply    = "$emoji <b>" . ucfirst($bestFb['category']) . "$lbl</b>\n━━━━━━━━━━━━━━━━━\n\n";
    $reply   .= $bestFb['answer'];
    $reply   .= "\n\n💡 Ask another question or type <b>hi</b> to see all modules.";
    sendReply($reply);
}

// ── Simple fallback ───────────────────────────────────────────────────────────
$courseTip = $fallbackCourse
    ? "Try: <b>HR for $fallbackCourse</b> · <b>GD for $fallbackCourse</b> · <b>SKILLS for $fallbackCourse</b>"
    : "First type your course (e.g. <b>BCA</b>, <b>BCOM</b>, <b>ENG</b>), then ask your question.";

sendReply("😊 Sorry, I didn't understand that.\n\n" . $courseTip . "\n\nType <b>hi</b> to see all options.");
?>
