//import user
import { validateLogin, lockAccount } from "./JSfiles/login.js";
import {
  togglePasswordVisibility,
  resetPassword,
  isValidPassword,
} from "./JSfiles/password.js";
//import { buildTable } from "./JSfiles/admin.js";

let arr = [];
let addOrUpdate; // to track whether we're doing an add or an update
let loginAttempts = 0;
let lastAttemptedUsername = "";

window.onload = function () {
  document.querySelector("#exitMainButton").addEventListener("click", exitMain);
  document
    .querySelector("#addUpdateExitButton")
    .addEventListener("click", hideAddUpdatePanel);
  document
    .querySelector("#passExitButton")
    .addEventListener("click", exitResetPass);
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
    .querySelector("#exitResetPassButton")
    .addEventListener("click", hideResetPassForm);
  document
    .querySelector("#resetButton")
    .addEventListener("click", handleResetPass);

  //the update buttons event listener is created within build table
  document
    .querySelector("#adminButton")
    .addEventListener("click", getAllEmployees);

  //should maybe look for any exitButton
  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("exitButton")) {
      exitMain();
    }
  });

  document.querySelector("#saveButton").addEventListener("click", processForm);
  document.querySelector("#addButton").addEventListener("click", addEmployee);
};

//ADD or UPDATE an employee
//Called when Save is pressed
function processForm() {
  let id = Number(document.querySelector("#addEmployeeID").value);
  let password = document.querySelector("#addPassword").value;
  let firstname = document.querySelector("#addFirstname").value;
  let lastname = document.querySelector("#addLastname").value;
  let email = document.querySelector("#addEmail").value;
  let active = document.querySelector("#addActive").checked ? 1 : 0;
  let position = document.querySelector("#addPosition").value;
  let location = document.querySelector("#addLocation").value;
  let locked = document.querySelector("#addLocked").checked ? 1 : 0;

  let confirmPassword = document.querySelector("#addConfirmPass").value;

  let emptyFields = [];

  // Check if any required field is empty
  if (password === "") {
    emptyFields.push("Password");
  }
  if (firstname === "") {
    emptyFields.push("First Name");
  }
  if (lastname === "") {
    emptyFields.push("Last Name");
  }
  if (email === "") {
    emptyFields.push("Email");
  }

  if (emptyFields.length > 0) {
    // Display alert for the empty fields
    alert(`Please fill in the following fields: ${emptyFields.join(", ")}.`);
    return;
  }

  //username might not be handled correctly it is possible to have two identical usernames in the db
  //if the user manually changes their username
  let username = document.querySelector("#addUsername").value;
  if (username === "") {
    username = findUsername();
    
  }

  if (!isValidPassword(password, confirmPassword)) {
    return; // Password is not valid
  }

  let obj = {
    employeeID: id,
    username: username,
    password: password,
    firstName: firstname,
    lastName: lastname,
    email: email,
    active: active,
    permissionLevel: position,
    siteName: location,
    locked: locked,
  };

  let url = "bullseye/employees/" + id;
  let method = addOrUpdate === "add" ? "POST" : "PUT";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        alert(
          "Employee " + resp.data + " was " + addOrUpdate === "add"
            ? "added"
            : "updated"
        );
        hideAddUpdatePanel();
        getAllEmployees();
      } else {
        alert(resp.error + "; status code: " + xhr.status);
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send(JSON.stringify(obj));
}

//use a placeholder for the password field
function buildTable(text) {
  arr = JSON.parse(text); // get JS Objects

  let html = "";
  html +=
    "<table id='employeeTable'><tr><th>Employee ID</th><th>Username</th><th>Password</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Active</th><th>Position</th><th>Site</th><th></th></tr>";
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html += "<tr>";
    html += "<td>" + row.employeeID + "</td>";
    html += "<td>" + row.username + "</td>";
    //need placceholder for password
    html += "<td>*******</td>";
    html += "<td>" + row.firstName + "</td>";
    html += "<td>" + row.lastName + "</td>";
    html += "<td>" + row.email + "</td>";
    html += "<td>" + row.active + "</td>";
    html += "<td>" + row.permissionLevel + "</td>";
    html += "<td>" + row.siteName + "</td>";

    html += "<td>";
    html +=
      "<button class='actionBtn' data-action='update' data-employee-id='" +
      row.employeeID +
      "'>Update</button>";
    html +=
      "<button class='actionBtn' data-action='delete' data-employee-id='" +
      row.employeeID +
      "'>Delete</button>";
    html += "</td>";

    html += "</tr>";
  }
  html += "</table>";

  let theTable = document.querySelector("#mainOutput");
  theTable.innerHTML = html;

  document.querySelector("#mainPage").classList.add("hidden");
  //add an event listener to the table
  theTable.addEventListener("click", handleButtonClick);
}

function findEmployee(id) {
  return arr.find((employee) => employee.employeeID == id);
}
//This could be moved to main
function handleButtonClick(event) {
  // Check if the clicked element is a button with the class 'actionBtn'
  if (event.target.classList.contains("actionBtn")) {
    // Get the action (update or delete) and employee ID from the clicked button
    let action = event.target.dataset.action;
    let employeeId = event.target.dataset.employeeId;

    let selectedEmployee = findEmployee(employeeId);

    // Call the appropriate function based on the action
    if (action === "update") {
      hideButtonPanel();
      //show update panel needs to be sent all the selected employees info
      addOrUpdate = "update";
      populateUpdatePanel(selectedEmployee);
      showUpdatePanel();
    } else if (action === "delete") {
      deleteEmployee(employeeId);
    }
  }
}

function populateUpdatePanel(employee) {
  let employeeID = document.querySelector("#addEmployeeID");
  let username = document.querySelector("#addUsername");
  let firstname = document.querySelector("#addFirstname");
  let lastname = document.querySelector("#addLastname");
  let email = document.querySelector("#addEmail");
  let active = document.querySelector("#addActive");

  employeeID.value = employee.employeeID;
  username.value = employee.username;
  firstname.value = employee.firstName;
  lastname.value = employee.lastName;
  email.value = employee.email;
  active.checked = employee.active === 1;
  const addPositionElement = document.querySelector("#addPosition");
  const positionOptions = addPositionElement.options;

  //select the permission level
  for (let i = 0; i < positionOptions.length; i++) {
    if (positionOptions[i].textContent === employee.permissionLevel) {
      positionOptions[i].selected = true;

      break;
    }
  }

  // select the location
  const addLocationElement = document.querySelector("#addLocation");
  const locationOptions = addLocationElement.options;

  for (let i = 0; i < locationOptions.length; i++) {
    if (locationOptions[i].textContent === employee.siteName) {
      locationOptions[i].selected = true;

      break;
    }
  }
}

function deleteEmployee(id) {
  //let url = "api/getAllItems.php";
  let url = "bullseye/employees/" + id;
  let method = "DELETE";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        alert("Employee Set as Inactive");
        getAllEmployees();
      } else {
        alert(resp.error + "; status code: " + xhr.status);
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function getAllEmployees() {
  //let url = "api/getAllItems.php";
  let url = "bullseye/employees";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        buildTable(resp.data);
        changeMenuName("Admin");
        document.querySelector("#buttonPanel").classList.remove("hidden");
        document.querySelector("#mainOutput").classList.remove("hidden");
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

  // Check if the login attempts threshold is reached
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
    document.querySelector("#displayUsername").innerHTML =
      userData.FirstName + ", " + userData.LastName;
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

function hideResetPassForm() {
  //document.querySelector("#")
  document.querySelector("#loginPanel").classList.remove("hidden");
  document.querySelector("#resetPassPanel").classList.add("hidden");
}

function exitMain() {
  document.querySelector("#mainOutput").classList.add("hidden");
  document.querySelector("#mainPage").classList.remove("hidden");
  document.querySelector("#AddUpdatePanel").classList.add("hidden");
  document.querySelector("#buttonPanel").classList.add("hidden");
}

function addEmployee() {
  addOrUpdate = "add";
  showUpdatePanel();
  document.querySelector("#addEmployeeID").value = findNextEmployeeID();
  hideButtonPanel();
}

function showUpdatePanel() {
  document.querySelector("#AddUpdatePanel").classList.remove("hidden");
  document.querySelector("#mainOutput").classList.add("hidden");
}

//when adding an employee default username is set here

function findUsername() {
  let fname = document.querySelector("#addFirstname").value;
  let Lname = document.querySelector("#addLastname").value;

  let defaultUsername = fname[0] + Lname;

  console.log(defaultUsername);

  let index = 0;
  let newUsername = defaultUsername;

  while (arr.some((employee) => employee.username === newUsername)) {
    index++;
    newUsername = defaultUsername + index.toString().padStart(2, "0");
  }

  return newUsername;
}

//when adding an employee the ID is set automatically here
function findNextEmployeeID() {
  let highestID = 0;

  // Loop through the array to find the highest ID
  for (let i = 0; i < arr.length; i++) {
    if (arr[i].employeeID > highestID) {
      highestID = arr[i].employeeID;
    }
  }

  //Add 1
  return highestID + 1;
}

function exitResetPass() {
  document.querySelector("#resetPassPanel").classList.add("hidden");
  document.querySelector("#loginPanel").classList.remove("hidden");
}

function hideAddUpdatePanel() {
  showButtonPanel();
  document.querySelector("#AddUpdatePanel").classList.add("hidden");
  document.querySelector("#mainOutput").classList.remove("hidden");

  document.querySelector("#addEmployeeID").value = "";
  document.querySelector("#addUsername").value = "";
  document.querySelector("#addFirstname").value = "";
  document.querySelector("#addLastname").value = "";
  document.querySelector("#addEmail").value = "";
  document.querySelector("#addActive").checked = false;
  document.querySelector("#addLocked").checked = false;
}

function hideButtonPanel() {
  document.querySelector("#buttonPanel").classList.add("hidden");
}

function showButtonPanel() {
  document.querySelector("#buttonPanel").classList.remove("hidden");
}
