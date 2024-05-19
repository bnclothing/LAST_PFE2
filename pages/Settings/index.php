<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/help.css">
    <link rel="stylesheet" href="../../css/settings.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/all.css">
</head>

<body>
    <?php
    include("../../includes/navbar.php");
    include("../../includes/help.php");
    ?>

    <div class="container-as">
        <form id="langForm" action="">
            <div class="btns">
                <button type="submit"><i class="fa-solid fa-check fa-xl"></i></button>
            </div>
            <div class="row">
                <div class="title">Language</div>
                <div class="value">
                    <div class="line">
                        <label for="lang">Text Color</label>
                        <div class="input-container">
                            <select name="lang" id="lang">
                                <option value="English">English</option>
                                <option value="Arabic">Arabic</option>
                                <option value="French">French</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('langForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission

            const lang = document.getElementById('lang').value;

            // Set localStorage for color and background image
            localStorage.setItem('lang', lang);


            // Show a confirmation message
            alert('Settings saved!');

            window.location.reload();
        });


        document.addEventListener('DOMContentLoaded', function() {

            const savedLang = localStorage.getItem('lang');
            // Set selected language based on saved value
            document.getElementById('lang').value = savedLang || 'English';
        });
    </script>


    <?php
    include("../../includes/scripts.php");
    ?>
</body>

</html>