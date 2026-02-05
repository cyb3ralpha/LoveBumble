// ======================
// Demo placeholder data
// In production, fetch from backend via PHP
// ======================

window.onload = () => {
    // Example: Fetch user profile
    fetchProfile();
};

function fetchProfile() {
    // Demo data, replace with PHP API fetch
    const profile = {
        name: "Alex",
        age: 22,
        bio: "Music lover, traveler, foodie. Always ready for an adventure ✈️",
        gender: "Male",
        preference: "Female",
        interests: ["Music", "Travel", "Fitness", "Movies"],
        photo: "assets/images/ui/default-user.png"
    };

    document.getElementById("profileName").innerText = `${profile.name}, ${profile.age}`;
    document.getElementById("profileBio").innerText = profile.bio;
    document.getElementById("profileGender").innerText = profile.gender;
    document.getElementById("profilePreference").innerText = profile.preference;
    document.getElementById("profilePhoto").src = profile.photo;

    const tagsContainer = document.getElementById("profileTags");
    tagsContainer.innerHTML = "";
    profile.interests.forEach(tag => {
        const div = document.createElement("div");
        div.className = "tag";
        div.innerText = tag;
        tagsContainer.appendChild(div);
    });
}

// ======================
// Actions
// ======================

function startChat() {
    // Redirect to one-to-one chat
    window.location.href = "chat.html";
}

function likeProfile() {
    alert("You liked this profile! ❤️");
    // In production, send like to backend
}
