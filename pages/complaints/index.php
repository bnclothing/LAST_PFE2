<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/users.css">
    <link rel="stylesheet" href="../../css/help.css">
    <link rel="stylesheet" href="../../css/complaints.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/fontawesome.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/brands.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/solid.css">

    <title>ClassTalk | Complaints</title>
</head>

<body>
    <?php
    include("../../includes/navbar.php");
    include("../../includes/help.php");
    ?>

    <div class="top">
        <div class="searchB">
            <input autocomplete="off" type="text" name="" id="search" placeholder="Search">
        </div>
        <div class="searchByB">
            <select name="" id="searchBy">
                <option selected value="complaint">Message</option>
                <option value="time">Submitted at</option>
                <?php
                if ($_SESSION["name_role"] == "admin") {
                    echo '<option value="ID_USER">Submited by</option>';
                }
                ?>
            </select>
        </div>
        <?php if ($_SESSION["name_role"] != "admin") : ?>
            <div class="addB">
                <input autocomplete="off" type="submit" value="add" id="AddUserButton" onclick="modal()">
            </div>
        <?php endif; ?>

    </div>

    <section class="wrapper">
        <!-- Row title -->
        <main class="row title">
            <ul>
                <li>Message</li>
                <li>Submited by</li>
                <li>Submitted at</li>
                <li>Type</li>
                <li>Status</li>
                <li>Operations</li>
            </ul>
        </main>
        <!-- Row 1 - fadeIn -->
        <div class="wrapperV2">
            <?php
            $sql;
            if ($_SESSION["name_role"] == "admin") {
                $sql = $db->prepare("SELECT * FROM complaints c,complaint_status s,complaint_type t,users u where c.TYPE=t.id_type and c.STATUS=s.id_status and c.ID_USER=u.ID_USER");
            } else {
                $sql = $db->prepare("SELECT * FROM complaints c,complaint_status s,complaint_type t,users u where c.TYPE=t.id_type and c.STATUS=s.id_status and c.ID_USER=u.ID_USER and c.ID_USER=" . $_SESSION["id_user"]);
            }
            $sql->execute();
            $result = $sql->fetchAll();
            foreach ($result as $value) {
                echo '<section class="row-fadeIn-wrapper">';
                echo '<article class="row nfl">';
                echo "<ul>";
                echo "<li>" . $value["complaint"] . "</li>";
                echo "<li>" . $value["EMAIL"] . "</li>";
                echo "<li>" . $value["time"] . "</li>";
                echo "<li>" . $value["type_labelle"] . "</li>";
                echo "<li>" . $value["status_labelle"] . "</li>";
                echo "<li>";
                echo "<a href=../complaint/?id=" . $value["ID_COMPLAINT"] . "><span><i class='fa-regular fa-folder-open'></i></span></a>";
                echo "</li>";
                echo "</ul>";
                echo "</article>";
                echo "</section>";
            }
            ?>
        </div>
    </section>

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <form action="addComplaint.php" method="POST" class="addUserForm" enctype="multipart/form-data">

                <label for="Type">Type:</label>
                <select name="Type" id="Type">
                    <?php
                    // Assuming $db is your existing PDO connection
                    $sql = "SELECT id_type, type_labelle FROM complaint_type";
                    $stmt = $db->query($sql);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($rows) > 0) {
                        // Output data of each row
                        foreach ($rows as $row) {
                            echo '<option value="' . htmlspecialchars($row['id_type'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['type_labelle'], ENT_QUOTES, 'UTF-8') . '</option>';
                        }
                    } else {
                        echo '<option value="">No types available</option>';
                    }
                    ?>

                </select>

                <label for="Message">Message:</label>
                <textarea autocomplete="off" name="Message" id="Message" required></textarea>

                <div class="form-buttons">
                    <input autocomplete="off" type="submit" value="Ajouter" name="submit">
                    &nbsp;&nbsp;&nbsp;
                    <input autocomplete="off" type="reset" value="Annuler" name="reset" onclick="hide()">
                </div>
            </form>

        </div>
    </div>

    <?php
    include("../../includes/scripts.php");
    ?>

    <script>
        document.getElementById('search').addEventListener('keyup', function(e) {
            var searchBy = document.getElementById('searchBy').value;
            var search = document.getElementById('search').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'complaintSearch.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.querySelector('.wrapperV2').innerHTML = xhr.responseText;
                }
            };

            var data = 'searchBy=' + encodeURIComponent(searchBy) + '&search=' + encodeURIComponent(search);
            xhr.send(data);
        });

        function modal() {
            var modal = document.getElementById("addUserModal");
            modal.style.display = "flex";
            setTimeout(() => {
                modal.style.top = "0";
            }, 100);
        }

        function hide() {
            var modal = document.getElementById("addUserModal");
            modal.style.top = "100vh";
            setTimeout(() => {
                modal.style.display = "none";
            }, 500);
        }
    </script>
</body>

</html>