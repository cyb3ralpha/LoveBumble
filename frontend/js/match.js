let matchTimer;

window.onload = () => {
    startMatching();
};

function startMatching() {
    matchTimer = setTimeout(() => {
        document.getElementById("statusCard").classList.add("hidden");
        document.getElementById("matchCard").classList.remove("hidden");

        // Demo data (will come from backend)
        document.getElementById("matchName").innerText = "Alex, 22";
        document.getElementById("matchBio").innerText = "Loves music & travel ✈️";
    }, 3000);
}

function stopMatching() {
    clearTimeout(matchTimer);
    window.location.href = "dashboard.html";
}

function findNext() {
    document.getElementById("matchCard").classList.add("hidden");
    document.getElementById("statusCard").classList.remove("hidden");
    startMatching();
}

function goToChat() {
    window.location.href = "chat.html";
}
