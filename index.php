<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bullseye</title>

    <link rel="stylesheet" href="mainStyleSheet.css">

    <!-- <script type="module" src="JSfiles/login.js"></script> -->
    <script type="module" src="main.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JavaScript (Optional, for certain components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div id="content" class="container w-100 border border-solid">
        <div class="row">
            <div class="border border-solid">
                Bullseye Inventory Management System - <span id="directory">Login</span>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-2 float-left">
                <img src="images/bullseyeLogo.png" class="img-fluid rounded-top" alt="" />
            </div>
            <div class="col-8 text-center" id="directoryTitle">
                Login
            </div>
            <picture class="col-1 ms-auto">
                <img src="images/question-mark-is-small.png" class="img-fluid" id="help" alt="" />
            </picture>
        </div>


        <div class="hidden container" id="loginPanel">
            <div class="row col-9 my-5">
                <div class="col-2">
                    <label for="username"><b>Username:</b></label>
                </div>

                <div class="col-6">
                    <input type="text" id="username" placeholder="Enter Username" required>
                </div>

            </div>
            <div class="row col-9 my-5">
                <div class="col-2">
                    <label for="password"><b>Password:</b></label>
                </div>

                <div class="col-6">
                    <input type="password" id="password" placeholder="Enter Password" required>
                    <span class="eye" id="revealIcon">&#x1F441;</span>

                </div>
            </div>

            <div class="row my-2">
                <button id="loginButton" class="col-2 mx-3">Login</button>
                <div class="col-2 ms-auto" id="forgotPass">Forgot password?</div>
            </div>
        </div>

        <div class="container hidden" id="resetPassPanel">
            <div class="row col-9 my-5">
                <div class="col-2">
                    <label for="username"><b>Username:</b></label>
                </div>

                <div class="col-6">
                    <div id="grabbedname"></div>
                </div>

            </div>
            <div class="row col-9 my-5">
                <div class="col-2">
                    <label for="newpass"><b>New Password:</b></label>
                </div>

                <div class="col-6">
                    <input type="password" id="newpass" placeholder="New Password" required>
                    <span class="eye" id="revealIcon">&#x1F441;</span>

                </div>
            </div>
            <div class="row col-9 my-5">
                <div class="col-2">
                    <label for="confirm"><b>Confirm:</b></label>
                </div>

                <div class="col-6">
                    <input type="password" id="confirm" placeholder="Confirm Password" required>
                    <span class="eye" id="revealIcon">&#x1F441;</span>

                </div>
            </div>

            <div class="row my-2">
                <button id="resetButton" class="col-2 mx-3">Reset</button>
                <button id="passExitButton" class="col-2">Exit</button>
                <!-- <span class="col-2 ms-auto"><a id="forgotPass">Forgot password?</a></span> -->
            </div>
        </div>


        <div id="mainPagePanel" class="">

            <div class="row my-2">

                <div class="col-3">
                    User: <span id="displayUsername"></span>
                </div>
                <div class="col-3 ms-auto">
                    Location: <span id="displayLocation"></span>
                </div>
            </div>

            <div id="mainPage">
                <div class="menu row">
                    <button class="btn-primary col-2 " id="ordersButton">
                        Orders
                    </button>
                    <button class="btn-primary col-2 " id="inventoryButton">
                        Inventory
                    </button>
                    <button class="btn-primary col-2 " id="lossreturnButton">
                        Loss/Return
                    </button>
                    <button class="btn-primary col-2 " id="reportButton">
                        Reports
                    </button>
                    <button class="btn-primary col-2 " id="adminButton">
                        Admin
                    </button>

                </div>
                <div class="border border-solid row" id="datagrid">
                    <picture>
                        <img src="images/datagridimage.png" class="img-fluid" style="height: 500px; width: 1000px"
                            alt="" />
                    </picture>
                </div>
                <div class="row">
                    <button class="btn-primary mx-3 col-1 " id="refreshButton">
                        Refresh
                    </button>
                    <!-- change the exit Button ids -->
                    <button class="btn-primary mx-3 col-1 ms-auto" id="exitResetPassButton">
                        Exit
                    </button>
                </div>
            </div>
            <div class="hidden" id="buttonPanel">
                <button class='btn btn-primary ms-auto' id='addButton'>Add Employee</button>
                <button class='btn btn-primary ms-auto' id='setPermsButton'>Set Employee Permissions</button>
                <button class='btn btn-primary ms-auto' id='exitMainButton'>Exit Page</button>

            </div>

            <!-- when making changes try to always change this output field-->

            <div id="mainOutput">
                <!-- to be filled by JS -->
            </div>


        </div>


        <div id="AddUpdatePanel" class="hidden">
            <div class="inputContainer">
                <div class="inputLabel">Employee ID:</div>
                <div class="inputField">
                    <input id="addEmployeeID" name="addEmployeeID" type="number" min="1" max="9999" disabled />
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Username:</div>
                <div class="inputField">
                    <input id="addUsername" name="addUsername" />
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Password:</div>
                <div class="inputField">
                    <input type="password" id="addPassword" name="addPassword" placeholder="Enter Password">
                    <span class="eye" id="revealIcon">&#x1F441;</span>
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Confirm Password:</div>
                <div class="inputField">
                    <input type="password" id="addConfirmPass" name="addConfirmPass" placeholder="Enter Password">
                    <span class="eye" id="revealIcon">&#x1F441;</span>
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">First Name:</div>
                <div class="inputField">
                    <input id="addFirstname" name="addFirstname" />
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Last Name:</div>
                <div class="inputField">
                    <input id="addLastname" name="addLastname" />
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Email:</div>
                <div class="inputField">
                    <input id="addEmail" name="addEmail" />
                </div>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Active:</div>
                <div class="inputField">
                    <input id="addActive" type="checkbox" name="addActive" />
                </div>
            </div>
            <div class="inputContainer">
                <label for="addPosition">Position:</label>
                <select id="addPosition" name="addPosition">
                    <option>Regional Manager</option>
                    <option>Financial Manager</option>
                    <option>Store Manager</option>
                    <option>Warehouse Manager</option>
                    <option>Trucking / Delivery</option>
                    <option>Warehouse Employee</option>
                    <option>Administrator</option>

                </select>
            </div>

            <div class="inputContainer">
                <label for="addLoction">Location:</label>
                <select id="addLocation" name="addLocation">
                    <option>Truck</option>
                    <option>Warehouse</option>
                    <option>Bullseye Corporate Headquarters</option>
                    <option>Saint John Retail</option>
                    <option>Sussex Retail</option>
                    <option>Moncton Retail</option>
                    <option>Dieppe Retail</option>
                    <option>Oromocto Retail</option>
                    <option>Fredericton Retail</option>
                    <option>Miramichi Retail</option>
                </select>
            </div>
            <div class="inputContainer">
                <div class="inputLabel">Locked:</div>
                <div class="inputField">
                    <input id="addLocked" type="checkbox" name="addLocked" />
                </div>
            </div>

            <div class="inputContainer">
                <div class="inputLabel">&nbsp;</div>
                <div class="inputField">
                    <button id="saveButton" class="btn-primary">
                        Save
                    </button>
                    <button id="addUpdateExitButton" class="btn-primary">
                        Exit
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>