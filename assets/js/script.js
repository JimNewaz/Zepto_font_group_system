	$(document).ready(function () {
		displayFonts();
	});

	$( '#multiple-select-field' ).select2( {
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    closeOnSelect: false,
	});


	function loadGoogleFonts(fontFamilies) {
    const linkElement = document.createElement('link');
    linkElement.rel = 'stylesheet';
    linkElement.href = `https://fonts.googleapis.com/css2?family=${fontFamilies.join('&family=')}&display=swap`;

    document.head.appendChild(linkElement);
	}

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
				// displayFonts();
				fetchAndPopulateFonts();
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