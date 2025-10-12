/******w**************
    
    Assignment 4 Javascript
    Name: Jueun Yang
    Date: 2025-10-09
    Description:

*********************/

document.querySelector('#searchForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const treeName = document.querySelector('#treeName').value.trim();
    const resultsDiv = document.querySelector('#results');
    resultsDiv.innerHTML = '';

    if (!treeName) {
        resultsDiv.innerHTML = '<p>Please enter a tree name.</p>';
        return;
    }

    const apiUrl = `https://data.winnipeg.ca/resource/hfwk-jp4h.json?$where=lower(common_name) like lower('%${treeName}%')&$order=diameter_at_breast_height DESC&$limit=100`;
    const encodedURL = encodeURI(apiUrl);

    try {
        const response = await fetch(encodedURL);
        const data = await response.json();

        if (data.length === 0) {
            resultsDiv.innerHTML = '<p>No trees found.</p>';
            return;
        }

        data.forEach(item => {
            const div = document.createElement('div');
            div.classList.add('result-card');
            div.innerHTML = `
        <h3>${item.common_name}</h3>
        <p><strong>Botanical Name:</strong> ${item.botanical_name || 'N/A'}</p>
        <p><strong>Neighborhood:</strong> ${item.neighbourhood || 'N/A'}</p>
        <p><strong>Diameter at Breast Height:</strong> ${item.diameter_at_breast_height || 'N/A'} cm</p>
        <p><strong>Park:</strong> ${item.park || 'N/A'}</p>
        <p><strong>Location Class:</strong> ${item.location_class || 'N/A'}</p>
        <p><strong>Property Type:</strong> ${item.property_type || 'N/A'}</p>
        <p><strong>Street:</strong> ${item.street || 'N/A'}</p>
        <p><strong>Cross Streets:</strong> ${item.x_street_from || 'N/A'} - ${item.x_street_to || 'N/A'}</p>
      `;
            resultsDiv.appendChild(div);
        });

    } catch (err) {
        resultsDiv.innerHTML = '<p>Error fetching data.</p>';
        console.error(err);
    }
});
