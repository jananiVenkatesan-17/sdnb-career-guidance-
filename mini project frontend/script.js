let selectedLanguage = "";

// Language selection
function selectLanguage(lang) {
  selectedLanguage = lang;

  document.getElementById("languageSelect").style.display = "none";
  document.getElementById("chatInput").style.display = "flex";

  const chat = document.getElementById("chatMessages");

  if (lang === "en") {
    addBotMessage("Hello 👋 I’m your SDNB Career Guidance Assistant. How can I help you?");
  } else {
    addBotMessage("வணக்கம் 👋 நான் SDNB தொழில் வழிகாட்டி உதவியாளர். உங்களுக்கு எப்படி உதவலாம்?");
  }
}

// Send message
function sendMessage() {
  const input = document.getElementById("userInput");
  const text = input.value.trim();
  if (text === "") return;

  addUserMessage(text);
  input.value = "";

  setTimeout(() => {
    botReply(text);
  }, 600);
}

// Add user message (RIGHT)
function addUserMessage(text) {
  const chat = document.getElementById("chatMessages");
  const msg = document.createElement("div");
  msg.className = "user-msg";
  msg.innerText = text;
  chat.appendChild(msg);
  chat.scrollTop = chat.scrollHeight;
}

// Add bot message (LEFT)
function addBotMessage(text) {
  const chat = document.getElementById("chatMessages");
  const msg = document.createElement("div");
  msg.className = "bot-msg";
  msg.innerText = text;
  chat.appendChild(msg);
  chat.scrollTop = chat.scrollHeight;
}

// Simple bot replies (demo logic)
function botReply(userText) {
  if (selectedLanguage === "en") {
    if (userText.toLowerCase().includes("career")) {
      addBotMessage("I can help you explore career options based on your interests.");
    } else if (userText.toLowerCase().includes("job")) {
      addBotMessage("We will guide you about job roles, skills, and placements.");
    } else {
      addBotMessage("Please tell me your interest or ask about careers.");
    }
  } else {
    if (userText.includes("தொழில்")) {
      addBotMessage("உங்கள் ஆர்வத்திற்கு ஏற்ற தொழில் வாய்ப்புகளை நான் பரிந்துரைக்க முடியும்.");
    } else {
      addBotMessage("தயவுசெய்து உங்கள் ஆர்வத்தை கூறுங்கள்.");
    }
  }
}
