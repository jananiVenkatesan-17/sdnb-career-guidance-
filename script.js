/* ============================================================
   SDNBVC - PLACIFY (Frontend + MySQL API)
   ============================================================ */
console.log("LATEST SCRIPT LOADED");
// ================== API PATHS ==================
const API_GET_COURSES = "../backend/api/get_courses.php";
const API_SKILLS = "../backend/api/get_skills.php";
const API_MATERIALS = "../backend/api/get_study_materials.php";
const API_APTITUDE = "../backend/api/get_aptitude.php";
const API_HR = "../backend/api/get_hr.php";
const API_GD = "../backend/api/get_gd.php";
const API_RESUME_TIPS = "../backend/api/get_resume_tips.php";
const API_MOCK_INTERVIEW = "../backend/api/get_mock_interview.php";
const API_CODING = "../backend/api/get_coding_topics.php";
const API_CHATBOT = "../backend/api/chatbot.php";

// ================== GLOBAL CACHE ==================
let COURSES_DB = [];
let CURRENT_MODULE = "";
 

// ================== HELPERS ==================
function getSelectedCourseId() {
  const params = new URLSearchParams(window.location.search);
  let id = params.get("courseId");

  if (!id) {
    id = localStorage.getItem("selectedCourseId");
  }

  return id ? Number(id) : null;
}

async function loadCoursesFromDB() {
  if (COURSES_DB.length > 0) return COURSES_DB;

  const res = await fetch(API_GET_COURSES);
  const json = await res.json();

  if (!json || json.status !== "success") {
    console.error("Course API error:", json);
    throw new Error("Courses not loading");
  }

  COURSES_DB = json.data || [];
  return COURSES_DB;
}

// ================== INDEX PAGE ==================
function showCourseInfo(selectedId, courses, infoBox) {
  if (!infoBox) return;

  const id = Number(selectedId);
  const course = courses.find(x => Number(x.id) === id);

  if (!course) {
    infoBox.style.display = "none";
    infoBox.textContent = "";
    return;
  }

  infoBox.style.display = "block";
  infoBox.innerHTML =
    `<strong>Selected:</strong> ${course.course_name}<br>` +
    `<strong>Department:</strong> ${course.department} | <strong>Level:</strong> ${course.level}`;
}

async function initIndexPage() {
  const select = document.getElementById("courseSelect");
  const infoBox = document.getElementById("courseInfo");
  if (!select) return;

  try {
    const courses = await loadCoursesFromDB();

    courses.forEach(c => {
      const op = document.createElement("option");
      op.value = c.id;
      op.textContent = `${c.course_name} (${c.course_code})`;
      select.appendChild(op);
    });

    const savedId = localStorage.getItem("selectedCourseId");
    if (savedId) {
      select.value = savedId;
      showCourseInfo(select.value, courses, infoBox);
    }

    select.addEventListener("change", () => {
      showCourseInfo(select.value, courses, infoBox);
    });

  } catch (err) {
    console.error(err);
    alert("Courses not loading. Check XAMPP Apache + MySQL + API.");
  }
}

function goCourse() {
  const select = document.getElementById("courseSelect");
  if (!select) return;

  const id = select.value;
  if (!id) {
    alert("Please select a course.");
    return;
  }

  localStorage.setItem("selectedCourseId", id);
  window.location.href = `placement.html?courseId=${encodeURIComponent(id)}`;
 
    
}

// ================== MODULES ==================
function getSpecificModules(course) {
  const dept = course.department;

  if (dept === "Computer Science" || dept === "Computer Applications") {
    return ["Technical", "Coding Practice"];
  }

  if (dept === "Commerce") {
    return ["Commerce", "Excel & Tally"];
  }

  if (dept === "Management") {
    return ["Business Communication", "Basics of Marketing/HR/Finance"];
  }

  if (dept === "Mathematics" || dept === "Statistics") {
    return ["Advanced Quant + LR", "Basics of Analytics"];
  }

  if (dept === "English" || dept === "Tamil" || dept === "Journalism") {
    return ["Communication & Fluency", "Grammar + Content Skills"];
  }

  return ["General Domain Awareness", "Communication & Soft Skills"];
}

// ================== RENDER MODULES ==================
async function renderPlacementModules() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "";

  try {
    const courses = await loadCoursesFromDB();
    const courseId = getSelectedCourseId();
    const course = courses.find(c => Number(c.id) === Number(courseId));

    if (!course) {
      title.textContent = "Placement Training";
      chat.innerHTML = `
        <div class="bot-msg">Course not selected. Go back and choose a course.</div>
        <div style="margin-top:12px;">
          <button class="option-btn" onclick="window.location.href='course.html'">← Back</button>
        </div>
      `;
      return;
    }

    title.textContent = `${course.course_code} - Placement Training`;

    const specific = getSpecificModules(course);

    chat.innerHTML = `
           <div class="bot-msg"><strong>${course.course_name}</strong></div>
      <div class="bot-msg"><strong>Choose a module:</strong></div>

      <div class="option-container">
        <button class="option-btn" onclick="showAptitudeQuestions()">Aptitude</button>

        ${specific.includes("Technical") ? `<button class="option-btn" onclick="showStudyMaterials('Technical')">Technical</button>` : ""}
        ${specific.includes("Coding Practice") ? `<button class="option-btn" onclick="showCodingTopics()">Coding Practice</button>` : ""}
        ${specific.includes("Commerce") ? `<button class="option-btn" onclick="showStudyMaterials('Commerce')">Commerce</button>` : ""}
        ${specific.includes("Excel & Tally") ? `<button class="option-btn" onclick="showStudyMaterials('Excel & Tally')">Excel & Tally</button>` : ""}
        ${specific.includes("Business Communication") ? `<button class="option-btn" onclick="showStudyMaterials('Business Communication')">Business Communication</button>` : ""}
        ${specific.includes("Basics of Marketing/HR/Finance") ? `<button class="option-btn" onclick="showStudyMaterials('Basics of Marketing/HR/Finance')">Marketing / HR / Finance</button>` : ""}
        ${specific.includes("Advanced Quant + LR") ? `<button class="option-btn" onclick="showStudyMaterials('Advanced Quant + LR')">Advanced Quant + LR</button>` : ""}
        ${specific.includes("Basics of Analytics") ? `<button class="option-btn" onclick="showStudyMaterials('Basics of Analytics')">Basics of Analytics</button>` : ""}
        ${specific.includes("Communication & Fluency") ? `<button class="option-btn" onclick="showStudyMaterials('Communication & Fluency')">Communication & Fluency</button>` : ""}
        ${specific.includes("Grammar + Content Skills") ? `<button class="option-btn" onclick="showStudyMaterials('Grammar + Content Skills')">Grammar + Content Skills</button>` : ""}
        ${specific.includes("General Domain Awareness") ? `<button class="option-btn" onclick="showStudyMaterials('General Domain Awareness')">General Domain Awareness</button>` : ""}
        ${specific.includes("Communication & Soft Skills") ? `<button class="option-btn" onclick="showStudyMaterials('Communication & Soft Skills')">Communication & Soft Skills</button>` : ""}

        <button class="option-btn" onclick="showHRQuestions()">HR</button>
        <button class="option-btn" onclick="showResumeTips()">Resume</button>
        <button class="option-btn" onclick="openATSChecker()">ATS Checker</button>
        <button class="option-btn" onclick="showGDTopics()">GD</button>
        <button class="option-btn" onclick="showSkillsFromDB()">Skill Requirements</button>
        <button class="option-btn" onclick="showMockInterviewQuestions()">Mock Interview</button>
        <button class="option-btn" onclick="window.location.href='course.html'">← Back</button>
      </div>
    `;

  } catch (err) {
    console.error(err);
    chat.innerHTML = `<div class="bot-msg">❌ Error loading modules. Check API.</div>`;
  }
}

// ================== SKILLS ==================
async function showSkillsFromDB() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  const courseId = getSelectedCourseId();
  if (!chat || !title || !courseId) return;

  CURRENT_MODULE = "Skills";
  title.textContent = "Skill Requirements";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading skills...</strong></div>`;

  try {
    const res = await fetch(`${API_SKILLS}?courseId=${encodeURIComponent(courseId)}`);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ Skills not loading. Check get_skills.php</div>`;
      return;
    }

    if (json.data.length === 0) {
      chat.innerHTML = `
        <div class="bot-msg">No skills found for this course.</div>
        <div style="margin-top:12px;">
          <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
        </div>
      `;
      return;
    }

    const groups = {};
    json.data.forEach(s => {
      const type = s.skill_type || "Other";
      if (!groups[type]) groups[type] = [];
      groups[type].push(s.skill_name);
    });

    let html = `<div class="bot-msg"><strong>SKILLS REQUIRED</strong></div>`;

    Object.keys(groups).forEach(type => {
      html += `
        <div class="bot-msg">
          <strong>${type}</strong><br>
          ${groups[type].map(x => "• " + x).join("<br>")}
        </div>
      `;
    });

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (err) {
    console.error(err);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching skills. Check XAMPP.</div>`;
  }
}

// ================== STUDY MATERIALS ==================
async function showStudyMaterials(moduleName = "") {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  const courseId = getSelectedCourseId();
  if (!chat || !title || !courseId) return;

  CURRENT_MODULE = moduleName;
  title.textContent = `Study Materials - ${moduleName}`;
  chat.innerHTML = `<div class="bot-msg"><strong>Loading study materials...</strong></div>`;

  try {
    const url = `${API_MATERIALS}?courseId=${encodeURIComponent(courseId)}&module=${encodeURIComponent(moduleName)}`;
    const res = await fetch(url);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ Study materials not loading. Check get_study_materials.php</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>${moduleName} - Links</strong></div>`;

    if (json.data.length === 0) {
      html += `<div class="bot-msg">No study materials found for this module.</div>`;
    } else {
      json.data.forEach(item => {
        html += `
          <div class="bot-msg">
            <a href="${item.url}" target="_blank">${item.title}</a>
          </div>
        `;
      });
    }

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (err) {
    console.error(err);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching study materials. Check XAMPP.</div>`;
  }
}

// ================== APTITUDE ==================
async function showAptitudeQuestions() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "Aptitude";
  title.textContent = "Aptitude Questions";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading aptitude questions...</strong></div>`;

  try {
    const res = await fetch(API_APTITUDE);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ Aptitude questions not loading.</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>APTITUDE QUESTIONS</strong></div>`;

    json.data.forEach(item => {
      html += `
        <div class="bot-msg">
          <strong>Q:</strong> ${item.question}<br>
          <strong>A:</strong> ${item.answer}
        </div>
      `;
    });

    /* ===== LINKS BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="showStudyMaterials('Aptitude')">links</button>
      </div>
    `;

    /* ===== BACK BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (error) {
    console.error(error);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching aptitude questions.</div>`;
  }
}

// ================== HR ==================
async function showHRQuestions() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "HR";
  title.textContent = "HR Questions";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading HR questions...</strong></div>`;

  try {
    const res = await fetch(API_HR);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ HR questions not loading.</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>HR Questions:</strong></div>`;

    json.data.forEach(item => {
      html += `
        <div class="bot-msg">
          <strong>Q:</strong> ${item.question}<br>
          <strong>Tip:</strong> ${item.tip}
        </div>
      `;
    });

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="showStudyMaterials('HR')">links</button>
      </div>
    `;

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (error) {
    console.error(error);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching HR questions.</div>`;
  }
}

// ================== GD ==================
async function showGDTopics() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "GD";
  title.textContent = "GD Topics";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading GD topics...</strong></div>`;

  try {
    const res = await fetch(API_GD);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ GD topics not loading.</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>GROUP DISCUSSION TOPICS</strong></div>`;

    json.data.forEach(item => {
      html += `<div class="bot-msg">• ${item.topic}</div>`;
    });

    /* ===== ADD LINKS BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="showStudyMaterials('GD')">links</button>
      </div>
    `;

    /* ===== BACK BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (error) {
    console.error(error);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching GD topics.</div>`;
  }
}
// ================== RESUME ==================
async function showResumeTips() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "Resume";
  title.textContent = "Resume Tips";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading resume tips...</strong></div>`;

  try {
    const res = await fetch(API_RESUME_TIPS);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ Resume tips not loading.</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>RESUME TIPS</strong></div>`;

    json.data.forEach(item => {
      html += `<div class="bot-msg">• ${item.tip}</div>`;
    });

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (error) {
    console.error(error);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching resume tips.</div>`;
  }
}

// ================== MOCK INTERVIEW ==================
async function showMockInterviewQuestions() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "Mock Interview";
  title.textContent = "Mock Interview";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading mock interview questions...</strong></div>`;

  try {
    const res = await fetch(API_MOCK_INTERVIEW);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">❌ Mock interview questions not loading.</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>MOCK INTERVIEW QUESTIONS</strong></div>`;

    json.data.forEach(item => {
      html += `<div class="bot-msg">• ${item.question}</div>`;
    });

    /* ===== LINKS BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="showStudyMaterials('Mock Interview')">links</button>
      </div>
    `;

    /* ===== BACK BUTTON ===== */

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (error) {
    console.error(error);
    chat.innerHTML = `<div class="bot-msg">❌ Error fetching mock interview questions.</div>`;
  }
}
// ================== CODING TOPICS ==================
async function showCodingTopics() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  if (!chat || !title) return;

  CURRENT_MODULE = "Coding";
  title.textContent = "Coding Practice";
  chat.innerHTML = `<div class="bot-msg"><strong>Loading coding topics...</strong></div>`;

  try {
    const res = await fetch(API_CODING);
    const json = await res.json();

    if (!json || json.status !== "success") {
      chat.innerHTML = `<div class="bot-msg">Coding topics not loading</div>`;
      return;
    }

    let html = `<div class="bot-msg"><strong>Coding Practice Topics</strong></div>`;

    json.data.forEach(item => {
      html += `
        <div class="bot-msg">
          <strong>${item.topic}</strong><br>
          ${item.description}
        </div>
      `;
    });

    html += `
      <div style="margin-top:12px;">
        <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
      </div>
    `;

    chat.innerHTML = html;

  } catch (err) {
    console.error(err);
    chat.innerHTML = `<div class="bot-msg">Error loading coding topics</div>`;
  }
}

// ================== CHAT INPUT ==================
async function sendMessage() {
  const input = document.getElementById("userInput");
  const chat = document.getElementById("chatMessages");
  if (!input || !chat) return;

  const message = input.value.trim();
  if (!message) return;

  const user = document.createElement("div");
  user.className = "user-msg";
  user.textContent = message;
  chat.appendChild(user);

  input.value = "";
  chat.scrollTop = chat.scrollHeight;

  try {
    const courseId = getSelectedCourseId() || 0;

    const res = await fetch(API_CHATBOT, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `message=${encodeURIComponent(message)}&courseId=${encodeURIComponent(courseId)}&currentModule=${encodeURIComponent(CURRENT_MODULE)}`
    });

    const json = await res.json();

    const bot = document.createElement("div");
    bot.className = "bot-msg";
    bot.innerHTML = (json.reply || "No reply from chatbot.").replace(/\n/g, "<br>");
    chat.appendChild(bot);

    chat.scrollTop = chat.scrollHeight;
  } catch (error) {
    console.error(error);
    const bot = document.createElement("div");
    bot.className = "bot-msg";
    bot.textContent = "Error connecting to chatbot.";
    chat.appendChild(bot);
  }
}

// ================== WINDOW EXPORTS ==================
window.goCourse = goCourse;
window.renderPlacementModules = renderPlacementModules;
window.showSkillsFromDB = showSkillsFromDB;
window.showStudyMaterials = showStudyMaterials;
window.showAptitudeQuestions = showAptitudeQuestions;
window.showHRQuestions = showHRQuestions;
window.showGDTopics = showGDTopics;
window.showResumeTips = showResumeTips;
window.showMockInterviewQuestions = showMockInterviewQuestions;
window.showCodingTopics = showCodingTopics;
window.sendMessage = sendMessage;
window.openATSChecker = openATSChecker;
window.checkATSScore = checkATSScore;

// ================== AUTO INIT ==================
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("courseSelect")) {
    initIndexPage();
  }

  if (document.getElementById("chatMessages") && document.getElementById("deptTitle")) {
    renderPlacementModules();
  }

  const input = document.getElementById("userInput");
  if (input) {
    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
      }
    });
  }
});
function openATSChecker() {
  const chat = document.getElementById("chatMessages");
  const title = document.getElementById("deptTitle");
  const courseId = getSelectedCourseId();

  if (!chat || !title || !courseId) return;

  CURRENT_MODULE = "ATS";
  title.textContent = "ATS Checker";

  chat.innerHTML = `
    <div class="bot-msg"><strong>ATS Resume Checker</strong><br>Paste your resume text below.</div>

    <div class="bot-msg">
      <textarea id="resumeText" placeholder="Paste your resume here..." style="width:100%; height:140px; padding:10px; border:1px solid #ccc; border-radius:8px;"></textarea>
    </div>

    <div class="option-container">
      <button class="option-btn" onclick="checkATSScore()">Check ATS Score</button>
      <button class="option-btn" onclick="renderPlacementModules()">← Back</button>
    </div>

    <div id="atsResult"></div>
  `;
}

async function checkATSScore() {
  const resumeText = document.getElementById("resumeText");
  const resultBox = document.getElementById("atsResult");
  const courseId = getSelectedCourseId();

  if (!resumeText || !resultBox || !courseId) return;

  const resume = resumeText.value.trim();

  if (!resume) {
    resultBox.innerHTML = `<div class="bot-msg">Please paste your resume text.</div>`;
    return;
  }

  resultBox.innerHTML = `<div class="bot-msg">Checking ATS score...</div>`;

  try {
    const res = await fetch("../backend/api/ats_checker.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `resume=${encodeURIComponent(resume)}&courseId=${encodeURIComponent(courseId)}`
    });

    const json = await res.json();

    if (!json || json.status !== "success") {
      resultBox.innerHTML = `<div class="bot-msg">ATS check failed.</div>`;
      return;
    }

    resultBox.innerHTML = `
      <div class="bot-msg">
        <strong>ATS Score:</strong> ${json.score}%<br><br>
        <strong>Matched Keywords:</strong><br>
        ${json.matched.length ? json.matched.map(k => "• " + k).join("<br>") : "No matched keywords"}<br><br>
        <strong>Missing Keywords:</strong><br>
        ${json.missing.length ? json.missing.map(k => "• " + k).join("<br>") : "No missing keywords"}
      </div>
    `;
  } catch (error) {
    console.error(error);
    resultBox.innerHTML = `<div class="bot-msg">Error checking ATS score.</div>`;
  }
}
async function uploadResume(){

const file=document.getElementById("resumeFile").files[0];

if(!file){
alert("Upload resume file");
return;
}

const formData=new FormData();
formData.append("resume",file);

const res=await fetch("../backend/api/upload_resume.php",{
method:"POST",
body:formData
});

const json=await res.json();

document.getElementById("atsResult").innerHTML=`
<div class="bot-msg">
ATS Score : <b>${json.score}%</b><br><br>
Matched Skills:<br>
${json.matched.join(", ")}
</div>
`;

}
