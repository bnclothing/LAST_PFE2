<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/help.css">
    <link rel="stylesheet" href="../../css/assignments.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/all.css">
</head>

<body>
    <?php
    include("../../includes/navbar.php");
    include("../../includes/help.php");

    if ($_SESSION["name_role"] == "teacher") {
    ?>
        <div class="assignments">
            <div class="row colored first">
                <div class="col" style="width: 25%;">Module</div>
                <div class="col" style="width: 25%;">Title</div>
                <div class="col" style="width: 50%;">Description</div>
            </div>
            <div class="assignments-table">

                <?php

                // Query to fetch assignments for teacher ID 44 and group by MODULE_NAME
                $sql = $db->prepare("SELECT a.ID_ASSIGNMENT,a.TITLE, a.DESCRIPTION, m.MODULE_NAME
FROM assignment a
JOIN modules m ON a.ID_MODULE = m.ID_MODULE
WHERE a.ID_TEACHER = :id_user
ORDER BY m.MODULE_NAME"); // Order by MODULE_NAME to group assignments

                $sql->bindParam(':id_user', $_SESSION["id_user"]);
                $sql->execute();
                $result = $sql->fetchAll();


                $currentModule = null;
                $totalModules = count($result);
                $currentModuleIndex = 0;

                // Iterate over the results and populate the rows
                foreach ($result as $row) {
                    $currentModuleIndex++;
                    // Check if the module has changed
                    if ($currentModule !== $row['MODULE_NAME']) {
                        // Close the previous module if it exists
                        if ($currentModule !== null) {
                            echo '</div>'; // Close the module div
                            echo '</div>'; // Close the modules div
                        }
                        // Start a new module section for the new module
                        echo '<div class="module">';
                        echo '<div class="module-name';
                        if ($currentModuleIndex === $totalModules) {
                            echo ' last-module-name';
                        }
                        echo '" onclick="toggleModule(this)">' . $row['MODULE_NAME'] . '</div>';
                        echo '<div class="modules" style="display:none">';
                        $currentModule = $row['MODULE_NAME'];
                    }
                    // Display the assignment details
                    echo '<div class="row';
                    if ($currentModuleIndex === $totalModules) {
                        echo ' last-row';
                    }
                    echo '" onclick="goToAss(' . $row['ID_ASSIGNMENT'] . ')">';
                    echo '<div class="col" style="width: 25%; border-right:1px solid #101827aa">' . $row['MODULE_NAME'] . '</div>';
                    echo '<div class="col" style="width: 25%; border-right:1px solid #101827aa">' . $row['TITLE'] . '</div>';
                    echo '<div class="col" style="width: 50%;">' . $row['DESCRIPTION'] . '</div>';
                    echo '</div>';
                }

                // Close the last module section
                if ($currentModule !== null) {
                    echo '</div>'; // Close the module div
                    echo '</div>'; // Close the modules div
                }
                ?>

            </div>
        </div>


        <div class="btns">
            <button class="addAss" onclick="modal()"><i class="fa-solid fa-plus fa-2xl"></i></button>
        </div>
    <?php
    } else {
    ?>
        <div class="container-as" id="assignments-container">

            <?php
            $currentUserId = $_SESSION['id_user']; // Assuming 'id_user' is the session variable storing the user ID

            $sql = $db->prepare("SELECT DISTINCT assignment.*, CONCAT(users.FIRST_NAME, ' ', users.LAST_NAME) AS TEACHER_NAME
                FROM assignment
                JOIN users ON assignment.ID_TEACHER = users.ID_USER
                JOIN users_fillieres_assoc ON users_fillieres_assoc.User_ID = users.ID_USER
                JOIN fillieres ON fillieres.ID_FILLIERE = users_fillieres_assoc.Filliere_ID
                WHERE fillieres.ID_DEPARTMENT = (SELECT ID_DEPARTMENT FROM users_fillieres_assoc WHERE User_ID = :userId)
                ORDER BY assignment.ID_TEACHER");
            $sql->bindParam(':userId', $currentUserId);
            $sql->execute();
            $result = $sql->fetchAll();
            
            $currentTeacher = null;
            
            foreach ($result as $assignment) {
                // Check if the teacher has changed
                if ($currentTeacher !== $assignment['ID_TEACHER']) {
                    // Close the previous card if it exists
                    if ($currentTeacher !== null) {
                        echo '</div>'; // Close the type div
                        echo '</div>'; // Close the assignment-card div
                    }
                    // Start a new card for the new teacher
                    echo '<div class="card assignment-card">';
                    echo '<div class="side1">' . $assignment['TEACHER_NAME'] . '</div>';
                    echo '<div class="side2">'; // Open the side2 div
                    $currentTeacher = $assignment['ID_TEACHER'];
                }
                // Display the assignment title
                echo '<div class="type" onclick="goToAss(' . $assignment['ID_ASSIGNMENT'] . ')">' . $assignment['TITLE'] . '</div>';
            }
            
            // Close the last card
            if ($currentTeacher !== null) {
                echo '</div>'; // Close the type div
                echo '</div>'; // Close the assignment-card div
            }
            
            ?>


        </div>

    <?php
    }

    ?>

    <div id="addUserModal" class="modal exclude">
        <div class="modal-content exclude">
            <form action="addAssignment.php" method="POST" class="addUserForm exclude" enctype="multipart/form-data">

                <label for="module" class="exclude">Module:</label>
                <select name="module" id="module" class="exclude">
                    <?php
                    // Fetch Module options from PHP without echoing them here
                    $sql = $db->prepare("SELECT m.* FROM modules m 
                    INNER JOIN modules_fillieres_association mfa ON m.ID_MODULE = mfa.id_module
                    INNER JOIN users_fillieres_assoc ufa ON mfa.id_filliere = ufa.Filliere_ID
                    WHERE ufa.User_ID = ?");
                    $sql->execute([$_SESSION["id_user"]]);
                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);


                    foreach ($result as $module) {
                        echo '<option value="' . $module['ID_MODULE'] . '">' . $module['MODULE_NAME'] . '</option>';
                    }
                    ?>
                </select>

                <label for="title" class="exclude">Title:</label>
                <input autocomplete="off" type="text" name="title" id="title" class="exclude" required>

                <label for="description" class="exclude">Description:</label>
                <input type="text" name="description" id="description" class="exclude" required>

                <!-- File input container -->
                <div class="file-input-container">
                    <!-- Custom file input button -->
                    <label for="file-upload" class="custom-file-input">Choose File</label>
                    <!-- Hidden default file input -->
                    <input id="file-upload" type="file" name="file" id="file" onchange="updateFileName(this)">
                    <!-- Display the selected file name -->
                    <span id="file-name" class="selected-file">No file chosen</span>
                </div>





                <label for="due_date" class="exclude">Due Date:</label>
                <input type="date" name="due_date" id="due_date" class="exclude" min="<?php echo date('Y-m-d'); ?>" required>


                <div class="form-buttons exclude">
                    <input autocomplete="off" type="submit" class="exclude" value="Ajouter" name="submit">
                    &nbsp;&nbsp;&nbsp;
                    <input autocomplete="off" type="reset" class="exclude" value="Annuler" name="reset" onclick="hide()">
                </div>
            </form>

        </div>
    </div>







    <script>
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

        // Calculate and set the height of each .type element based on the number of elements
        document.querySelectorAll('.side2').forEach(typeContainer => {
            const typeElements = typeContainer.querySelectorAll('.type');
            const numTypeElements = typeElements.length;

            if (numTypeElements >= 3) {
                typeElements.forEach(element => {
                    element.style.height = '33.3%'; // Set height to 33.3% for each element
                });
            } else {
                const heightPercentage = 100 / numTypeElements;
                typeElements.forEach(element => {
                    element.style.height = `${heightPercentage}%`; // Set height based on the number of elements
                });
            }
        });


        function toggleModule(module) {
            // Find the module's assignments div
            let moduleAssignments = module.nextElementSibling;
            // Toggle the display style
            if (moduleAssignments.style.display === 'none') {
                moduleAssignments.style.display = 'block';
                module.classList.add('border-all');
            } else {
                moduleAssignments.style.display = 'none';
                module.classList.remove('border-all');
            }
        }



        function goToAss(assignmentId) {
            window.location.href = "../assignment/?assignment=" + assignmentId;
        }
    </script>

    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            document.getElementById('file-name').innerText = fileName;
        }
    </script>

    <?php
    include("../../includes/scripts.php");
    ?>
</body>

</html>