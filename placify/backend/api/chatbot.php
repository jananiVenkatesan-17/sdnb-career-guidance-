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
// STREAM DEFINITIONS  (matches courses in dataset.sql)
// ============================================================
$scienceStreams  = ["CS","BCA","MCA","MSC CS","COGNITIVE","DS","AI","DSAI",
                    "CLOUD","CYBER","MATHS","PHYSICS","CHEM","PSY","CND","NFSM","EVS"];
$commerceStreams = ["BCOM","CORP","AF","HONS","BIM","BCOM CA","PA","ISM",
                    "FINTECH","IB","BF","MCOM","BBA","DM","FASHION","AIRPORT"];
$artsStreams     = ["ENG","HISTORY","ECO","TAMIL","HINDI","SANSKRIT",
                    "FRENCH","TOURISM","MA ENG","MA TAMIL","VISCOM","SOC"];

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
$msg      = mb_strtolower($message, 'UTF-8');

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
    // Basic sciences
    $sci = ['MATHS','PHYSICS','CHEM','PSY','CND','NFSM','EVS'];
    if (in_array($short, $sci)) return 'basic_science';
    // Management
    $mgmt = ['BBA','DM','MCOM'];
    if (in_array($short, $mgmt)) return 'management';
    // Vocational
    $voc = ['FASHION','AIRPORT'];
    if (in_array($short, $voc)) return 'vocational';
    // Commerce
    $com = ['BCOM','CORP','AF','HONS','BIM','BCOM CA','PA','ISM','FINTECH','IB','BF'];
    if (in_array($short, $com)) return 'commerce';
    // Arts
    $arts = ['ENG','HISTORY','ECO','TAMIL','HINDI','SANSKRIT','FRENCH','TOURISM','MA ENG','MA TAMIL','VISCOM','SOC'];
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
            "📋 <b>Now paste your full resume text below</b> and I will:\n" .
            "   🔍 Check it against the ATS keywords for <b>$short</b>\n" .
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

    // ── Step 1: Resolve stream ────────────────────────────────────────────────
    // Priority: explicit stream word in message > active course stream > none

    // Map stream aliases the user might type
    $streamAliasMap = [
        'science'  => 'science',
        'tech'     => 'science',
        'it'       => 'science',
        'computer' => 'science',
        'commerce' => 'commerce',
        'business' => 'commerce',
        'finance'  => 'commerce',
        'arts'     => 'arts',
        'language' => 'arts',
        'humanity' => 'arts',
        'humanities'=> 'arts',
        'management'=> 'commerce',
        'vocational'=> 'arts',
    ];

    // Check if user typed a stream name directly (e.g. "company for arts")
    $directStream = null;
    if ($parsedCourse) {
        $parsedLower = strtolower(trim($parsedCourse));
        if (isset($streamAliasMap[$parsedLower])) {
            $directStream = $streamAliasMap[$parsedLower];
        }
    }
    // Also scan the raw message for stream keywords
    if (!$directStream) {
        foreach ($streamAliasMap as $alias => $streamVal) {
            if (strpos($msg, $alias) !== false) {
                $directStream = $streamVal;
                break;
            }
        }
    }

    // Course info from active course
    $ci     = $activeCourse ? getCourseInfo($activeCourse, $scienceStreams, $commerceStreams, $artsStreams) : null;
    $stream = $directStream ?? ($ci ? $ci['stream'] : null);

    // ── Step 2: Build label ───────────────────────────────────────────────────
    $streamEmojis = ['science' => '🔬', 'commerce' => '💼', 'arts' => '🎨'];
    if ($directStream) {
        // User typed a stream name — e.g. "company for arts"
        $label = " for <b>" . ucfirst($directStream) . " Stream</b>";
        $emoji = $streamEmojis[$directStream] ?? '🎓';
    } elseif ($ci) {
        // User has an active course — e.g. "company for BCA"
        $label = " for <b>{$ci['short']}</b> ({$ci['name']})";
        $emoji = $ci['emoji'];
    } else {
        $label = "";
        $emoji = "🏢";
    }

    $reply = "$emoji <b>Companies Visiting for Placement$label</b>\n━━━━━━━━━━━━━━━━━\n\n";

    // ── Step 3: Query DB ──────────────────────────────────────────────────────
    // companies table: (id, company_name, role, salary, type, stream)
    $result = null;
    if ($stream) {
        $safeStream = $conn->real_escape_string($stream);
        $result = $conn->query(
            "SELECT company_name, role, salary, type, stream
             FROM companies
             WHERE stream = '$safeStream'
             ORDER BY company_name
             LIMIT 15"
        );
    } else {
        // No stream — show all, grouped by stream
        $result = $conn->query(
            "SELECT company_name, role, salary, type, stream
             FROM companies
             ORDER BY stream, company_name
             LIMIT 20"
        );
    }

    // ── Step 4: Render results ────────────────────────────────────────────────
    $found = false;
    if ($result && $result->num_rows > 0) {
        $currentStream = null;
        while ($row = $result->fetch_assoc()) {
            // If showing all streams, print a section header per stream
            if (!$stream && $row['stream'] !== $currentStream) {
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

    // ── Step 5: Hardcoded fallback if DB empty ────────────────────────────────
    if (!$found) {
        $defaults = [
            "science"  => [
                ["TCS",           "Software Developer",        "₹3.5 LPA", "On-Campus"],
                ["Infosys",       "Systems Engineer",          "₹3.6 LPA", "On-Campus"],
                ["Wipro",         "Project Engineer",          "₹3.5 LPA", "On-Campus"],
                ["Cognizant",     "Programmer Analyst",        "₹4 LPA",   "On-Campus"],
                ["Zoho",          "Software Developer",        "₹6–8 LPA", "Referral"],
                ["HCL Technologies","Graduate Engineer Trainee","₹3.5 LPA","On-Campus"],
                ["Accenture",     "Associate Software Engineer","₹4.5 LPA","On-Campus"],
                ["Capgemini",     "Analyst",                   "₹3.8 LPA", "On-Campus"],
                ["Tech Mahindra", "Graduate Trainee",          "₹3.5 LPA", "On-Campus"],
                ["Freshworks",    "Junior Developer",          "₹7 LPA",   "Off-Campus"],
            ],
            "commerce" => [
                ["Deloitte",      "Business Analyst",          "₹5 LPA",   "On-Campus"],
                ["HDFC Bank",     "Banking Associate",         "₹3–4 LPA", "On-Campus"],
                ["ICICI Bank",    "Relationship Manager",      "₹3.5 LPA", "On-Campus"],
                ["Axis Bank",     "Banking Executive",         "₹3 LPA",   "On-Campus"],
                ["KPMG",          "Audit Associate",           "₹5 LPA",   "On-Campus"],
                ["EY",            "Associate",                 "₹4.5 LPA", "On-Campus"],
                ["PwC",           "Junior Associate",          "₹4 LPA",   "On-Campus"],
                ["Infosys BPO",   "Process Executive",         "₹2.5 LPA", "On-Campus"],
                ["Bajaj Finserv", "Sales Executive",           "₹3 LPA",   "On-Campus"],
                ["Gartner",       "Research Associate",        "₹4 LPA",   "Off-Campus"],
            ],
            "arts"     => [
                ["Concentrix",    "Customer Support Executive","₹2.5 LPA", "On-Campus"],
                ["Sutherland",    "Process Associate",         "₹2.5 LPA", "On-Campus"],
                ["EY",            "Content & Communications",  "₹3.5 LPA", "Off-Campus"],
                ["Cognizant BPS", "Customer Service",          "₹3 LPA",   "On-Campus"],
                ["HCL BPO",       "Support Executive",         "₹2.5 LPA", "On-Campus"],
                ["Times of India","Content Writer / Trainee",  "₹2.5 LPA", "Off-Campus"],
                ["MakeMyTrip",    "Travel Consultant",         "₹3 LPA",   "Off-Campus"],
                ["Thomas Cook",   "Tour Executive",            "₹2.5 LPA", "Off-Campus"],
                ["iEnergizer",    "Customer Service Rep",      "₹2.5 LPA", "On-Campus"],
                ["Mphasis BPO",   "Customer Support",         "₹3 LPA",   "On-Campus"],
            ],
        ];

        if ($stream && isset($defaults[$stream])) {
            foreach ($defaults[$stream] as $c) {
                $reply .= "🔹 <b>{$c[0]}</b>\n   💼 Role: {$c[1]}\n   💰 Package: {$c[2]}\n   📋 Type: {$c[3]}\n\n";
            }
        } else {
            // Show all streams
            foreach ($defaults as $s => $list) {
                $secEmoji = $streamEmojis[$s] ?? '🏢';
                $reply .= "$secEmoji <b>" . ucfirst($s) . " Companies:</b>\n";
                foreach ($list as $c) {
                    $reply .= "🔹 <b>{$c[0]}</b>\n   💼 Role: {$c[1]}\n   💰 Package: {$c[2]}\n   📋 Type: {$c[3]}\n\n";
                }
            }
        }
    }

    // ── Step 6: Footer tip ────────────────────────────────────────────────────
    $reply .= "━━━━━━━━━━━━━━━━━\n";
    $reply .= "💡 <b>Try also:</b> company for BCA · company for BCOM · company for TAMIL\n";
    $reply .= "💡 Or by stream: company for science · company for commerce · company for arts\n";
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
// DEFAULT FALLBACK — Try FAQ fuzzy match first, then show menu
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

$hint = $fallbackCourse
    ? "💡 Your active course is <b>$fallbackCourse</b>. Try:\n" .
      "gd for $fallbackCourse · hr for $fallbackCourse · coding for $fallbackCourse · ats for $fallbackCourse\n\n"
    : "";

sendReply(
    "🤖 <b>I didn't quite catch that.</b>\n\n" . $hint .
    "📌 <b>Try typing:</b>\n" .
    "• hi → Welcome & full menu\n" .
    "• courses → All available courses\n" .
    "• gd / gd for DS → GD Topics\n" .
    "• hr / hr for BCA → HR Questions\n" .
    "• mock / mock for BCOM → Mock Interview\n" .
    "• aptitude → Practice Questions\n" .
    "• technical → Technical Topics\n" .
    "• coding / coding for MCA → Coding Problems\n" .
    "• skills / skills for AI → Skills Needed\n" .
    "• resume → Resume Tips\n" .
    "• ats / ats for BCA → ATS Resume Checker\n" .
    "• company / company for BBA → Hiring Companies\n\n" .
    "🎓 <b>Or type your course:</b> BCA · MCA · CS · AI · DS · BBA · BCOM · ENG · TAMIL · VISCOM"
);
?>