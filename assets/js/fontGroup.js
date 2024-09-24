let fontCount = 0;

function disableSubBtn() {
    if (fontCount >= 2) {
        document.getElementById('subBtn').disabled = false;
    } else {
        document.getElementById('subBtn').disabled = true;
    }
}

function fetchAndPopulateFonts() {
    $.ajax({
        type: "GET",
        url: "./functions/FontsController.php",
        data: {
            action: "displayAllFonts"
        },
        success: function (response) {
            $('.fontSelect').each(function() {
                $(this).html(response); 
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

function addFontRow() {
    let rowHtml = `                
        <div class="align-items-center mb-2 fontGroupCard" id="row${fontCount}">
            <div class="row">
                <div class="col">
                    <input class="form-control" placeholder="Font Name" name="fontNames[]">
                </div>
                <div class="col">
                    <select class="form-control fontSelect" name="fonts[]">
                        <option value="">Select a Font</option>
                    </select>
                </div>                    
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove-row" ${fontCount === 0 ? 'disabled' : ''}>X</button>
                </div>
            </div>
        </div>                
    `;

    $('#fontGroupContainer').append(rowHtml);
    fontCount++;
    disableSubBtn(); 
    fetchAndPopulateFonts(); 
}

// Delete
function deleteFontGroup(groupId) {
    fetch('./functions/FontsController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `deleteFontGroup=true&deleteFontGroup=${groupId}`
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('fontGroupList').innerHTML = data;
        });
}

//  Display All Font Groups start

function displayAllFontGroups() {
    fetch('./functions/FontsController.php?action=displayAllFontGroups')
        .then(response => response.text())
        .then(data => {
            document.getElementById('fontGroupList').innerHTML = data;

            $('#fontGroupTable').DataTable().destroy();
            $('#fontGroupTable').DataTable({
                "order": [[0, "desc"]]
            });
            
            // $('#fontGroupTable').DataTable().ajax.reload();

            document.querySelectorAll('.edit-font-group').forEach(button => {
                button.addEventListener('click', function () {
                    const groupId = this.getAttribute('data-group-id');
                    const groupName = this.getAttribute('data-group-name');                    
                    console.log(groupId, groupName);

                    // Set the group name in the modal
                    document.getElementById('editgroupName').value = groupName;
                    document.getElementById('editGroupId').value = groupId;                    

                    fetch('./functions/FontsController.php?action=getAllFonts')
                        .then(response => response.json())
                        .then(fonts => {
                            const fontSelect = document.getElementById('groupFonts');
                            fontSelect.innerHTML = ''; 
                            fonts.forEach(font => {
                                const option = document.createElement('option');
                                option.value = font.id;
                                option.textContent = font.font_name;
                                
                                if (this.getAttribute('data-font-names').includes(font.font_name)) {
                                    option.selected = true;
                                }

                                fontSelect.appendChild(option);
                            });
                        });

                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editFontGroupModal'));
                    editModal.show();
                });
            });

        })
        .catch(error => console.error('Error fetching font groups:', error));
}

// Display All Font Groups End 


$(document).ready(function () {   
    addFontRow();

    $('#addRowBtn').click(function () {
        addFontRow();
    });
    
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.fontGroupCard').remove();
        fontCount--;
        console.log(fontCount);
        disableSubBtn();
    });
    
    $('#fontGroupForm').submit(function (event) {
        event.preventDefault(); 
        const groupName = $('#groupName').val().trim(); 
        const fontSelects = $('.fontSelect').map(function () {
            return $(this).val(); 
        }).get();
    
        // Validation
        if (groupName === "" || fontSelects.filter(Boolean).length < 2) {
            alert('Please enter a group name and select at least two fonts to create a group.');
            return; 
        }
    
        // Submit
        $.ajax({
            type: "POST",
            url: "./functions/FontsController.php",
            data: {
                action: "createGroup",
                groupName: groupName,
                fonts: fontSelects
            },
            success: function(response) {
                console.log("Group created successfully:", response);      
                const data = JSON.parse(response);

                if(data.success) {
                    displayAllFontGroups();
                } else {
                    console.error("Error in backend response:", response.error);
                }

                // displayAllFontGroups();      
            },
            error: function(xhr) {
                console.error("An error occurred:", xhr.responseText);                            
            }
        });
    });
    

    document.getElementById('saveChangesBtn').addEventListener('click', function () {
        const groupId = document.getElementById('editGroupId').value;
        const groupName = document.getElementById('editgroupName').value;

        const selectedFonts = Array.from(document.getElementById('groupFonts').selectedOptions)
        .map(option => option.value);
    
        const formData = new FormData();
        formData.append('groupId', groupId);
        formData.append('groupName', groupName);
        formData.append('selectedFonts', JSON.stringify(selectedFonts));
        
        // console.log(formData);
        fetch('./functions/FontsController.php?action=updateFontGroup', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editFontGroupModal'));
            editModal.hide();
            displayAllFontGroups();
        })
        .catch(error => console.error('Error saving font group:', error));
    });



    disableSubBtn();
    fetchAndPopulateFonts();
    displayAllFontGroups();      
});



