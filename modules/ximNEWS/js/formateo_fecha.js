/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */


function IsNumeric(valor) 
{ 
var log=valor.length; var sw="S"; 
for (x=0; x<log; x++) 
{ v1=valor.substr(x,1); 
v2 = parseInt(v1); 
//Checking if its a numeric value
if (isNaN(v2)) { sw= "N";} 
} 
if (sw=="S") {return true;} else {return false; } 
} 

function formateafecha(fecha, required)
{
	if(fecha == "undefined" && required ) {
		alert("Introduzca una fecha");
		return false;
	}
	
	var datetime = fecha.split(' ');
	
	var date = isValidDate(datetime[0]);
	if(date) {
		if(datetime.length>1 && datetime != "undefined") {
			var time = isValidTime(datetime[1]);
			if(!time) {
				return date[0]+"/"+date[1]+"/"+date["2"];
			}
			return date[0]+"/"+date[1]+"/"+date["2"]+" "+time[0]+":"+time[1]+":"+time[2];
		}
		return date[0]+"/"+date[1]+"/"+date["2"];
	}else {
		return false;
	}
}


/// FUNCIONTS TO CHECK DATES FROM internet.org
function isValidDate(dateStr)
{

	var datePat = /([0-9]{1,2})[-/]([0-9]{1,2})[-/]([0-9]{2,4})/;

	var matchArray = dateStr.match(datePat); // is the format ok?
	if (matchArray == null)
		{
		alert(dateStr + ", no es un formato de fecha válido.")
		return false;
		}


	day = parseInt(matchArray[1], 10);
	month = parseInt(matchArray[2], 10);
	year = parseInt(matchArray[3], 10);

	if (month < 1 || month > 12)
		{ // check month range
		alert("El mes debe estar entre 1 y 12. match="+matchArray[2]+'month '+month);
		return false;
		}

	if (day < 1 || day > 31)
		{
		alert("El dia debe estar entre 1 y 31.");
		return false;
		}

	if ((month==4 || month==6 || month==9 || month==11) && day==31)
		{
		alert("El mes "+month+" no tiene 31 dias. "+dateStr)
		return false;
		}
	if (month == 2)
		{ // check for february 29th
		var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
		if (day>29 || (day==29 && !isleap))
			{
			alert("Febrero en el año " + year + " no tiene " + day + " dias. "+dateStr);
			return false;
			}
		}
	a = new Array(day, month, year);

	return a;
}

function isValidTime(timeStr)
{
	var timePat = /([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/;
					
	var matchArray = timeStr.match(timePat);
	if (matchArray == null)
		{
		alert("Formato invalido de hora: "+timeStr);
		return false;
		}

	hour = parseInt(matchArray[1], 10);
	minute = parseInt(matchArray[2], 10);
	second = parseInt(matchArray[3], 10);

	if (second.length==0) {	second = null; } 


	if (hour < 0  || hour > 23)
		{
		alert(timeStr+" ,la hora debe estar entre 00 y 23");
		return false;
		}

	if (minute < 0 || minute > 59)
		{
		alert (timeStr+", los minutos deben estar entre 00 y 59.");
		return false;
		}

	if (second == null || second < 0 || second > 59)
		{
		alert (timeStr+", los segundos deben estar entre 00 y 59");
		return false;
		}

	a = new Array(hour, minute, second);
	return a;
}

function dateDiff(dateA, timeA, dateB, timeB)
{
	diff  = new Date();

	dateA = isValidDate(dateA);
	timeA = isValidTime(timeA);
	if (dateA.length && timeB.length)
		{
		date1 = new Date(dateA[2], dateA[0], dateA[1], timeA[0], timeA[1], timeA[2]);
		}
	else
		return false;

	dateB = isValidDate(dateB);
	timeB = isValidTime(timeB);
	//alert(dateB[0]+" "+dateB[1]+" "+dateB[2]);
	if (dateB.length && timeB.length)
		{
		date2 = new Date(dateB[2], dateB[0], dateB[1], timeB[0], timeB[1], timeB[2]);
		}
	else
		return false;

	diff.setTime(date2.getTime() - date1.getTime());

	timediff = diff.getTime();

	return (timediff/1000);
}
