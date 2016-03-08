var isExpLocked = true;

//called when the page is loaded
function intilializeVariables()
{
	document.getElementById("exp_value").disabled = true;
	isExpLocked = true;
}

function toggleManualEdit()
{
	isExpLocked = !isExpLocked;
	document.getElementById("exp_value").disabled = isExpLocked;
	//calcEXP(); //update box
}

function enableEXPBoxBeforeSubmit()
{
	if (isExpLocked)
	{
		isExpLocked = false;
		document.getElementById("exp_value").disabled = false;
	}
}

function calcEXP()
{
	if (isExpLocked)
	{
		//document.getElementsByName("level_number")[0]
		var currLevel = parseInt(document.getElementById("level_number").value);
		var currRole = document.getElementById("role").value;
		
		//alert(currRole);
		
		//Simple switch statement. The formula is...irregular...ug...
		var exp = 0;
		switch(currLevel)
		{
			case 1: exp = 100; break;
			case 2: exp = 125; break;
			case 3: exp = 150; break;
			case 4: exp = 175; break;
			case 5: exp = 200; break;
			case 6: exp = 250; break;
			case 7: exp = 300; break;
			case 8: exp = 350; break;
			case 9: exp = 400; break;
			case 10: exp = 500; break;
			case 11: exp = 600; break;
			case 12: exp = 700; break;
			case 13: exp = 800; break;
			case 14: exp = 1000; break;
			case 15: exp = 1200; break;
			case 16: exp = 1400; break;
			case 17: exp = 1600; break;
			case 18: exp = 2000; break;
			case 19: exp = 2400; break;
			case 20: exp = 2800; break;
			case 21: exp = 3200; break;
			case 22: exp = 4150; break;
			case 23: exp = 5100; break;
			case 24: exp = 6050; break;
			case 25: exp = 7000; break;
			case 26: exp = 9000; break;
			case 27: exp = 11000; break;
			case 28: exp = 13000; break;
			case 29: exp = 15000; break;
			case 30: exp = 19000; break;
			case 31: exp = 23000; break;
			case 32: exp = 27000; break;
			case 33: exp = 31000; break;
			case 34: exp = 39000; break;
			case 35: exp = 47000; break;
			case 36: exp = 55000; break;
			case 37: exp = 63000; break;
			case 38: exp = 79000; break;
			case 39: exp = 95000; break;
			case 40: exp = 111000; break;
			default: exp = 0;
		}

		//note: these three things should be constrained to be mutually excusive
		//I could make it so when any is selected, the other are "grey'd out"
		if (currRole == "Minion")
			exp = Math.round(exp / 4);

		if (document.getElementById("elite_flag").checked)
			exp = exp * 2;

		if (document.getElementById("solo_flag").checked)
			exp = exp * 5;

		//set exp
		document.getElementById("exp_value").value = exp;
	}
}

window.onload = intilializeVariables; //set up functions, executes after the page loads