/**************************************************************************
 *                                                                        *
 *                                                                        *
 *           Script: Markup Editor                                        *
 *          Version: 1.0.0                                                *
 *      Description: an open source markup editor                         *
 *               By: Eyad Fallatah                                        *
 *      Orgnization: Loyola University of Chicago                         *
 *             File: functions.php                                        *
 *                                                                        *
 *                                                                        *
 *************************************************************************/




$(document).ready(function()
{
	// Never cache Ajax requests
    $.ajaxSetup ({  
        cache: false  
    });  




	// On load, Set focus on the text area 
	document.getElementById('userInput').focus();

	 
	 
	 
	 
	    
    // needed variable(s)
    var savedSel;
    var loading_image = "<div class='loading'><img src='images/loading.gif' border='0' alt='loading…'></div>";

	
	
	// get the content of the document
	$("#userInput").ready(function()
	{
	    $("#userInput")  
	        .html(loading_image)  
	        .load('do.php?action=getContent', null, function(responseText){  
	     });	     	  	     
	}); 
 	
 	
 	
 	
 	
 	
 	
	// event when mouse is out of user input area 	
	$("#userInput").mouseout(function()
	{
		savedSel = rangy.saveSelection();
	});
 	
 	
 	
 	
 	
 
	// event when the add mark button is clicked
	$("#addMarkButton").click(function()
	{	
		if( $("#menu").is(":visible") )
		{
			$("#menu").hide("fast");	
		}
		else
		{					
			$("#menu").show("fast");
		}	  	     
	}); 
	
	 	
 	
 	
 	
 	
 	
	// event when the + button is clicked
	$("#markButton").click(function()
	{
		var ns = document.getElementById('ns').value;
		var tag = document.getElementById('tag').value;
		var attr = document.getElementById('attr').value;
		
		rangy.restoreSelection(savedSel);
		
		if((ns != '')&&(tag != '')&&(attr != '')&&(reportSelectionText() != ''))
		{						 			
			$("#menu").hide("fast");
			
			document.getElementById('ns').value = "";
			document.getElementById('tag').value = "";
			document.getElementById('attr').value = "";
			 
			document.getElementById('userInput').focus();
			 
			var thisSelection = rangy.getSelection();
			var sp = thisSelection.anchorOffset;
			var ep = thisSelection.focusOffset;

			 					 			
			$.post("do.php?action=addMark", { ns:ns, tag:tag, attr:attr, sp:sp, ep:ep}, function(data){});
			 			
			surroundRange();
			rangy.removeMarkers(savedSel);						 			
		}
		else
		{
			if(ns == '')
			{
				alert("You have to fill the name space field");
				document.getElementById('ns').focus();
			}
			else if(tag == '')
			{
				alert("You have to fill the tag field");
				document.getElementById('tag').focus();
			}
			else if(attr == '')
			{
				alert("You have to fill the attribute field");
				document.getElementById('attr').focus();
			}
			else
			{
				alert("No selection was made");
				document.getElementById('userInput').focus();
				$("#menu").hide("fast");
			}									
		} 	     
	}); 

	 	

 	
 	
	// event when the clear marks button is clicked
	$("#clearMarksButton").click(function()
	{
	    $("#userInput")  
	        .html(loading_image)  
	        .load('do.php?action=getContent', null, function(responseText){  
	     });  	     
	}); 
});


 	
// returns plain text of user selection
function reportSelectionText()
{
    return rangy.getSelection().getRangeAt(0);
}




// returns HTML text of user selection
function reportSelectionHtml()
{
    return rangy.getSelection().toHtml();
}



// returns the first range
function getFirstRange()
{
    var sel = rangy.getSelection();
    return sel.rangeCount ? sel.getRangeAt(0) : null;
}



// surround user selection
function surroundRange()
{
    var range = getFirstRange();
    if (range)
    {
        var el = document.createElement("span");
        el.style.backgroundColor = "#ffe5e5";
        el.style.border="1px solid #ff0000";
        el.style.color = "#ff0000";
        try
        {
            range.surroundContents(el);
        } 
        catch(ex)
        {
          alert("Invalid Selection");  
        }
    }
}



// validate the login form
function checkLoginForm(form)
{	
	if(form.username.value == '')
	{
		alert("You have to enter your username");
		form.username.focus();
		return false;
	}
	
	if(form.password.value == '')
	{
		alert("You have to enter your password");
		form.password.focus();
		return false;
	}
	
	return true;
}


// validate the new document form
function checkNewDocumentForm(form)
{
	if(form.name.value == '')
	{
		alert("You have to specify a name for this document");
		form.name.focus();
		return false;
	}
	
	if(form.name.value.length > 40)
	{
		alert("The length of your document's name should be less than 40 characters");
		form.name.focus();
		return false;
	}
	
	return true;
}



// validate the signup form
function CheckSignupForm(form)
{
	 var ck_name = /^[A-Za-z ]{2,20}$/;
	 var ck_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i 
	 var ck_username = /^[A-Za-z0-9]{4,20}$/;
	 var ck_password =  /^[A-Za-z0-9!@#$%^&*()_]{4,20}$/;
	
	 var fname = form.fname.value;
	 var email = form.email.value;
	 var username = form.username.value;
	 var password = form.password.value;
	 var errors = [];
	 
	 if (!ck_name.test(fname))
	 {
	 	errors[errors.length] = "Your first name has to be 2-20 characters long and has to contain English Alphabets only";
	 }
	 if (!ck_email.test(email))
	 {
	 	errors[errors.length] = "You must enter a valid email address";
	 }
	 if (!ck_username.test(username))
	 {
	 	errors[errors.length] = "Your username has to be 6-20 characters long and has to contain English Alphabets only";
	 }
	 if (!ck_password.test(password))
	 {
	 	errors[errors.length] = "You must enter a valid password that is between 6-20 characters long";
	 }
	 if (errors.length > 0)
	 {
	  	var msg = "In order to signup:";
	 	for (var i = 0; i<errors.length; i++)
	 	{
	  		msg += "\n\n" + errors[i];
	  	}
	  	alert(msg);
	  	return false;
	 }
	return true;	
}



// load document
function loadDocument(id)
{
	window.location = 'edit.php?id=' + id;
}


// update background
function updateBackground(type, elementName)
{
    var newImage;
    
    if(type == 'up')
    {
    	newImage = "url(images/li_background2.png)";
    }
    else
    {
    	newImage = "url(images/li_background.png)";
    }
    
    document.getElementById(elementName).style.backgroundImage = newImage;	
}




// delete document
function confirmDelete(id)
{
	if(confirm("This action is permanent, do you still want to continue?"))
	{
		window.location = 'do.php?action=delete&id=' + id;
	}
}


// load Step
function loadStep(url, stepID)
{
	if(stepID == '1')
	{
		if(confirm("If you step back, you will lose the current marks. Do you still want to proceed?"))
		{
			window.location = url;
		}
	}
	else
	{
		window.location = url;
	}
}