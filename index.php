<?php
    require_once 'includes/header.php';
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <h3 class="module_header">FONT MANAGEMENT SYSTEM</h3>
                    <p>Upload your font and create font groups</p>
                    <hr>
                    <!-- <small>Developed By: <a href="https://github.com/abhishekpandey01">Sayed Nur E Newaz</a></small> -->
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form id="uploadForm" enctype="multipart/form-data">
                    <label for="fontFile" class="et_pb_contact_form_label">Enter</label>
                    <input type="file" id="fontFile" name="font" accept=".ttf" class="file-upload"
                        onchange="uploadFont()">
                </form>

                <ul id="fontList" class="list-group"></ul>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table id="fontTable" class="table table-striped " style="width:100%">
                    <thead>
                        <tr>
                            <th>Font Name</th>
                            <th>Preview</th>
                            <th>Action</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>                            
                        </tr>                        
                    </tbody>                    
                </table>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <?php
        require_once 'includes/scripts.php';
    ?>

    <script type="text/javascript" src="assets/js/fontUploader.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
</body>

</html>