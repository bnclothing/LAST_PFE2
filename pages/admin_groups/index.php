<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Groups</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/help.css">
    <link rel="stylesheet" href="../../css/assignments.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.5.1-web/css/all.css">
</head>

<body>
    <?php
    include("../../includes/navbar.php");
    include("../../includes/help.php");
    ?>

    <div class="container-as">
        <?php
        $sql = $db->prepare("SELECT gd.ID_GROUP_DISCUSSION AS ID ,gd.NAME AS GroupName, gdt.libelle_gc_type AS Type, gd.entity_type AS Entity FROM groupdiscussion gd JOIN groupdiscussiontype gdt ON gd.TYPE = gdt.id_gc_type ORDER BY gd.ID_GROUP_DISCUSSION");
        $sql->execute();
        $result = $sql->fetchAll();

        // Group discussions by GroupName
        $groupedDiscussions = [];
        foreach ($result as $value) {
            if (!isset($groupedDiscussions[$value['GroupName']])) {
                $groupedDiscussions[$value['GroupName']] = [
                    'GroupName' => $value['GroupName'],
                    'Types' => [],
                    'IDs' => [] // Initialize an array for IDs
                ];
            }
            $groupedDiscussions[$value['GroupName']]['Types'][] = $value['Type'];
            $groupedDiscussions[$value['GroupName']]['IDs'][] = $value['ID']; // Add ID to the array
        }


        // Display grouped discussions
        foreach ($groupedDiscussions as $discussion) {
            echo '<div class="card">
            <div class="side1">
            ' . $discussion['GroupName'] . '
            </div>
            <div class="side2"> ';
            foreach ($discussion['Types'] as $key => $type) {
                echo '<div class="type" onclick="deleteGroupChat(' . $discussion['IDs'][$key] . ',this)">' . $type . '</div>';
            }
            echo '</div>
        </div>';
        }
        ?>
        <div class="card" onclick="modal()">
            <i class="fa-solid fa-plus fa-2xl"></i>
        </div>
    </div>

    <div id="addUserModal" class="modal exclude">
        <div class="modal-content exclude">
            <form action="addGroup.php" method="POST" class="addUserForm exclude">

                <label for="name" class="exclude">Name:</label>
                <input autocomplete="off" type="text" name="name" id="name" class="exclude" required>

                <label for="type" class="exclude">Type: </label>
                <select name="type" id="type" class="exclude">
                    <?php
                    // Fetch Type options from PHP without echoing them here
                    $sql = $db->prepare("SELECT * FROM groupdiscussiontype");
                    $sql->execute();
                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $filliere) {
                        echo '<option value="' . $filliere['id_gc_type'] . '">' . $filliere['libelle_gc_type'] . '</option>';
                    }
                    ?>
                </select>


                <input type="hidden" name="entity_type" value="Global" class="exclude" required>


                <div class="form-buttons exclude">
                    <input autocomplete="off" type="submit" class="exclude" value="Ajouter" name="submit">
                    &nbsp;&nbsp;&nbsp;
                    <input autocomplete="off" type="reset" class="exclude" value="Annuler" name="reset" onclick="hide()">
                </div>
            </form>

        </div>
    </div>

    <script>
        // Calculate and set the height of each .type element based on the number of elements
        // Calculate and set the height of each .type element based on the number of elements
        document.querySelectorAll('.side2').forEach(typeContainer => {
            const typeElements = typeContainer.querySelectorAll('.type');
            const numTypeElements = typeElements.length;

            // Calculate the height based on the number of elements
            const heightPercentage = 100 / numTypeElements;
            typeElements.forEach(element => {
                element.style.height = `${heightPercentage}%`; // Subtract padding/margin to avoid overflow
            });

            // Add classes for single type
            if (numTypeElements === 1) {
                typeElements[0].classList.add('single-type');
                typeElements[numTypeElements - 1].classList.add('single-type');
            }
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

        function deleteGroupChat(id, element) {
            if (confirm("Are you sure you want to delete this group chat?")) {
                fetch('deleteGroup.php?id=' + id, {
                        method: 'DELETE'
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            console.error('Failed to delete group chat');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting group chat:', error);
                    });
            }
        }
    </script>



    <?php
    include("../../includes/scripts.php");
    ?>
</body>

</html>