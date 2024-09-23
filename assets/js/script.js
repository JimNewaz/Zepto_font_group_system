	$(document).ready(function () {
		displayFonts();
		displayAllFontGroups();
	});

	function displayFonts() {
		fetch('./functions/FontsController.php?action=displayFonts')
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.text();
			})
			.then(data => {
				document.getElementById('fontList').innerHTML = data;
				$('#fontTable').DataTable().destroy();
				$('#fontTable').DataTable({
					"order": [
						[0, "desc"]
					]
				});
			})
			.catch(error => {
				console.error('Error fetching fonts:', error);
			});
	}

	//  Display All Font Groups start


	function displayAllFontGroups()
	{
		fetch('./functions/FontsController.php?action=displayAllFontGroups')
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.text();
			})
			.then(data => {	
				document.getElementById('fontGroupList').innerHTML = data;
				$('#fontGroupTable').DataTable().destroy();
				$('#fontGroupTable').DataTable({	
					"order": [
						[0, "desc"]
					]
				});
			})
	}


	// Display All Font Groups End 


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
				body: `deleteFont=true&deleteFontId=${fontId}`
			})
			.then(response => response.text())
			.then(data => {
				document.getElementById('fontList').innerHTML = data;
			});
	}