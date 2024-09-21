	
new DataTable('#fontTable');

function uploadFont() {
    var formData = new FormData(document.getElementById('uploadForm'));
    
    var fontFile = formData.get('font'); 
    // console.log(fontFile);
    
    fetch('./functions/FontsController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        console.log(data);
        document.getElementById('fontList').innerHTML = data;
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
        alert('An error occurred while uploading the font.');
    });
}

// Delete font via AJAX
function deleteFont(fontId) {
    fetch('./functions/FontsController.php', {
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