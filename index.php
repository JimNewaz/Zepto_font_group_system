<?php
    require_once 'includes/header.php';
?>

<body>
    <div class="container">
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="container">
                <label for="fontFile" class="et_pb_contact_form_label">Enter</label>
                <input type="file" id="fontFile" name="font" accept=".ttf" class="file-upload"  onchange="uploadFont()">
            </div>
        </form>


        <ul id="fontList" class="list-group">
            
        </ul>
    </div>

    <?php
        require_once 'includes/scripts.php';
    ?>

    <script>
        $(document).ready(function () {
            $('input[type="file"]').on('click', function () {
                $(".file_names").html("");
            })
            if ($('input[type="file"]')[0]) {
                var fileInput = document.querySelector('label[for="fontFile"]');
                fileInput.ondragover = function () {
                    this.className = "et_pb_contact_form_label changed";
                    return false;
                }
                fileInput.ondragleave = function () {
                    this.className = "et_pb_contact_form_label";
                    return false;
                }
                fileInput.ondrop = function (e) {
                    e.preventDefault();
                    var fileNames = e.dataTransfer.files;
                    for (var x = 0; x < fileNames.length; x++) {
                        console.log(fileNames[x].name);
                        $ = jQuery.noConflict();
                        $('label[for="fontFile"]').append(
                            "<div class='file_names'>" + fileNames[x].name + "</div>");
                    }
                }
                $('#fontFile').change(function () {
                    var fileNames = $('#fontFile')[0].files[0].name;
                    $('label[for="fontFile"]').append(
                        "<div class='file_names'>" + fileNames + "</div>");
                    $('label[for="fontFile"]').css('background-color',
                        '##eee9ff');
                });
            }
        });
    </script>

    <script>
        // Upload font via AJAX
        function uploadFont() {
            var formData = new FormData(document.getElementById('uploadForm'));
            fetch('path_to_your_php_file.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('fontList').innerHTML = data;
                });
        }

        // Delete font via AJAX
        function deleteFont(fontId) {
            fetch('path_to_your_php_file.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `deleteFont=true&fontId=${fontId}`
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('fontList').innerHTML = data;
                });
        }
    </script>
</body>

</html>