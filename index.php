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
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h4>Our Fonts</h4>
                <p>Browse a list of Zepto fonts to build your font group</p>
                <table id="fontTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>FONT NAME</th>
                            <th>PREVIEW</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="fontList"></tbody>
                </table>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h3>Create Font Group</h3>
                <p>You have to select at least two fonts</p>

                <form id="fontGroupForm">                    
                    <div class="form-group mb-2">
                        <input type="text" class="form-control" id="groupName" name="groupName" placeholder="Enter a Font Group Title"
                            required>
                    </div>

                    <!-- Font Groups -->
                    <div id="fontGroupContainer"></div>
                    
                    <button type="button" class="btn btn-secondary" id="addRowBtn">+ Add Row</button>                    
                    <button type="submit" id="subBtn" class="btn btn-primary" >Create</button>
                </form>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>



    <?php
        require_once 'includes/scripts.php';
    ?>

    <script type="text/javascript" src="assets/js/fontUploader.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="assets/js/fontGroup.js"></script>
</body>

</html>