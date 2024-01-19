<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bullseye</title>

    <link rel="stylesheet" href="mainStyleSheet.css">
    
    <script type="module" src="main.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JavaScript (Optional, for certain components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <div id="content" class="container container-fluid border border-solid">
        <div class="row my-2">
            <div class="border border-solid">
                Bullseye Inventory Management System - <span id="directory">Dashboard</span>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-2 float-left">
                <img src="images/bullseyeLogo.png" class="img-fluid rounded-top" alt="" />
            </div>
            <div class="col-3">
                User: <span id="username">user</span>
            </div>
            <div class="col-3 ms-auto">
                Location: <span id="location">location</span>
            </div>
        </div>

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
            <picture class="col-1 ms-auto">
                <img src="images/question-mark-is-small.png" class="img-fluid" id="help" alt="" />
            </picture>
        </div>
        <div class="border border-solid row" id="datagrid">
            <picture>
                <img src="images/datagridimage.png" class="img-fluid" style="height: 500px; width: 1000px" alt="" />
            </picture>
        </div>
        <div class="row">
            <button class="btn-primary col-1 " id="refreshButton">
                Refresh
            </button>
            <button class="btn-primary col-1 ms-auto" id="exitButton">
                Exit
            </button>
        </div>
    </div>
</body>

</html>