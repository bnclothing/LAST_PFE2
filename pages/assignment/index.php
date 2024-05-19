<?php

include("../../php/database.php");
session_start();

$cmp = 0;
if ($_SESSION["login"] == 1) {
    $cmp = 1;
}
if ($cmp == 0) {
    header("location:../../");
}

include("../../includes/help.php");

if (isset($_GET['assignment'])) {
    $assignmentId = $_GET['assignment'];

    // Query to fetch assignment information including module name
    $sqlAssignment = $db->prepare("SELECT a.*, m.MODULE_NAME FROM assignment a JOIN modules m ON a.ID_MODULE = m.ID_MODULE WHERE a.id_assignment = ?");
    $sqlAssignment->execute([$assignmentId]);
    $assignment = $sqlAssignment->fetch();

    // Query to fetch assignment responses
    $sqlResponses = $db->prepare("SELECT * FROM assignments_responses WHERE id_assignment = ?");
    $sqlResponses->execute([$assignmentId]);
    $responses = $sqlResponses->fetchAll();

    // Query to check if the current user has already responded to this assignment
    $sqlUserResponse = $db->prepare("SELECT * FROM assignments_responses WHERE id_assignment = ? AND user_id = ?");
    $sqlUserResponse->execute([$assignmentId, $_SESSION['id_user']]);
    $userResponse = $sqlUserResponse->fetch();
}
?>

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
    <div class="back-container">
        <button class="btn Back" onclick="window.location.href='../assignments/'">Back</button>
    </div>
    <div class="container-as" id="assignment-container">
        <div class="assignment">
            <div class="row colored first">
                <div class="col" style="width: 100%;">Module</div>
            </div>
            <div class="row">
                <div class="col" style="width: 100%;"><?php echo $assignment['MODULE_NAME']; ?></div>
            </div>
            <div class="row colored">
                <div class="col" style="width: 100%;">Title</div>
            </div>
            <div class="row">
                <div class="col" style="width: 100%;"><?php echo $assignment['TITLE']; ?></div>
            </div>
            <div class="row colored">
                <div class="col" style="width: 100%;">Description</div>
            </div>
            <div class="row description-hover">
                <div class="col" style="width: 100%;"><?php echo $assignment['DESCRIPTION']; ?></div>
                <div class="description-popup"> <?php echo $assignment['DESCRIPTION']; ?></div>
            </div>
            <div class="row colored">
                <div class="col" style="width: 100%;">File</div>
            </div>
            <div class="row">
                <?php if (!empty($assignment['file_path'])) : ?>
                    <div class="col" style="width: 100%;">
                        <?php
                        $fileName = basename($assignment['file_path']);
                        echo '<a href="../assignments/' . $assignment['file_path'] . '" download>' . $fileName . '</a>';
                        ?>
                    </div>
                <?php else : ?>
                    <div class="col" style="width: 100%;">No file</div>
                <?php endif; ?>
            </div>

            <div class="row colored">
                <div class="col" style="width: 100%;">Due Date</div>
            </div>
            <div class="row last-row">
                <div class="col" style="width: 100%;"><?php echo $assignment['DUE_DATE']; ?></div>
            </div>
        </div>

        <div class="responses">
            <?php if ($_SESSION["name_role"] == "teacher") : ?>

                <div class="row colored first">
                    <div class="col" style="width: 25%;">Date</div>
                    <div class="col" style="width: 25%;">File</div>
                    <div class="col" style="width: 50%;">Description</div>
                </div>
                <?php if (count($responses) > 0) : ?>
                    <?php foreach ($responses as $response) : ?>
                        <div class="row colored">
                            <div class="col" style="width: 25%;"><?php echo $response['timestamp']; ?></div>
                            <div class="col" style="width: 25%;"><?php echo !empty($assignment['file_path']) ? $assignment['file_path'] : 'No file'; ?></div>
                            <div class="col" style="width: 50%;"><?php echo $response['response']; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="row">
                        <div class="col" style="width: 100%;">No responses</div>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <?php if ($userResponse) : ?>
                    <div class="row first">
                        <div class="col" style="width: 100%;">Your Response</div>
                    </div>
                    <div class="row colored">
                        <div class="col" style="width: 100%;"><?php echo $userResponse['response']; ?></div>
                    </div>
                    <div class="row ">
                        <div class="col" style="width: 100%;">Your File</div>
                    </div>
                    <?php if (!empty($userResponse['file_path'])) : ?>
                        <div class="row colored last-row">
                            <div class="col" style="width: 100%;">
                                <?php
                                $responseFileName = basename($userResponse['file_path']);
                                echo '<a href="../assignment/' . $userResponse['file_path'] . '" download>' . $responseFileName . '</a>';
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <form action="addResponse.php" method="POST" class="addUserForm response exclude" enctype="multipart/form-data">

                        <input type="hidden" name="id_assignment" value="<?php echo $assignmentId; ?>">

                        <label for="Response" class="exclude">Response:</label>
                        <textarea name="Response" id="Response" class="exclude" required></textarea>

                        <!-- File input container -->
                        <div class="file-input-container">
                            <!-- Custom file input button -->
                            <label for="file-upload" class="custom-file-input">Choose File</label>
                            <!-- Hidden default file input -->
                            <input id="file-upload" type="file" name="file" id="file" onchange="updateFileName(this)">
                            <!-- Display the selected file name -->
                            <span id="file-name" class="selected-file">No file chosen</span>
                        </div>

                        <div class="form-buttons exclude">
                            <input autocomplete="off" type="submit" class="exclude" value="Submit" name="submit">
                            &nbsp;&nbsp;&nbsp;
                            <input autocomplete="off" type="reset" class="exclude" value="Cancel" name="reset" onclick="hide()">
                        </div>

                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            document.getElementById('file-name').innerText = fileName;
        }
    </script>

    <?php include("../../includes/scripts.php"); ?>
</body>

</html>
