function togglePasswordVisibility() {
  const passwordInput = document.querySelector("#password");
  const revealIcon = document.querySelector("#revealIcon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    revealIcon.innerHTML = "&#x1F440;"; // Eye icon
  } else {
    passwordInput.type = "password";
    revealIcon.innerHTML = "&#x1F441;"; // Closed eye icon
  }
}

function showResetPasswordForm(username) {
  // Hide loginPanel, show resetPassPanel
  document.querySelector("#loginPanel").classList
  document.getElementById("resetPassPanel").style.display = "block";

  // Set the username in the reset form
  document.getElementById("resetUsername").value = username;
}

export { togglePasswordVisibility };
