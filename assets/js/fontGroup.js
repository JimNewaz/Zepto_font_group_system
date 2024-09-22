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
        const fontSelects = $('.fontSelect').map(function () {
            return $(this).val();
        }).get();
        if (fontSelects.filter(Boolean).length < 2) {
            event.preventDefault();
            alert('Please select at least two fonts to create a group.');
        }
    });

    disableSubBtn();
    fetchAndPopulateFonts();
});



