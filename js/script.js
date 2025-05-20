// --- Popover Sound and Auto-Remove ---
window.addEventListener("DOMContentLoaded", function () {
  var popover = document.querySelector(".popover-message");
  if (popover) {
    if (popover.classList.contains("success")) {
      document.getElementById("successSound").play();
    } else if (popover.classList.contains("error")) {
      document.getElementById("errorSound").play();
    }
    setTimeout(function () {
      if (popover) popover.remove();
    }, 4000);
  }
});

// --- Prevent Form Resubmission on Reload ---
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

// --- Unset Game Session on Page Leave (except form submit) ---
let isFormSubmitting = false;
document.addEventListener(
  "submit",
  function () {
    isFormSubmitting = true;
  },
  true
);

window.addEventListener("beforeunload", function (e) {
  if (!isFormSubmitting) {
    navigator.sendBeacon("unset_game.php");
  }
});

// --- Hover Sound for Username, Points, and Reset Buttons ---
[
  "loginname",
  "pointstable",
  "playAgainBtn",
  "nextGameBtn",
  "logoutBtn",
].forEach(function (id) {
  var el = document.getElementById(id);
  if (el) {
    el.addEventListener("mouseenter", function () {
      var hoverSound = document.getElementById("hoverSound");
      hoverSound.pause();
      hoverSound.currentTime = 0;
      hoverSound.play();
    });
  }
});

document.querySelectorAll(".sound-btn").forEach(function (btn) {
  btn.addEventListener("click", function (e) {
    e.preventDefault();
    var sound = document.getElementById("clickSound");
    sound.currentTime = 0;
    if (sound.readyState < 2) sound.load();
    var navigated = false;
    var go = function () {
      if (!navigated) {
        navigated = true;
        sound.removeEventListener("ended", go);
        window.location.href = btn.getAttribute("data-href");
      }
    };
    sound.addEventListener("ended", go, {
      once: true,
    });
    sound.play().catch(go);
    setTimeout(go, 350);
  });
});

document.querySelectorAll(".difficulty-btn").forEach(function (btn) {
  btn.addEventListener("click", function (e) {
    e.preventDefault();
    var sound = document.getElementById("clickSound");
    sound.currentTime = 0;
    sound.play();
    setTimeout(function () {
      window.location.href = btn.getAttribute("data-href");
    }, 180);
  });
});
// Only run confetti if the congratulations message is visible
window.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector(".completion-message")) {
    confetti({
      particleCount: 150,
      spread: 70,
      origin: {
        y: 0.6,
      },
    });
  }
});
