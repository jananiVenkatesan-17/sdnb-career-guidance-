let selectedLanguage = "";

function selectLanguage(lang) {
  selectedLanguage = lang;

  document.getElementById("languageSelect").style.display = "none";
  document.getElementById("chatInput").style.display = "flex";

  const chat = document.getElementById("chatMessages");

  if (lang === "en") {
    chat.innerHTML += "<div>Hello 👋 How can I help you?</div>";
  } else {
    chat.innerHTML += "<div>வணக்கம் 👋 நான் உங்களுக்கு எப்படி உதவலாம்?</div>";
  }
}

function sendMessage() {
  const input = document.getElementById("userInput");
  if (input.value === "") return;

  document.getElementById("chatMessages").innerHTML +=
    "<div>" + input.value + "</div>";

  input.value = "";
}
