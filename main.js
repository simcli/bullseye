//import user
import { validateLogin, lockAccount } from "./JSfiles/login.js";
import { togglePasswordVisibility, resetPassword } from "./JSfiles/password.js";
import { buildTable } from "./JSfiles/admin.js";

let addOrUpdate; // to track whether we're doing an add or an update
let loginAttempts = 0;
let lastAttemptedUsername = "";

window.onload = function () {
  //Tab Click handlers
  //document.querySelector("#playersOutput").addEventListener("click", displayPlayersClick);

  //BUTTON HANDLERS
  document.querySelector("#loginButton").addEventListener("click", handleLogin);
  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("eye")) {
      togglePasswordVisibility(event);
    }
  });

  document
    .querySelector("#forgotPass")
    .addEventListener("click", showResetPasswordForm);

  document
    .querySelector("#exitButton")
    .addEventListener("click", hideResetPassForm);
  document
    .querySelector("#resetButton")
    .addEventListener("click", handleResetPass);

    document.querySelector("#adminButton").addEventListener("click", showUsersTable)
};

function showUsersTable() {
  //let url = "api/getAllItems.php";
  let url = "bullseye/users";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
          let resp = JSON.parse(xhr.responseText);
          if (resp.data) {
              buildTable(resp.data);
              //Delete and update button should be disabled by default
              //setDeleteUpdateButtonState(false);
          } else {
              alert(resp.error + "; status code: " + xhr.status);
          }
      }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function hideResetPassForm() {
  //document.querySelector("#")
  document.querySelector("#loginPanel").classList.remove("hidden");
  document.querySelector("#resetPassPanel").classList.add("hidden");
}

function showResetPasswordForm() {
  let username = document.querySelector("#username").value;

  if (username.length === 0) {
    alert("Username field must be filled in");
    return;
  }

  // Hide loginPanel, show resetPassPanel
  document.querySelector("#loginPanel").classList.add("hidden");
  document.querySelector("#resetPassPanel").classList.remove("hidden");

  // Set the username in the reset form
  document.querySelector("#grabbedname").innerHTML = username;
}

function handleLogin() {
  let username = document.querySelector("#username").value;
  let password = document.querySelector("#password").value;

  if (username === "" || password === "") {
    alert("Both username and password must be filled out");
    return;
  }

  if (username === lastAttemptedUsername) {
    loginAttempts++;
  } else {
    // Reset the attempts counter if the username changes
    loginAttempts = 1;
    lastAttemptedUsername = username;
  }

  // Check if the login attempts threshold is reached (e.g., 4 attempts)
  const maxLoginAttempts = 4;

  if (loginAttempts >= maxLoginAttempts) {
    // Lock the account
    lockAccount(username);
    alert("Account locked due to multiple failed login attempts.");
    return;
  }

  // Continue with the login validation
  login(username, password);
}

async function login(username, password) {
  try {
    const userData = await validateLogin(username, password);
    
    //if login is successful reveal mainpage
    document.querySelector("#mainPagePanel").classList.remove("hidden");
    document.querySelector("#loginPanel").classList.add("hidden");
    //then change the username and location to the logged in user
    document.querySelector("#displayUsername").innerHTML = userData.FirstName + ", " + userData.LastName;
    document.querySelector("#displayLocation").innerHTML = userData.location;
    changeMenuName("Main Menu");
  } catch (error) {
    console.error("Login failed:", error);
    // Handle login failure
  }
}

function handleResetPass() {
  let username = document.querySelector("#grabbedname").innerHTML;
  let newPassword = document.querySelector("#newpass").value;
  let confirmPassword = document.querySelector("#confirm").value;

  // Check if newPass or confirmPass is empty
  if (newPassword === "" || confirmPassword === "") {
    alert("Both new password and confirm password must be filled out");
    return;
  }

  // Check if newPass and confirmPass match
  if (newPassword !== confirmPassword) {
    alert("New password and confirm password do not match");
    return;
  }

  // Continue with the password reset process
  resetPassword(username, newPassword, confirmPassword);
}


function changeMenuName(name) {
  document.querySelector("#directory").innerHTML = name;
  document.querySelector("#directoryTitle").innerHTML = name;
}