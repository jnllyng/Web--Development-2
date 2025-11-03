/*
	Coding Challenge 6
	Canada JSON Challenge
	Load the JSON, create HTML elements and insert
	values from the JSON into the HTML
	May 15 2018
*/

//adds an event listener to execute load function when page finished loading
document.addEventListener("DOMContentLoaded", load);

/*
 * Initial setup and adding of event listeners to the buttons and loading the page
 * dynamically from the JSON file - run when the page loads
 */
function load() {
	fetch('people.json')
		.then(function (result) {
			return result.json();
		})
		.then(function (data) {
			createCard(data);
		});

}

function createCard(peopleData) {
	let body = document.getElementsByTagName("body")[0];
	let addressLabels = ["Street: ", "City: ", "Province: ", "Postal Code: "];


	for (let i = 0; i < peopleData.length; i++) {
		let section = document.createElement("section");
		let h2 = document.createElement("h2");
		let h4 = document.createElement("h4");
		let p = document.createElement("p");

		/*
			Code here
			Complete the for loop adding all values from the JSON
		*/
		let values = Object.values(peopleData[i]);
		let address = peopleData[i].address;
		h2.innerHTML = `${peopleData[i].name}`;
		h4.innerHTML = `${peopleData[i].age}`;
		p.innerHTML = `${addressLabels[0]}${address.street}<br>
						${addressLabels[1]}${address.street}<br>
						${addressLabels[2]}${address.street}<br>
						${addressLabels[3]}${address.street}`;

		section.appendChild(h2);
		section.appendChild(h4);
		section.appendChild(p);
		body.appendChild(section);

	}

}




































