/*
 * Survey Form Validation: 
 * Handles the validation of form elements on the survey page.
 *
 * Script: formvalidation.js
 * Author: Alan Simpson
 * Version: 1.0
 * Date Created: 3.18.2012
 * Last Updated: 11.09.2014
 *
 */

/*
 * Removes white space from a string value.
 *
 * return  A string with leading and trailing white-space removed.
 */
function trim(str) 
{
	// Uses a regex to remove spaces from a string.
	return str.replace(/^\s+|\s+$/g,"");
}

/*
 * Determines if a text field element has input
 *
 * param   fieldElement A text field input element object
 * return  True if the field contains input; False if nothing entered
 */
function formFieldHasInput(fieldElement)
{
	// Check if the text field has a value
	if ( fieldElement.value == null || trim(fieldElement.value) == "" )
	{
		// Invalid entry
		return false;
	}
	
	// Valid entry
	return true;
}

/*
 * Handles the submit event of the survey form
 *
 * param e  A reference to the submit event
 * return   True if no validation errors; False if the form has
 *          validation errors
 */
function validate(e)
{
	//	Hides all error elements on the page
	hideAllErrors();

	//	Determine if the form has errors
	if(formHasErrors()){
		// 	Prevents the form from submitting
		e.preventDefault();
		// 	Returning false prevents the form from submitting
		return false;
	}

	return true;
}

/*
 * Handles the reset event for the form.
 *
 * param e A reference to the reset event
 * return  True allows the reset to happen; False prevents
 *         the browser from resetting the form.
 */
function resetForm(e)
{
	// Confirm that the user wants to reset the form.
	if ( confirm('Clear survey?') )
	{
		// Ensure all error fields are hidden
		hideAllErrors();
		
		// Set focus to the first text field on the page
		document.getElementById("fname").focus();
		
		// When using onReset="resetForm()" in markup, returning true will allow
		// the form to reset
		return true;
	}

	// Prevents the form from resetting
	e.preventDefault();
	
	// When using onReset="resetForm()" in markup, returning false would prevent
	// the form from resetting
	return false;
}

/*
 * Displays the error for an invalid form field.
 *
 * param formField A reference to the form field that caused a validation error.
 * param errorId   The id of the error element to display.
 * param errorFlag True (an error has already occured), False (no errors have occured thus far)
 */
function showError(formField, errorId, errorFlag)
{
	// Set the display style of the error element to diplay
	document.getElementById(errorId).style.display = "block";
	
	// Determine if this is the first error
	// If so, set focus to the text field
	if ( !errorFlag )
	{
		// Set focus to the text field that caused the error
		formField.focus();
		
		if ( formField.type == "text" )
		{
			// Select the text in the text field
			formField.select();
		}		
	}
}

/*
 * Does all the error checking for the form.
 *
 * return   True if an error was found; False if no errors were found
 */
function formHasErrors()
{
	// Code below here
	var errorFlag = false;

	var requiredTextFields = ["fname","lname","studentnum"];

	//	Loop through the text field ids
	for(var i = 0; i < requiredTextFields.length; i++){
		var textField = document.getElementById(requiredTextFields[i]);

		//	Determine if the text field has input
		if(!formFieldHasInput(textField)){
			//	Get the error object from the DOM for the appropriate
			//	error field and make it visible
			document.getElementById(requiredTextFields[i] + "_error").style.display = "block";

			//	Determine if this is the first error
			if(!errorFlag){
				textField.focus();

				textField.select();
			}

			errorFlag = true;
		}
	}

	//	Create a regular expression for a six digit number
	var regex = new RegExp(/^\d{6}$/);

	var studentNumber = document.getElementById("studentnum").value;

	//	Determine if the value is not a number or the length is more than 6 characters
	if(!regex.test(studentNumber)){
		//	Display the appropriate error message
		document.getElementById("studentnum_error").style.display = "block";

		if(!errorFlag){
			textField.focus();
			textField.select();
		}

		errorFlag = true;
	}
	
	//	Validate that the user has selected a program
	var program = ["bit","aviation","accounting","ba"];

	var programChecked = false;

	//	Loop through each program id
	for(var i = 0; i < program.length && !programChecked; i++){
		//	Determine if the current program is checked
		if(document.getElementById(program[i]).checked){
			programChecked = true;
		}
	}

	if(!programChecked){
		document.getElementById("program_error").style.display = "block";

		errorFlag = true;
	}

	//	Validate the major fields
	var program = ["bit","ba"];

	for(var i = 0; i < program.length; i++){
		if(document.getElementById(program[i]).checked && 
			!formFieldHasInput(document.getElementById(program[i] + "major"))){

			document.getElementById(program[i] + "major_error").style.display = "block";

			//	Determine if this is the first error
			if(!errorFlag){
				document.getElementById(program[i] + "major").focus();
			}

		errorFlag = true;
		}
	}

	// Code above here
	return errorFlag;
}

/*
 * Resets (hides) all of the error messages on the page.
 */
function hideAllErrors()
{
	var errorFields = document.getElementsByClassName("error");
	for(var i = 0; i < errorFields.length; i++){
		errorFields[i].style.display = "none";
	}
}

/*
 * Handles the click event for program radio buttons for programs
 * that have a major.
 *
 * param  major  The id of the radio button that was clicked
 */
function showMajor(major)
{
	document.getElementById("majorheading").style.display = "block";

	document.getElementById(((major == "bit") ? "ba":"bit") + "majordiv").style.display = "none";

	//	Show the div element for the selected major
	document.getElementById(major + "majordiv").style.display = "block";

	document.getElementById(major + "major").value = "";

	document.getElementById(major + "major").focus();
}

/*
 * Hides elements in the Major Information section of the page
 */
function hideMajor()
{
	// Hide the heading in the major section
	document.getElementById("majorheading").style.display = "none";

	// Hide the text field input for both majors
	document.getElementById("bitmajordiv").style.display = "none";
	document.getElementById("bamajordiv").style.display = "none";
}

/**
 * Handles the load event of the document.
 */
function load()
{
	// Add event listener for the form submit
	document.getElementById("survey_form").addEventListener("submit", validate, false);

	// Reset the form using the default browser reset
	// This is done to ensure the radio buttons are unchecked when the page is refreshed
	// This line of code must be done before attaching the event listener for the customer reset
	document.getElementById("survey_form").reset();

	// Add event listener for our custom form submit function
	document.getElementById("survey_form").addEventListener("reset", resetForm, false);

	// Add event listeners for the radio buttons
	document.getElementById("bit").addEventListener("click",
		function(){
			showMajor("bit");
		});
	document.getElementById("ba").addEventListener("click",
		function(){
			showMajor("ba");
		});
	document.getElementById("aviation").addEventListener("click", hideMajor);
	document.getElementById("accounting").addEventListener("click", hideMajor);
}

// Add the event listener for the document load
document.addEventListener("DOMContentLoaded", load, false);
















