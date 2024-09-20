<?php
    require_once 'includes/header.php';
?>

<body>
    <div class="container">
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="container">
                <label for="fontFile" class="et_pb_contact_form_label">Enter</label>
                <input type="file" id="fontFile" name="font" accept=".ttf" class="file-upload" onchange="uploadFont()">
            </div>
        </form>

        <ul id="fontList" class="list-group"></ul>
    </div>

    <?php
        require_once 'includes/scripts.php';
    ?>

    <script type="text/javascript" src="assets/js/fontUploader.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
</body>

</html>