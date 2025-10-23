/******w**************
    
    Assignment 4 Javascript
    Name: Group 8
    Date: 16/10/25
    Description: A javascript logic that constructs the API request URL using the input from the form, fectch the data from the API and display the results.

*********************/

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent form from submitting and reloading the page

        const name = document.getElementById('name').value.trim(); // Get input value and remove extra spaces
        
        // Check if the input is empty
        if (!name) {
            return;
        }

        // Construct the API request URL using the input from the form
        const apiUrl = `https://data.winnipeg.ca/resource/mgde-4fua.json?` +
            `$where=lower(council_member) LIKE lower('%${name}%')` +  
            '&$order=amount DESC' +  
            '&$limit=100';  

        const encodedURL = encodeURI(apiUrl);  // Encode the URL to ensure it handles special characters

        // Fetch the data from the API
        fetch(encodedURL)
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('results');
                resultsContainer.innerHTML = '';  // Clear previous results

                // If no results, display a message
                if (data.length === 0) {
                    resultsContainer.innerHTML = '<p>No results found.</p>';
                    return;
                }

                // Display the results
                data.forEach(item => {
                    const resultElement = document.createElement('div');
                    resultElement.classList.add('result-item');
                    resultElement.innerHTML = `
                        <strong>${item.council_member}</strong><br>
                        Expense Amount: $${item.amount}<br>
                        Description: ${item.description}<br>
                        Vendor: ${item.vendor}<br><br>`;
                    resultsContainer.appendChild(resultElement);
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    });
});
