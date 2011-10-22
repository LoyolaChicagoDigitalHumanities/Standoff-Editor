$(document).ready(function()
{
	// Never cache Ajax requests
    $.ajaxSetup ({  
        cache: false  
    });  


	// On load, Set focus on the text area 
	document.getElementById('userInput').focus();

	    
    // Loading image
    var loading_image = "<div class='loading'><img src='images/loading.gif' border='0' alt='loading…'></div>";
    
    
	
	
	// show all marks in marks area
	$("#marks").ready(function()
	{
	    $("#marks")  
	        .html(loading_image)  
	        .load('do.php?action=getMarks', null, function(responseText){  
	     });	  	     
	}); 
	
	
	
	// show all marks in marks area
	$("#userInput").ready(function()
	{
	    $("#userInput")  
	        .html(loading_image)  
	        .load('do.php?action=getContent', null, function(responseText){  
	     });	  	     
	}); 
 	
 	
 	
 	
	// event when the + button is clicked
	$("#markButton").click(function()
	{
		var ns   = document.getElementById("ns").value;
		var tag  = document.getElementById("tag").value;
		var attr = document.getElementById("attr").value;
	    var sp   = document.getElementById('xxSP').value;
	    var ep   = document.getElementById('xxEP').value;
	    var val  = document.getElementById('xxVAL').value;
	    var userInput  = document.getElementById('userInput').value;	

		
		if((ns != "")&&(tag != "")&&(attr != ""))
		{	
		    var url  = "do.php?action=addMark&sp=" + sp + "&ep=" + ep + "&ns=" + ns + "&tag=" + tag + "&attr=" + attr;
	
		    
		    $("#marks")  
		        .html(loading_image)  
		        .load(url, null, function(responseText){  
		     });
		     	
	

			$.post("do.php?action=process", { data: userInput}, function(data){
			   document.getElementById('userInput').innerHTML = data;
			 });
	
	
		    
			document.getElementById("ns").value = '';
			document.getElementById("tag").value = '';
			document.getElementById("attr").value = '';	    
		    
		    document.getElementById('userInput').focus();		
	
		}
		$("#menu").hide("fast");	  	     
	}); 
	 	
 	
 	
	// event when the clear marks button is clicked
	$("#clearMarksButton").click(function()
	{
	    $("#marks")  
	        .html(loading_image)  
	        .load('do.php?action=clearMarks', null, function(responseText){  
	     });	  	     
	}); 


});



function addMark()
{	
	var sel, range, node, perm, html, sp, ep, le;		
	
	if(window.getSelection)
	{
	    sel = window.getSelection();
	    le = sel.toString().length;
				    
	    html = "<span>" + sel.toString() + "</span>";
	
	    if (sel.getRangeAt && sel.rangeCount)
	    {
	        range = window.getSelection().getRangeAt(0);
			sp = range.startOffset;
			ep = sp + le;		        
	        //range.deleteContents(); 
	        
	        if (range.createContextualFragment)
	        {
	            node = range.createContextualFragment(html);
	        }
	        else
	        {
	            var div = document.createElement("div"), child;
	            div.innerHTML = html;
	            node = document.createDocumentFragment();
	            while ( (child = div.firstChild) )
	            {
	                node.appendChild(child);
	            }
	        }
	        //range.insertNode(node);
	        document.getElementById('xxSP').value = sp;
	        document.getElementById('xxEP').value = ep;
	        document.getElementById('xxVAL').value = node;
	    }
	}
	else if (document.selection && document.selection.createRange)
	{
        html = "<span>" + "???" + "</span>"; // replace ??? with current selection
        range = document.selection.createRange();
        //range.pasteHTML(html);
        document.getElementById('xxSP').value = sp;
        document.getElementById('xxEP').value = ep;
        document.getElementById('xxVAL').value = node;        
	}
			
		
		
	if( $("#menu").is(":visible") )
	{
		$("#menu").hide("fast");	
	}
	else
	{
		$("#menu").show("fast");
	}	
}