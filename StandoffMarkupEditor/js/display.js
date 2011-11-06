// Change the button when mouse over out
function updateButton(status, elementID)
{
	if(status == '1')
	{
		var thisSrc = document.getElementById(elementID).src;
		thisSrc = thisSrc.replace(".png", "_2.png");
		document.getElementById(elementID).src = thisSrc;		
	}
	else
	{
		var thisSrc = document.getElementById(elementID).src;
		thisSrc = thisSrc.replace("_2.png", ".png");
		document.getElementById(elementID).src = thisSrc;					
	}
}