// ================= SEND MESSAGE =================
function sendMessage() {
  const input = document.getElementById("userInput");
  const message = input.value.trim();
  const chatMessages = document.getElementById("chatMessages");

  if (message === "") return;

  // Create user message
  const userMsg = document.createElement("div");
  userMsg.classList.add("user-msg");
  userMsg.textContent = message;
  chatMessages.appendChild(userMsg);

  input.value = "";

  // Auto scroll
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// ================= ENTER KEY SUPPORT =================
document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("userInput");

  if (input) {
    input.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        sendMessage();
      }
    });
  }
});

// ================= SHOW SKILL PAGE =================
function showSkills() {
  const chatMessages = document.getElementById("chatMessages");

  chatMessages.innerHTML = `
    <div class="bot-msg">SKILL REQUIREMENTS</div>

    <div class="bot-msg">✔ Communication Skills</div>
    <div class="bot-msg">✔ Technical Knowledge</div>
    <div class="bot-msg">✔ Problem Solving Ability</div>
    <div class="bot-msg">✔ Team Work</div>
    <div class="bot-msg">✔ Time Management</div>

    <div style="margin-top:20px;">
      <button class="option-btn" onclick="goBack()">⬅ Back</button>
    </div>
  `;
}

// ================= BACK TO FRONT PAGE =================
function goBack() {
  const chatMessages = document.getElementById("chatMessages");

  chatMessages.innerHTML = `
    <div class="bot-msg">PLACEMENT TRAINING MODULES:</div>

    <div class="option-container">
      <button class="option-btn">Aptitude Preparation</button>
      <button class="option-btn">Technical Preparation</button>
      <button class="option-btn">Interview Preparation</button>
      <button class="option-btn">Resume Building</button>
      <button class="option-btn">Mock Tests & GD</button>
      <button class="option-btn" onclick="showSkills()">Skill Requirements</button>
    </div>
  `;
}
