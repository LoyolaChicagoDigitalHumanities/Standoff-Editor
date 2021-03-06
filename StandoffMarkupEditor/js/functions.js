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
 *			  TO Do: convert functions from classic JS to jQuery          *
 *                                                                        *
 *************************************************************************/




$(document).ready(function()
{
	// Never cache Ajax requests
    $.ajaxSetup ({  
        cache: false  
    });  




	// On load, Set focus on the text area 
	$('#userInput').focus();


	 
	// get file content
	$("#uploadTextFileButton").click(function()	
	{
		
		if($("#uploadFile").val())
		{
		
			var ext = $('#uploadFile').val().split('.').pop().toLowerCase();
			
			//if($.inArray(ext, ['txt', 'rtf']) == -1)
			if($.inArray(ext, ['txt']) == -1)
			{
			    alert('Invalid file type. You can only upload .txt files');
			}
			else
			{
				var thisAction = $("#stepHolderID").attr("action");
				thisAction = thisAction.replace("processStep1", "uploadFile");
				$("#stepHolderID").attr("action", thisAction);
				$("#stepHolderID").submit();
			}
		}
		else
		{
			alert("You have not chosen a file");
		}
	});
		 
		 
		 
	  
    // needed variable(s)
    var savedSel;
    var loading_image = "<div class='loading'><img src='images/loading.gif' border='0' alt='loading…'></div>";

	
	
	
	
	// get all marks of this document
	$("#marksSideMenu").ready(function()
	{
	    $("#marksSideMenu")  
	        .html(loading_image)  
	        .load('do.php?action=getAllMarks', null, function(responseText){  
	     });	     	  	     
	});
	
	
	
	
	// get the marked text of this document
	$("#userInput").ready(function()
	{
	    $("#userInput")  
	        .html(loading_image)  
	        .load('do.php?action=getMarkedContent', null, function(responseText){  
	     });	     	  	     
	});	 
 	
 	
 	 	
	// event when mouse is out of user input area 	
	$("#userInput").mouseout(function()
	{
		savedSel = rangy.saveSelection();
	});

	

	
	
	 	
	// event when the + button is clicked
	$("#markButton").click(function()
	{		
		var ns = $("#ns").val();
		var tag = $("#tag").val();
		var totalAttr = $("#newAttrCounter").val();
		var url = $("#url").val();
		var txt = $("#text").val();		

		rangy.restoreSelection(savedSel);
				
		if((ns != '')&&(tag != '')&&(reportSelectionText() != ''))
		{						 						
			var newSpanID = parseInt($("#spanIDHistory").val())+1;
			$("#spanIDHistory").val(newSpanID);
			
			var selectedText = rangy.getSelection().toString();
			
			surroundRange(newSpanID);
			
			$("#ns").val("");
			$("#tag").val("");
			$("#url").val("");
			$("#text").val("");
					
			 
			$('#userInput').focus();
						
			$('span[id^="junk"]').remove();
			
			var userInput = $('#userInput').html();
			var selectionTextLength = selectedText.length;
			var sp = getStartPoint(newSpanID, userInput);
			var ep = sp + selectionTextLength - 1; 
			var	backupContent = userInput;		
			
			 					 			
			$.post("do.php?action=addMark", {ns:ns, tag:tag, url:url, txt:txt, userInput:userInput, newSpanID:newSpanID, selectedText:selectedText, sp:sp, ep:ep}, function(data){
			    $("#marksSideMenu")  
			        .html(loading_image)  
			        .load('do.php?action=getAllMarks', null, function(responseText){  
			     });			
			});
			
			var totalAttributes = parseInt($('#newAttrCounter').val());
			var counter = 0;
			var tempElementID = "";
			while(counter < totalAttributes)
			{
				counter++;
				
				tempElementID = "attr" + counter;
				var valueOfTemp = $("#" + tempElementID).val();
				
				if(valueOfTemp != '')
				{
					$("#" + tempElementID).val() = "";
					$.post("do.php?action=linkAttribute", {attrValue:valueOfTemp, spanID:newSpanID}, function(data){}); 
				}
			}		
			
			rangy.removeMarkers(savedSel);						 			
		}
		else
		{
			if(ns == '')
			{
				alert("You have to fill the name space field");
				$('#ns').focus();
			}
			else if(tag == '')
			{
				alert("You have to fill the tag field");
				$('#tag').focus();
			}
			else
			{
				alert("No selection was made");
				$('#userInput').focus();
			}									
		} 	     
	}); 
		
 	
	// event when the clear marks button is clicked
	$("#clearMarksButton").click(function()
	{
	    $("#userInput")  
	        .html(loading_image)  
	        .load('do.php?action=clearMarks', null, function(responseText){  
	     }); 
	     
	    $("#marksSideMenu")  
	        .html(loading_image)  
	        .load('do.php?action=getAllMarks', null, function(responseText){  
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
function surroundRange(spanID)
{
    var range = getFirstRange();
    if (range)
    {
        var el = document.createElement("span");
        el.id = spanID;
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
	 	errors[errors.length] = "Your name has to be 2-20 characters long and has to contain English Alphabets only";
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





// validate the account form
function CheckAccountForm(form)
{
	 var ck_name = /^[A-Za-z ]{2,20}$/;
	 var ck_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i 
	 var ck_username = /^[A-Za-z0-9]{4,20}$/;
	 var ck_password =  /^[A-Za-z0-9!@#$%^&*()_]{4,20}$/;
	
	 var fname = form.fname.value;
	 var email = form.email.value;
	 var username = form.username.value;
	 var password = form.password.value;
	 var userType = document.getElementById('userType').value;
	 var errors = [];

	 if(!ck_name.test(fname))
	 {
	 	errors[errors.length] = "Your name has to be 2-20 characters long and has to contain English Alphabets only";
	 }
	 if(!ck_email.test(email))
	 {
	 	errors[errors.length] = "You must enter a valid email address";
	 }
	 if((!ck_username.test(username))&&(userType == 'citizen'))
	 {
	 	errors[errors.length] = "Your username has to be 6-20 characters long and has to contain English Alphabets only";
	 }
	 if((!ck_password.test(password))&&(password != '')&&(userType == 'citizen'))
	 {
	 	errors[errors.length] = "You must enter a valid password that is between 6-20 characters long. Leave the password field empty if you do not wish to change your password";
	 }	 
	 if(errors.length > 0)
	 {
	  	var msg = "Error:";
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


// update background image of the given element
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


// change color of the given element to high
function colorUp(eID)
{
	document.getElementById(eID).style.backgroundColor = '#b2d7e2';
}


// change color of the given element to low
function colorDown(eID)
{
	document.getElementById(eID).style.backgroundColor = '#c9e5ed';
}


// Update transparency of the given element
function updateTransparency(type, elementID)
{	 
	 var opacityValue;
	 
	 if(type == "low")
	 {
	 	opacityValue = 7;
	 }
	 else
	 {
	 	opacityValue = 10;
	 }
	 
	 document.getElementById(elementID).style.opacity = opacityValue/10;
	 document.getElementById(elementID).style.filter = 'alpha(opacity=' + opacityValue*10 + ')';
}


// disables the return key
function stopRKey(evt)
{
	var evt  = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=="text")) { return false; }
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
	window.location = url;
}



// clear user input for suggestion
function clearSuggestionField()
{
	document.getElementById('suggestionsInput').value = "";
	document.getElementById('suggestionsInput').style.color = "#000";	
}


// show suggestions
function showSuggestions()
{
	if(document.getElementById('suggestionsInput').value != "")
	{
		var userInput = document.getElementById('suggestionsInput').value;
		$.post("do.php?action=showSuggestions", {userInput:userInput}, function(data){
			document.getElementById('suggestionsContent').innerHTML = data;
		});
		
		
		$('#suggestionsBox').fadeIn('slow', function() {});
	}
	else
	{
		$('#suggestionsBox').fadeOut('slow', function() {});
	}
}


// hide suggestions
function hideSuggestions()
{
	$('#suggestionsBox').fadeOut('slow', function() {});
	document.getElementById('suggestionsInput').value = "Enter User Name";
	document.getElementById('suggestionsInput').style.color = "#797878";
}




// go to the given url
function goToURL(url)
{
	window.location = url;
}



// get the start point of the given selection using spanID
function getStartPoint(spanID, originalText)
{
	var toFind = '<span id="' + spanID;
	var index = originalText.indexOf(toFind);
	var subStringOfOriginal = originalText.substring(0, index);
  	
  	subStringOfOriginal = subStringOfOriginal.replace(/<\/span>/g, '');
  	subStringOfOriginal = subStringOfOriginal.replace(/<span[^\>]+\>/g, '');
  	
  	var sp = subStringOfOriginal.length;

	return sp;
}



// remove unwanted spans
function unwrapSpanTag(s)
{

  var a = document.createElement('div');
  a.innerHTML = s;
  var span, spans = a.getElementsByTagName('span');
  var frag, arr = [];

  // Stabilise spans collection in array
  for (var i=0, iLen=spans.length; i<iLen; i++) {
    arr[i] = spans[i];
  }

  // Process spans
  for (i=0; i<iLen; i++) {
    span = arr[i];

    // If no id, put content into a fragment
    if (!span.id) {

      // Some older IEs may not like createDocumentFragment
      frag = document.createDocumentFragment();

      while (span.firstChild) {
        frag.appendChild(span.firstChild);
      }

      // Replace span with its content in the fragment
      span.parentNode.replaceChild(frag, span);
    }
  }
  return a.innerHTML;
  
}



// remove the given mark and reloade document content
function removeMark(thisID, thisSpanID)
{		
	document.getElementById(thisSpanID).removeAttribute("style");
	document.getElementById(thisSpanID).removeAttribute("id");
	
	var dirtyText = document.getElementById('userInput').innerHTML;
	var cleanText = unwrapSpanTag(dirtyText);
		
	document.getElementById('userInput').innerHTML = cleanText;
		
	makeAJAXrequest("marksSideMenu", "do.php?action=removeMark&id=" + thisID);

	$.post("do.php?action=setMarkedContent", {cleanText:cleanText}, function(data){});    	  
}



// make an Ajax GET request using the given url to update the given div element
function makeAJAXrequest(elementID, requestURL)
{
	if (window.XMLHttpRequest)
	{
	  	// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	}
	else
	{
	  	// code for IE6, IE5
	  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	xmlhttp.onreadystatechange=function()
	{
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
	    	document.getElementById(elementID).innerHTML = xmlhttp.responseText;
	    }
	}

    xmlhttp.open("GET", requestURL, true);
    xmlhttp.setRequestHeader('X-Sent-From','StandoffMarkupEditor');
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlhttp.setRequestHeader("Connection", "close");		
    xmlhttp.send(); 
}



// add new attribute field 
function addNewAttr()
{
	var newCount = parseInt(document.getElementById('newAttrCounter').value)+1;
	
	var newElementContent = "<div class='attributeFieldContainer'><input name='attr" + newCount + "' id='attr" + newCount + "' class='classicField'></div><div class='attributeAddContainer'><img src='images/button_add_attr.png' class='addNewAttrButton' onclick='addNewAttr();'></div>";
	
	var newElement = document.createElement('div');
	newElement.innerHTML = newElementContent;	
	
	document.getElementById('attributesHolder').appendChild(newElement); 		
	document.getElementById('newAttrCounter').value = newCount;
}