// Wait until the document has been fully loaded
document.addEventListener("DOMContentLoaded", function() {

    // Get the CSRF token from a meta tag in the document head
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Define a function to get place details from the server
    function getPlaceDetails(placeId) {

        // Send a POST request to the server
        fetch('/actions/search/search/get-place-details', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken, 
            },
            body: JSON.stringify({ place_id: placeId }),  
        })
        .then(response => response.json())  // Parse the response as JSON
        .then(data => {
            if (data.error) {  // If there was an error, log it
                console.error('Error:', data.error);
            } else {  // Otherwise, generate some HTML and append it to the page
                const cityInfo = `<h1 class="text-xl font-bold text-blue-600">${data.city}</h1>`;
                const locationInfo = `<div class="pt-6"><p>Latitude: <span class=" text-gray-500" >${data.latitude}</span></p><p>Longitude:<span class=" text-gray-500"> ${data.longitude}</span></p></div>`;
                const directionsLink = `
                    <div class="absolute flex right-0 bottom-0 p-2 ">
                        <a href="https://www.google.com/maps?q=${encodeURIComponent(data.city)}" target="_blank" class="mt-2 mr-1 text-main3 underline">Get directions</a>
                        <svg id="Group_12" data-name="Group 12" xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                            <circle id="Ellipse_1" data-name="Ellipse 1" cx="19" cy="19" r="19" fill="#c86e04"/>
                            <g id="np_arrow-right_888647_000000" transform="translate(16 11.711)">
                                <path id="Path_4" data-name="Path 4" d="M54.856,36.66l-1.524-1.524L59.1,29.371l-5.766-5.765,1.524-1.524,7.288,7.289Z" transform="translate(-53.332 -22.082)" fill="#fff"/>
                            </g>
                        </svg>
                    </div>
                `;
                const resultContainer = document.getElementById('result');
                resultContainer.innerHTML += `
                    <div class="mx-auto h-50 bg-white w-auto p-3 relative rounded-lg mt-3">
                        ${cityInfo}
                        ${locationInfo}
                        ${directionsLink}
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error:', error));  // If there was an error with the fetch
    }

    // Define a function to submit the search form
    function submitForm(e) {
        e.preventDefault();  
        const city = document.getElementById('city').value;  // Get the value of the city input

        // Send a POST request to the server
        fetch('/actions/search/search/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken,  
            },
            body: JSON.stringify({ city: city }),  
        })
        .then(response => response.json())  // Parse the response as JSON
        .then(data => {
            if (data.error) {  // If there was an error, show it on the page
                document.getElementById('error').innerText = data.error;
                document.getElementById('result').innerHTML = '';
            } else {
                document.getElementById('error').innerText = '';
                document.getElementById('result').innerHTML = ''; 

                // Get details for each prediction
                data.predictions.forEach(prediction => getPlaceDetails(prediction.place_id));
            }
        })
        .catch(error => console.error('Error:', error));  // If there was an error with the fetch, log it
    }

    // Add an event listener to the search form to call submitForm when it is submitted
    document.getElementById('searchForm').addEventListener('submit', submitForm);
});
