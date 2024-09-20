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