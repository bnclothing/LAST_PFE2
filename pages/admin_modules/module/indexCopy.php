<!DOCTYPE html>
<html lang="en">

<?php
include("../../../php/database.php");
session_start();

$cmp = 0;
if ($_SESSION["login"] == 1) {
    $cmp = 1;
}
if ($cmp == 0) {
    header("location:../../../");
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="stylesheet" href="../../../css/help.css">
    <link rel="stylesheet" href="../../../css/home.css">
    <link rel="stylesheet" href="../../../css/module.css">
    <link rel="stylesheet" href="../../../fontawesome-free-6.5.1-web/css/fontawesome.css">
    <link rel="stylesheet" href="../../../fontawesome-free-6.5.1-web/css/brands.css">
    <link rel="stylesheet" href="../../../fontawesome-free-6.5.1-web/css/solid.css">
</head>

<body>
    <?php
    include("../../../includes/help.php");
    ?>

    <div class="screen">
        <div class="back-container">
            <button class="btn Back" onclick="window.location.href='../'">Back</button>
        </div>
        <div class="content">
            <div class="filliere">
                GI
            </div>
            <div class="selected-modules">
                <div class="line">
                    <div class="col" style="width: 70%;">
                        <select name="" class="module-input" id="moduleEdit">
                            <option value="" class="module">java</option>
                            <option value="" class="module">algebra</option>
                            <option value="" class="module">net</option>
                        </select>
                    </div>
                    <div class="col" style="width: 30%;">
                        <?php
                        echo "<a  href=?edit=" . 1 . " ><span><i class='fa-solid fa-user-pen fa-xl' ></i></span></a>";

                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                        echo "<a href=?delete=" . 1 . " id='delete'><span><i class='fa-solid fa-user-slash fa-xl' ></i></span></a>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="modules">
                <select name="" id="modules">
                    <option value="" class="module" selected disabled>--- Select Module ---</option>
                    <option value="" class="module">java</option>
                    <option value="" class="module">algebra</option>
                    <option value="" class="module">net</option>
                </select>
                <button class="btn Add">ADD</button>

            </div>
        </div>
    </div>

    <?php
    include("../../../includes/scripts.php");
    ?>
</body>

</html>