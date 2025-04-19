const OPENAI_API_KEY = "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"; // Replace with your OpenAI API key
const OPENAI_API_URL = "https://api.openai.com/v1/chat/completions";

// Fetch AI response
async function getAIResponse(userMessage) {
    try {
        const response = await fetch(OPENAI_API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${OPENAI_API_KEY}`
            },
            body: JSON.stringify({
                model: "gpt-4",
                messages: [{ role: "user", content: userMessage }],
                max_tokens: 150, // Limit response length
                temperature: 0.7, // Control creativity
            }),
        });

        if (!response.ok) {
            throw new Error(`API request failed with status ${response.status}`);
        }

        const data = await response.json();
        return data.choices?.[0]?.message?.content || "I couldn't process that. Can you rephrase?";
    } catch (error) {
        console.error("Error fetching AI response:", error);
        return "Sorry, I'm having trouble connecting to the server. Please try again later.";
    }
}

// Send message and save conversation
async function sendMessage() {
    let inputField = document.getElementById("user-input");
    let message = inputField.value.trim();
    if (message === "") return;

    let chatBox = document.getElementById("chat-box");
    let userMessage = document.createElement("div");
    userMessage.className = "chat-message user-message";
    userMessage.textContent = message;
    chatBox.appendChild(userMessage);
    chatBox.scrollTop = chatBox.scrollHeight;

    inputField.value = "";

    let loadingDiv = document.createElement("div");
    loadingDiv.className = "loading-indicator";
    loadingDiv.innerHTML = `<div></div><div></div><div></div>`;
    chatBox.appendChild(loadingDiv);

    const botResponse = await getAIResponse(message);
    chatBox.removeChild(loadingDiv);

    let botMessage = document.createElement("div");
    botMessage.className = "chat-message bot-message";
    botMessage.textContent = botResponse;
    chatBox.appendChild(botMessage);
    chatBox.scrollTop = chatBox.scrollHeight;

    // Save conversation to database
    await fetch('chatbot.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_message: message,
            bot_response: botResponse,
        }),
    });
}

// Retrieve conversation history
async function loadConversationHistory() {
    const response = await fetch('chatbot.php?action=get_history');
    const conversations = await response.json();

    let chatBox = document.getElementById("chat-box");
    conversations.forEach(conversation => {
        // Display user message
        let userMessage = document.createElement("div");
        userMessage.className = "chat-message user-message";
        userMessage.textContent = conversation.user_message;
        chatBox.appendChild(userMessage);

        // Display bot response
        let botMessage = document.createElement("div");
        botMessage.className = "chat-message bot-message";
        botMessage.textContent = conversation.bot_response;
        chatBox.appendChild(botMessage);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Handle Enter key press
document.getElementById("user-input").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        sendMessage();
    }
});

// Load conversation history when the page loads
document.addEventListener('DOMContentLoaded', loadConversationHistory);

// Emoji picker functions
function toggleEmojiPicker() {
    let picker = document.getElementById("emoji-picker");
    picker.style.display = picker.style.display === "none" ? "block" : "none";
}

function addEmoji(emoji) {
    document.getElementById("user-input").value += emoji;
    toggleEmojiPicker();
}

// Image preview function
function previewImage(event) {
    let file = event.target.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let chatBox = document.getElementById("chat-box");
            let userImage = document.createElement("div");
            userImage.className = "chat-message user-message";
            userImage.innerHTML = `<img src="${e.target.result}" width="150">`;
            chatBox.appendChild(userImage);
            chatBox.scrollTop = chatBox.scrollHeight;
        };
        reader.readAsDataURL(file);
    }
}