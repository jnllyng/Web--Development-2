/******w**************

  Assignment 4 JavaScript
  Name: Princepal Singh
  Date:
  Description: Uses Fetch API to search Winnipeg park data and show matching results interactively on the page. 

*********************/

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("searchForm");
  const results = document.getElementById("results");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    results.innerHTML = "<p>Loading results...</p>";

    const parkName = document.getElementById("parkName").value.trim();
    const limit = document.getElementById("limitSelect").value;
    const order = document.getElementById("orderSelect").value;

    if (!parkName) {
      results.innerHTML = "<p>Please enter a park name.</p>";
      return;
    }

    const baseURL = "https://data.winnipeg.ca/resource/tx3d-pfxq.json";
    const query = `?$where=lower(park_name) LIKE lower('%${parkName}%')&$order=${order}&$limit=${limit}`;
    const apiURL = encodeURI(baseURL + query);

    try {
      const response = await fetch(apiURL);
      const data = await response.json();

      if (data.length === 0) {
        results.innerHTML = `<p>No parks found for "${parkName}".</p>`;
      } else {
        results.innerHTML = data.map(park => `
          <div class="result-item">
            <strong>${park.park_name || "Unnamed Park"}</strong><br>
            Neighbourhood: ${park.neighbourhood || "N/A"}<br>
            Type: ${park.park_type || "N/A"}
          </div>
        `).join("");
      }
    } catch (error) {
      results.innerHTML = "<p>Error retrieving data. Please try again later.</p>";
      console.error(error);
    }
  });
});
