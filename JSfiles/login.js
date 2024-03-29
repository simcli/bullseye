//This function validates user creditials on login and calls the backend login.php
//URL bullseye/login

function validateLogin(username, password) {
  return new Promise((resolve, reject) => {
    // Convert data to JSON format
    let obj = {
      username: username,
      password: password,
    };

    // Set method and URL variables
    let method = "POST";
    let url = "bullseye/login";

    // Perform AJAX request
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        let response = JSON.parse(xhr.responseText);

        if (xhr.status === 200) {
          // Successful login
          alert(
            "Login successful! Welcome, " +
              response.data.FirstName +
              " " +
              response.data.LastName +
              " " +
              response.data.permissionLevel
          );
          resolve(response.data); //resolve with user data
        } else {
          // Login failed
          let errorResponse = JSON.parse(xhr.responseText);
          alert("Login failed. " + errorResponse.error);
          reject(errorResponse.error); //reject with the error message
        }
      }
    };

    // Send JSON data in the request body
    xhr.open(method, url, true);
    xhr.send(JSON.stringify(obj));
  });
}

function lockAccount(username) {
  // Convert data to JSON format
  let obj = {
    username: username,
  };

  // Set method and URL variables
  let method = "POST";
  let url = "bullseye/lockaccount";

  // Perform AJAX request
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let response = JSON.parse(xhr.responseText);

      if (xhr.status === 200) {
        // Account locked successfully
        alert("Account locked successfully.");
      } else {
        // Locking account failed
        let errorResponse = JSON.parse(xhr.responseText);
        alert("Locking account failed. " + errorResponse.error);
      }
    }
  };

  // Send JSON data in the request body
  xhr.open(method, url, true);
  xhr.setRequestHeader("Content-type", "application/json");
  xhr.send(JSON.stringify(obj));
}

export { validateLogin, lockAccount };
