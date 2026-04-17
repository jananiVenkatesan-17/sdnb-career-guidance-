console.log("✅ Placify script.js loaded");

// 🔥 FIX 1: REMOVE localhost (use relative path)
const API_URL = "../backend/api/chatbot.php";

let selectedLanguage = "English";

function addMessageToChat(message, sender) {
    const chatBox = document.getElementById("chatMessages");
    const div = document.createElement("div");
    div.classList.add(sender === "bot" ? "bot-msg" : "user-msg");
    div.innerHTML = message.replace(/\n/g, "<br>");
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

async function sendMessage() {
    const inputField = document.getElementById("userInput");
    const userText = (inputField?.value || "").trim();
    if (userText === "") return;

    addMessageToChat(userText, "user");
    inputField.value = "";

    console.log("📤 Sending:", userText);

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({ 
                message: userText,
                language: selectedLanguage   // 🔥 FIX 2: include language like example
            })
        });

        console.log("📥 Status:", response.status);

        const text = await response.text();
        console.log("📥 Raw Response:", text);

        let data;

        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("❌ JSON Parse Error:", text);
            addMessageToChat("❌ Server returned invalid response.", "bot");
            return;
        }

        addMessageToChat(data.reply || "No reply from server.", "bot");

    } catch (err) {
        console.error("❌ Fetch Error:", err);
        addMessageToChat("❌ Fetch failed. Check server/API.", "bot"); // 🔥 improved message
    }
}

// 🔹 Your same event listeners (UNCHANGED)
document.getElementById("sendBtn").addEventListener("click", sendMessage);

document.getElementById("userInput").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
    }
});

document.getElementById("uploadBtn").addEventListener("click", function () {
    document.getElementById("resumeFile").click();
});
