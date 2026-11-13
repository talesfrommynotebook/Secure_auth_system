// Inject the clean PHP values from the controller into JavaScript

function display_timeout(absoluteTimeRemaining, idleTimeRemaining) {
  function formatTime(seconds) {
    let minutes = Math.floor(seconds / 60);
    let secs = seconds % 60;
    return `${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
  }

  let timerInterval = setInterval(function () {
    absoluteTimeRemaining--;
    idleTimeRemaining--;

    document.getElementById("hard-timer").innerText = formatTime(
      Math.max(0, absoluteTimeRemaining),
    );
    document.getElementById("idle-timer").innerText = formatTime(
      Math.max(0, idleTimeRemaining),
    );

    if (absoluteTimeRemaining <= 0) {
      clearInterval(timerInterval);
      alert("Your 30-minute session has ended.");
      window.location.href = "../login.php";
    }

    if (idleTimeRemaining <= 0) {
      clearInterval(timerInterval);
      alert("You have been logged out due to inactivity.");
      window.location.href = "../login.php";
    }
  }, 1000);

  function resetIdleTimer() {
    idleTimeRemaining = 600; // Reset back to 10 minutes on user activity
  }

  window.addEventListener("mousemove", resetIdleTimer);
  window.addEventListener("keypress", resetIdleTimer);
  window.addEventListener("click", resetIdleTimer);
  window.addEventListener("scroll", resetIdleTimer);
}
