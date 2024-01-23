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
    <div id="content" class="container container-fluid border border-solid">
        <div class="row my-2">
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


        <div class="container" id="loginPanel">
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
                <button id="loginButton" class="col-2 mx-1">Login</button>
                <!-- <button id="exitButton" class="col-2">Exit</button> -->
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
                <button id="resetButton" class="col-2 mx-1">Reset</button>
                <button id="exitButton" class="col-2">Exit</button>
                <!-- <span class="col-2 ms-auto"><a id="forgotPass">Forgot password?</a></span> -->
            </div>
        </div>

    </div>
    
</body>

</html>