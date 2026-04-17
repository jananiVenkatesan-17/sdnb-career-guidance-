console.log("✅ Placify script.js loaded");

const API_URL = "http://localhost:8080/placify/backend/api/chatbot.php";

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
            body: JSON.stringify({ message: userText })
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
        console.error("❌ Fetch Error:", err.message);
        addMessageToChat("❌ Error: " + err.message, "bot");
    }
}

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