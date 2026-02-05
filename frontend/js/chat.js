let socket;
let connected = false;

window.onload = () => {
    startSearch();
};

function startSearch() {
    socket = new WebSocket("ws://localhost:5000/random-chat"); // Python WebSocket
    socket.onopen = () => console.log("Connected to random chat server");
    socket.onmessage = (event) => handleMessage(JSON.parse(event.data));
}

function handleMessage(data) {
    if (data.type === "matched") {
        connected = true;
        document.getElementById("status").classList.add("hidden");
        document.getElementById("chatCard").classList.remove("hidden");
    }

    if (data.type === "message") {
        addMessage(data.user, data.message);
    }

    if (data.type === "disconnect") {
        alert("User disconnected. Finding new match...");
        nextChat();
    }
}

function stopSearch() {
    socket.close();
    window.location.href = "dashboard.html";
}

function sendMessage() {
    const input = document.getElementById("messageInput");
    const msg = input.value.trim();
    if (!msg || !connected) return;

    socket.send(JSON.stringify({ type: "message", message: msg }));
    addMessage("You", msg);
    input.value = "";
}

function addMessage(user, msg) {
    const messages = document.getElementById("messages");
    const div = document.createElement("div");
    div.className = "message";
    div.innerHTML = `<strong>${user}:</strong> <p>${msg}</p>`;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

function nextChat() {
    document.getElementById("chatCard").classList.add("hidden");
    document.getElementById("status").classList.remove("hidden");
    startSearch();
}
