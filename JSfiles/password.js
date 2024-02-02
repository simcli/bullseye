function togglePasswordVisibility(event) {
  const revealIcon = event.target;
  const passwordInput = revealIcon.previousElementSibling;

  if (passwordInput.type === "password") {
      passwordInput.type = "text";
      revealIcon.innerHTML = "&#x1F440;"; // Eye icon
  } else {
      passwordInput.type = "password";
      revealIcon.innerHTML = "&#x1F441;"; // Closed eye icon
  }
}

//ajax request reset pass function here
function resetPassword(username, newPassword, confirmPassword) {
  // Validate password
  if (!isValidPassword(newPassword, confirmPassword)) {
    return; // Password is not valid
  }

  // Convert data to JSON format
  let obj = {
    username: username,
    newPassword: newPassword,
  };

  // Set method and URL variables
  let method = "POST";
  let url = "bullseye/resetpass";

  // Perform AJAX request
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let response = JSON.parse(xhr.responseText);
     
      if (xhr.status === 200) {
        // Password reset successfully
        alert("Password reset successful.");
        // Redirect or perform any other necessary action
      } else {
        // Password reset failed
        let errorResponse = JSON.parse(xhr.responseText);
        alert("Password reset failed. " + errorResponse.error);
      }
    }
  };

  // Send JSON data in the request body
  xhr.open(method, url, true);
  xhr.setRequestHeader("Content-type", "application/json");
  xhr.send(JSON.stringify(obj));
}

function isValidPassword(password, confirmPassword) {
  // Password security requirements: minimum 8 characters, at least 1 non-numeric, one capital letter, one number
  const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    return false;
  }

  if (!passwordRegex.test(password)) {
    alert("Password does not meet security requirements.");
    return false;
  }

  return true;
}



export { togglePasswordVisibility, resetPassword, isValidPassword };
