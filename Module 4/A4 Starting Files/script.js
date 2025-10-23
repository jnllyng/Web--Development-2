/******w**************
    
    Assignment 4 Javascript
    Name: Jueun Yang
    Date: 2025-10-09
    Description: Display results dynamically, and handle empty or error states.

*********************/

document.querySelector("#search-form").addEventListener("submit", async function (e) {
  e.preventDefault();

  let parkName = document.querySelector("#parkName").value.trim();
  let results = document.querySelector("#results");
  results.innerHTML = "";

  if (!parkName) {
    results.innerHTML = "<p>Please enter a valid park name or neighbourhood.</p>";
    return;
  }

  const apiUrl = 'https://data.winnipeg.ca/resource/tx3d-pfxq.json?' +
                 `$where=lower(park_name) LIKE lower('%${parkName}%')` +
                 ` OR lower(neighbourhood) LIKE lower('%${parkName}%')` +
                 '&$order=land_area_in_hectares DESC' +
                 '&$limit=100';
  const encodedURL = encodeURI(apiUrl);

  try {
    let response = await fetch(encodedURL);
    if (!response.ok) throw new Error("Network response error");

    let data = await response.json();

    if (data.length == 0) {
      results.innerHTML = `<p>No parks found for "${parkName}".</p>`;
      return;
    }

    data.forEach(item => {
      let div = document.createElement("div");
      div.classList.add("result-card");
      div.innerHTML = `
        <h3>${item.park_name || "Park name is not set."}</h3>
        <p class="label">Neighbourhood: <span class="value">${item.neighbourhood || "N/A"}</span></p>
        <p class="label">District: <span class="value">${item.district || "N/A"}</span></p>
        <p class="label">Category: <span class="value">${item.park_category || "N/A"}</span></p>
        <p class="label">Land Area (ha): <span class="value">${item.land_area_in_hectares || "N/A"}</span></p>
        <p class="label">Water Area (ha): <span class="value">${item.water_area_in_hectares || "N/A"}</span></p>
        <p class="label">Total Area (ha): <span class="value">${item.total_area_in_hectares || "N/A"}</span></p>
        <p class="label">Address: <span class="value">${item.location_description || "N/A"}</span></p>
      `;
      results.appendChild(div);
    });

  } catch (err) {
    console.error(err);
    results.innerHTML = "<p>Error fetching data.</p>";
  }
});


