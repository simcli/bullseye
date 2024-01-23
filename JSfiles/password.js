function togglePasswordVisibility() {
  console.log("success");
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



export { togglePasswordVisibility };
