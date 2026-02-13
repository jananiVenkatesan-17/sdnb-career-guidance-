let currentStage = "start";

// ================= ON LOAD =================
window.onload = () => {
  addBotMessage(
    "Welcome to SDNBVC PLACIFY – Your Placement Guidance Assistant"
  );
  showGeneralInfo();
};

// ================= UI HELPERS =================
function addBotMessage(text) {
  const chat = document.getElementById("chatMessages");
  const msg = document.createElement("div");
  msg.className = "bot-msg";
  msg.innerText = text;
  chat.appendChild(msg);
  chat.scrollTop = chat.scrollHeight;
}

function addUserMessage(text) {
  const chat = document.getElementById("chatMessages");
  const msg = document.createElement("div");
  msg.className = "user-msg";
  msg.innerText = text;
  chat.appendChild(msg);
  chat.scrollTop = chat.scrollHeight;
}

function addOptions(options, showBack = false) {
  const chat = document.getElementById("chatMessages");
  const container = document.createElement("div");
  container.className = "option-container";

  options.forEach(option => {
    const btn = document.createElement("button");
    btn.className = "option-btn";
    btn.innerText = option;
    btn.onclick = () => handleOptionClick(option);
    container.appendChild(btn);
  });

  if (showBack) {
    const backBtn = document.createElement("button");
    backBtn.className = "option-btn";
    backBtn.innerText = "⬅ Back";
    backBtn.onclick = goBack;
    container.appendChild(backBtn);
  }

  chat.appendChild(container);
  chat.scrollTop = chat.scrollHeight;
}

// ================= GENERAL INFO =================
function showGeneralInfo() {
  currentStage = "general";

  addBotMessage(
    "GENERAL PLACEMENT INFORMATION\n\n" +
    "Placement is the process where companies visit colleges to recruit students for job roles.\n\n" +
    "Placement process:\n" +
       "• Pre-placement talk\n" +
       "• Aptitude test\n" +
       "• Technical interview\n" +
       "• HR interview\n" +
       "• Final job offer\n\n" +
    "Why placement preparation matters:\n" +
       "• Eligibility criteria must be satisfied\n" +
       "• Technical and soft skills are essential\n" +
       "• A good resume improves selection chances\n" +
       "• Communication skills build confidence"
  );

  addOptions(["Proceed to Placement Training"]);
}

// ================= PLACEMENT MENU =================
function showPlacementMenu() {
  currentStage = "placement";

  addBotMessage("PLACEMENT TRAINING MODULES:");
  addOptions(
    [
      "Aptitude Preparation",
      "Technical Preparation",
      "Interview Preparation",
      "Resume Building",
      "Mock Tests & GD",
      "Skill Requirements"
    ],
    true
  );
}

// ================= OPTION HANDLER =================
function handleOptionClick(option) {
  addUserMessage(option);

  if (option === "Proceed to Placement Training") {
    showPlacementMenu();
  }

  else if (option === "Aptitude Preparation") {
    addBotMessage("Practice aptitude questions regularly.");
  }

  else if (option === "Technical Preparation") {
    addBotMessage("Revise programming, DBMS, OOPS, and data structures.");
  }

  else if (option === "Interview Preparation") {
    addBotMessage("Prepare HR and technical interview questions.");
  }

  else if (option === "Resume Building") {
    addBotMessage("Create a professional resume with skills and projects.");
  }

  else if (option === "Mock Tests & GD") {
    addBotMessage("Attend mock tests and group discussions.");
  }

  else if (option === "Skill Requirements") {
    addBotMessage("SKILL REQUIREMENTS:");
    addOptions(
      [
        "Technical Skills",
        "Soft Skills",
        "Professional Skills",
        "Practical Exposure",
        "Continuous Learning"
      ],
      true
    );
  }
}

// ================= BACK BUTTON =================
function goBack() {
  if (currentStage === "placement") {
    showGeneralInfo();
  }
}
