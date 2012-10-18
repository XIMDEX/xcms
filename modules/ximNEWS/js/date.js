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


	var includeTime = false; // Change this flag into 'true' to include current time in date output
	var language = 'es';	// Default Language: en - english ; es - spanish; de - german
	var enablePast = 0;		// 0 - disabled ; 1 - enabled
	var fixedX = -1;		// x position (-1 if to appear below control)
	var fixedY = -1;		// y position (-1 if to appear below control)
	var startAt = 1;		// 0 - sunday ; 1 - monday
	var showWeekNumber = 0;	// 0 - don't show; 1 - show
	var showToday = 1;		// 0 - don't show; 1 - show
	var imgDir = '../modules/ximNEWS/images/';		// directory for images ... e.g. var imgDir="/img/"
	var dayName = '';

	var gotoString = {
		en : 'Go To Current Month',
		es : 'Ir al Mes Actual',
		de : 'Gehe zu aktuellem Monat'
		};
	var todayString = {
		en : 'Today is',
		es : 'Hoy es',
		de : 'Heute ist'
		};
	var weekString = {
		en : 'Wk',
		es : 'Sem',
		de : 'KW'
		};
	var scrollLeftMessage = {
		en : 'Click to scroll to previous month. Hold mouse button to scroll automatically.',
		es : 'Presione para pasar al mes anterior. Deje presionado para pasar varios meses.',
		de : 'Klicken um zum vorigen Monat zu gelangen. Gedrückt halten, um automatisch weiter zu scrollen.'
		};
	var scrollRightMessage = {
		en : 'Click to scroll to next month. Hold mouse button to scroll automatically.',
		es : 'Presione para pasar al siguiente mes. Deje presionado para pasar varios meses.',
		de : 'Klicken um zum nächsten Monat zu gelangen. Gedrückt halten, um automatisch weiter zu scrollen.'
		};
	var selectMonthMessage = {
		en : 'Click to select a month.',
		es : 'Presione para seleccionar un mes',
		de : 'Klicken um Monat auszuwählen'
		};
	var selectYearMessage = {
		en : 'Click to select a year.',
		es : 'Presione para seleccionar un año',
		de : 'Klicken um Jahr auszuwählen'
		};
	var selectDateMessage = {		// do not replace [date], it will be replaced by date.
		en : 'Select [date] as date.',
		es : 'Seleccione [date] como fecha',
		de : 'Wähle [date] als Datum.'
		};
	var	monthName = {
		en : new Array('January','February','March','April','May','June','July','August','September','October','November','December'),
		es : new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'),
		de : new Array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember')
		};
	var	monthName2 = {
		en : new Array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'),
		es : new Array('ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'),
		de : new Array('JAN','FEB','MRZ','APR','MAI','JUN','JUL','AUG','SEP','OKT','NOV','DEZ')
		};

	if (startAt==0)
		{
		dayName = {
			en : new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'),
			es : new Array('Dom','Lun','Mar','Mie','Jue','Vie','Sab'),
			de : new Array('So','Mo','Di','Mi','Do','Fr','Sa')
			};
		}
	else
		{
		dayName = {
			en : new Array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
			es : new Array('Lun','Mar','Mie','Jue','Vie','Sab','Dom'),
			de : new Array('Mo','Di','Mi','Do','Fr','Sa','So')
			};
		}
	var crossobj, crossMonthObj, crossYearObj, monthSelected, yearSelected, dateSelected, omonthSelected, oyearSelected, odateSelected, monthConstructed, yearConstructed, intervalID1, intervalID2, timeoutID1, timeoutID2, ctlToPlaceValue, ctlNow, dateFormat, nStartingYear, selDayAction, isPast;
	var visYear  = 0;
	var visMonth = 0;
	var bPageLoaded = false;
	var ie  = document.all;
	var dom = document.getElementById;
	var ns4 = document.layers;
	var today    = new Date();
	var dateNow  = today.getDate();
	var monthNow = today.getMonth();
	var yearNow  = today.getYear();
	var imgsrc   = new Array('drop1.gif','drop2.gif','left1.gif','left2.gif','right1.gif','right2.gif');
	var img      = new Array();
	var bShow    = false;
	/* hides <select> and <applet> objects (for IE only) */
	function hideElement( elmID, overDiv )
		{
		if(ie)
			{
			for(i = 0; i < document.all.tags( elmID ).length; i++)
				{
				obj = document.all.tags( elmID )[i];
				if(!obj || !obj.offsetParent)
					continue;

				// Find the element's offsetTop and offsetLeft relative to the BODY tag.
				objLeft   = obj.offsetLeft;
				objTop    = obj.offsetTop;
				objParent = obj.offsetParent;

				if(objParent != null) {
					while(objParent.tagName.toUpperCase() != 'BODY')
					{
						objLeft  += objParent.offsetLeft;
						objTop   += objParent.offsetTop;
						objParent = objParent.offsetParent;
						if(objParent == null) {
							break;
						}
					}
				}

				objHeight = obj.offsetHeight;
				objWidth  = obj.offsetWidth;

				if((overDiv.offsetLeft + overDiv.offsetWidth) <= objLeft)
					;
				else if((overDiv.offsetTop + overDiv.offsetHeight) <= objTop)
					;
				/* CHANGE by Charlie Roche for nested TDs*/
				else if(overDiv.offsetTop >= (objTop + objHeight + obj.height))
					;
				/* END CHANGE */
				else if(overDiv.offsetLeft >= (objLeft + objWidth))
					;
				else {
					obj.style.visibility = 'hidden';
				}
			}
		}
	}

	/*
	* unhides <select> and <applet> objects (for IE only)
	*/
	function showElement(elmID) {
		if(ie) {
			for(i = 0; i < document.all.tags( elmID ).length; i++) {
				obj = document.all.tags(elmID)[i];
				if(!obj || !obj.offsetParent) continue;
				obj.style.visibility = '';
			}
		}
	}

	function HolidayRec (d, m, y, desc) {
		this.d = d;
		this.m = m;
		this.y = y;
		this.desc = desc;
	}

	var HolidaysCounter = 0;
	var Holidays = new Array();

	function addHoliday (d, m, y, desc) {
		Holidays[HolidaysCounter++] = new HolidayRec (d, m, y, desc);
	}

	if (dom) {
		for	(i=0;i<imgsrc.length;i++) {
			img[i] = new Image;
			img[i].src = imgDir + imgsrc[i];
		}
		var str = '<div onclick="bShow=true" id="calendar" style="z-index:+999;position:absolute;visibility:hidden;"><table width="'+((showWeekNumber==1)?250:220)+'" style="font-family:Arial;font-size:11px;border: 1px solid #A0A0A0;" bgcolor="#ffffff"><tr bgcolor="#AB1683"><td><table width="'+((showWeekNumber==1)?248:218)+'"><tr><td style="padding:2px;font-family:Arial;font-size:11px;"><font color="#ffffff' + '' /*C9D3E9*/ +'"><b><span id="caption"></span></b></font></td><td align="right"><a href="#" onclick="hideCalendar(); return false;"><img src="'+imgDir+'close.gif" width="15" height="13" border="0" /></a></td></tr></table></td></tr><tr><td style="padding:5px" bgcolor="#ffffff"><span id="content"></span></td></tr>';
		
		if (showToday == 1) {
			str = str + '<tr bgcolor="#f0f0f0"><td style="padding:5px" align="center"><span id="lblToday"></span></td></tr>';
		}

		str = str + '</table></div><div id="selectMonth" style="z-index:+999;position:absolute;visibility:hidden;"></div><div id="selectYear" style="z-index:+999;position:absolute;visibility:hidden;"></div>';
		
		jQuery(str).appendTo('body');
	}

	var	styleAnchor = 'text-decoration:none;color:black;';
	var	styleLightBorder = 'border:1px solid #a0a0a0;';

	function swapImage(srcImg, destImg) {
		if (ie) document.getElementById(srcImg).setAttribute('src',imgDir + destImg);
	}

	function init() {
		if (!ns4)
		{
			
			if (!ie) yearNow += 1900;
			crossobj=(dom)?document.getElementById('calendar').style : ie? document.all.calendar : document.calendar;
			hideCalendar();

			crossMonthObj = (dom) ? document.getElementById('selectMonth').style : ie ? document.all.selectMonth : document.selectMonth;

			crossYearObj = (dom) ? document.getElementById('selectYear').style : ie ? document.all.selectYear : document.selectYear;

			monthConstructed = false;
			yearConstructed = false;

			if (showToday == 1) {
				document.getElementById('lblToday').innerHTML =	'<font color="#000066">' + todayString[language] + ' <a onmousemove="window.status=\''+gotoString[language]+'\'" onmouseout="window.status=\'\'" title="'+gotoString[language]+'" style="'+styleAnchor+'" href="javascript:monthSelected=monthNow;yearSelected=yearNow;constructCalendar();">'+dayName[language][(today.getDay()-startAt==-1)?6:(today.getDay()-startAt)]+', ' + dateNow + ' ' + monthName[language][monthNow].substring(0,3) + ' ' + yearNow + '</a></font>';
			}

			sHTML1 = '<span id="spanLeft" style="border:1px solid #FFFFFF;cursor:pointer" onmouseover="swapImage(\'changeLeft\',\'left2.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\''+scrollLeftMessage[language]+'\'" onclick="decMonth()" onmouseout="clearInterval(intervalID1);swapImage(\'changeLeft\',\'left1.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\'\'" onmousedown="clearTimeout(timeoutID1);timeoutID1=setTimeout(\'StartDecMonth()\',500)" onmouseup="clearTimeout(timeoutID1);clearInterval(intervalID1)">&nbsp<img id="changeLeft" src="'+imgDir+'left1.gif" width="10" height="11" border="0">&nbsp</span>&nbsp;';
			sHTML1 += '<span id="spanRight" style="border:1px solid #FFFFFF;cursor:pointer" onmouseover="swapImage(\'changeRight\',\'right2.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\''+scrollRightMessage[language]+'\'" onmouseout="clearInterval(intervalID1);swapImage(\'changeRight\',\'right1.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\'\'" onclick="incMonth()" onmousedown="clearTimeout(timeoutID1);timeoutID1=setTimeout(\'StartIncMonth()\',500)" onmouseup="clearTimeout(timeoutID1);clearInterval(intervalID1)">&nbsp<img id="changeRight" src="'+imgDir+'right1.gif" width="10" height="11" border="0">&nbsp</span>&nbsp;';
			sHTML1 += '<span id="spanMonth" style="border:1px solid #FFFFFF;cursor:pointer" onmouseover="swapImage(\'changeMonth\',\'drop2.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\''+selectMonthMessage[language]+'\'" onmouseout="swapImage(\'changeMonth\',\'drop1.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\'\'" onclick="popUpMonth()"></span>&nbsp;';
			sHTML1 += '<span id="spanYear" style="border:1px solid #FFFFFF;cursor:pointer" onmouseover="swapImage(\'changeYear\',\'drop2.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\''+selectYearMessage[language]+'\'" onmouseout="swapImage(\'changeYear\',\'drop1.gif\');this.style.borderColor=\'#FFFFFF\';window.status=\'\'" onclick="popUpYear()"></span>&nbsp;';

			document.getElementById('caption').innerHTML = sHTML1;

			bPageLoaded=true;
		}
	}

	function hideCalendar() {
		if (crossobj != undefined) {
			crossobj.visibility = 'hidden';
		}
		if (crossMonthObj != null) crossMonthObj.visibility = 'hidden';
		if (crossYearObj  != null) crossYearObj.visibility = 'hidden';
		showElement('SELECT');
		showElement('APPLET');
	}

	function padZero(num) {
		return (num	< 10) ? '0' + num : num;
	}

	function constructDate(d,m,y) {
		sTmp = dateFormat;
		sTmp = sTmp.replace ('dd','<e>');
		sTmp = sTmp.replace ('d','<d>');
		sTmp = sTmp.replace ('<e>',padZero(d));
		sTmp = sTmp.replace ('<d>',d);
		sTmp = sTmp.replace ('mmmm','<p>');
		sTmp = sTmp.replace ('mmm','<o>');
		sTmp = sTmp.replace ('mm','<n>');
		sTmp = sTmp.replace ('m','<m>');
		sTmp = sTmp.replace ('<m>',m+1);
		sTmp = sTmp.replace ('<n>',padZero(m+1));
		sTmp = sTmp.replace ('<o>',monthName[language][m]);
		sTmp = sTmp.replace ('<p>',monthName2[language][m]);
		sTmp = sTmp.replace ('yyyy',y);
		sTmp = sTmp.replace ('yy',padZero(y%100));

		if (includeTime == true) {
			var today = new Date();
			var time = " " + padZero(today.getHours()) + ":" + padZero(today.getMinutes()) + ":" + padZero(today.getSeconds());
			sTmp += time;
		}


		return sTmp;
	}

	function closeCalendar() {
		hideCalendar();
		ctlToPlaceValue.value = constructDate(dateSelected,monthSelected,yearSelected);
	}

	/*** Month Pulldown	***/
	function StartDecMonth() {
		intervalID1 = setInterval("decMonth()",80);
	}

	function StartIncMonth() {
		intervalID1 = setInterval("incMonth()",80);
	}

	function incMonth () {
		monthSelected++;
		if (monthSelected > 11) {
			monthSelected = 0;
			yearSelected++;
		}
		constructCalendar();
	}

	function decMonth () {
		monthSelected--;
		if (monthSelected < 0) {
			monthSelected = 11;
			yearSelected--;
		}
		constructCalendar();
	}

	function constructMonth() {
		popDownYear()
		if (!monthConstructed) {
			sHTML = "";
			for (i=0; i<12; i++) {
				sName = monthName[language][i];
				if (i == monthSelected){
					sName = '<b>' + sName + '</b>';
				}
				sHTML += '<tr><td id="m' + i + '" onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'" style="cursor:pointer" onclick="monthConstructed=false;monthSelected=' + i + ';constructCalendar();popDownMonth();event.cancelBubble=true"><font color="#000066">&nbsp;' + sName + '&nbsp;</font></td></tr>';
			}

			document.getElementById('selectMonth').innerHTML = '<table width="70" style="font-family:Arial;font-size:11px;border:1px solid #a0a0a0;" bgcolor="#f0f0f0" cellspacing="0" onmouseover="clearTimeout(timeoutID1)" onmouseout="clearTimeout(timeoutID1);timeoutID1=setTimeout(\'popDownMonth()\',100);event.cancelBubble=true">' + sHTML + '</table>';

			monthConstructed = true;
		}
	}

	function popUpMonth() {
		if (visMonth == 1) {
			popDownMonth();
			visMonth--;
		} else {
			constructMonth();
			crossMonthObj.visibility = (dom||ie) ? 'visible' : 'show';
			crossMonthObj.left = ( parseInt(crossobj.left) + 50 ) + "px";
			crossMonthObj.top =  ( parseInt(crossobj.top) + 26 ) + "px";
// 			hideElement('SELECT', document.getElementById('selectMonth'));
// 			hideElement('APPLET', document.getElementById('selectMonth'));
			visMonth++;
		}
	}

	function popDownMonth() {
		crossMonthObj.visibility = 'hidden';
		visMonth = 0;
	}

	/*** Year Pulldown ***/
	function incYear() {
		for	(i=0; i<7; i++) {
			newYear	= (i + nStartingYear) + 1;
			if (newYear == yearSelected)
				txtYear = '<span style="color:#006;font-weight:bold;">&nbsp;' + newYear + '&nbsp;</span>';
			else
				txtYear = '<span style="color:#006;">&nbsp;' + newYear + '&nbsp;</span>';
			document.getElementById('y'+i).innerHTML = txtYear;
		}
		nStartingYear++;
		bShow=true;
	}

	function decYear() {
		for	(i=0; i<7; i++) {
			newYear = (i + nStartingYear) - 1;
			if (newYear == yearSelected)
				txtYear = '<span style="color:#006;font-weight:bold">&nbsp;' + newYear + '&nbsp;</span>';
			else
				txtYear = '<span style="color:#006;">&nbsp;' + newYear + '&nbsp;</span>';
			document.getElementById('y'+i).innerHTML = txtYear;
		}
		nStartingYear--;
		bShow=true;
	}

	function selectYear(nYear) {
		yearSelected = parseInt(nYear + nStartingYear);
		yearConstructed = false;
		constructCalendar();
		popDownYear();
	}

	function constructYear() {
		popDownMonth();
		sHTML = '';
		if (!yearConstructed) {
			sHTML = '<tr><td align="center" onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="clearInterval(intervalID1);this.style.backgroundColor=\'\'" style="cursor:pointer" onmousedown="clearInterval(intervalID1);intervalID1=setInterval(\'decYear()\',30)" onmouseup="clearInterval(intervalID1)"><font color="#000066">-</font></td></tr>';

			j = 0;
			nStartingYear =	yearSelected - 3;
			for ( i = (yearSelected-3); i <= (yearSelected+3); i++ ) {
				sName = i;
				if (i == yearSelected) sName = '<b>' + sName + '</b>';
				sHTML += '<tr><td id="y' + j + '" onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'" style="cursor:pointer" onclick="selectYear('+j+');event.cancelBubble=true"><font color="#000066">&nbsp;' + sName + '&nbsp;</font></td></tr>';
				j++;
			}

			sHTML += '<tr><td align="center" onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="clearInterval(intervalID2);this.style.backgroundColor=\'\'" style="cursor:pointer" onmousedown="clearInterval(intervalID2);intervalID2=setInterval(\'incYear()\',30)" onmouseup="clearInterval(intervalID2)"><font color="#000066">+</font></td></tr>';

			document.getElementById('selectYear').innerHTML = '<table width="44" cellspacing="0" bgcolor="#f0f0f0" style="font-family:Arial;font-size:11px;border:1px solid #a0a0a0;" onmouseover="clearTimeout(timeoutID2)" onmouseout="clearTimeout(timeoutID2);timeoutID2=setTimeout(\'popDownYear()\',100)">' + sHTML + '</table>';

			yearConstructed = true;
		}
	}

	function popDownYear() {
		clearInterval(intervalID1);
		clearTimeout(timeoutID1);
		clearInterval(intervalID2);
		clearTimeout(timeoutID2);
		crossYearObj.visibility= 'hidden';
		visYear = 0;
	}

	function popUpYear() {
		var leftOffset
		if (visYear==1) {
			popDownYear();
			visYear--;
		} else {
			constructYear();
			crossYearObj.visibility	= (dom||ie) ? 'visible' : 'show';
			leftOffset = parseInt(crossobj.left) + document.getElementById('spanYear').offsetLeft;
			if (ie) leftOffset += 6;
			crossYearObj.left = leftOffset + "px";
			crossYearObj.top = ( parseInt(crossobj.top) + 26 ) + "px";
			visYear++;
		}
	}

	/*** calendar ***/
	function WeekNbr(n) {
		// Algorithm used:
		// From Klaus Tondering's Calendar document (The Authority/Guru)
		// http://www.tondering.dk/claus/calendar.html
		// a = (14-month) / 12
		// y = year + 4800 - a
		// m = month + 12a - 3
		// J = day + (153m + 2) / 5 + 365y + y / 4 - y / 100 + y / 400 - 32045
		// d4 = (J + 31741 - (J mod 7)) mod 146097 mod 36524 mod 1461
		// L = d4 / 1460
		// d1 = ((d4 - L) mod 365) + L
		// WeekNumber = d1 / 7 + 1

		year = n.getFullYear();
		month = n.getMonth() + 1;
		if (startAt == 0) {
			day = n.getDate() + 1;
		} else {
			day = n.getDate();
		}

		a = Math.floor((14-month) / 12);
		y = year + 4800 - a;
		m = month + 12 * a - 3;
		b = Math.floor(y/4) - Math.floor(y/100) + Math.floor(y/400);
		J = day + Math.floor((153 * m + 2) / 5) + 365 * y + b - 32045;
		d4 = (((J + 31741 - (J % 7)) % 146097) % 36524) % 1461;
		L = Math.floor(d4 / 1460);
		d1 = ((d4 - L) % 365) + L;
		week = Math.floor(d1/7) + 1;

		return week;
	}

	function constructCalendar () {
		var aNumDays = Array (31,0,31,30,31,30,31,31,30,31,30,31);
		var dateMessage;
		var startDate = new Date (yearSelected,monthSelected,1);
		var endDate;

		if (monthSelected==1) {
			endDate = new Date (yearSelected,monthSelected+1,1);
			endDate = new Date (endDate - (24*60*60*1000));
			numDaysInMonth = endDate.getDate();
		} else {
			numDaysInMonth = aNumDays[monthSelected];
		}

		datePointer = 0;
		dayPointer = startDate.getDay() - startAt;

		if (dayPointer<0) dayPointer = 6;

		sHTML = '<table border="0" style="font-family:verdana;font-size:10px;"><tr>';

		if (showWeekNumber == 1) {
			sHTML += '<td width="27"><b>' + weekString[language] + '</b></td><td width="1" rowspan="7" bgcolor="#d0d0d0" style="padding:0px"><img src="'+imgDir+'divider.gif" width="1"></td>';
		}

		for (i = 0; i<7; i++) {
			sHTML += '<td width="27" align="right"><b><font color="#000066">' + dayName[language][i] + '</font></b></td>';
		}

		sHTML += '</tr><tr>';

		if (showWeekNumber == 1) {
			sHTML += '<td align="right">' + WeekNbr(startDate) + '&nbsp;</td>';
		}

		for	( var i=1; i<=dayPointer;i++ ) {
			sHTML += '<td>&nbsp;</td>';
		}

		for	( datePointer=1; datePointer <= numDaysInMonth; datePointer++ ) {
			dayPointer++;
			sHTML += '<td align="right">';
			sStyle=styleAnchor;
			if ((datePointer == odateSelected) && (monthSelected == omonthSelected) && (yearSelected == oyearSelected))
			{ sStyle+=styleLightBorder }

			sHint = '';
			for (k = 0;k < HolidaysCounter; k++) {
				if ((parseInt(Holidays[k].d) == datePointer)&&(parseInt(Holidays[k].m) == (monthSelected+1))) {
					if ((parseInt(Holidays[k].y)==0)||((parseInt(Holidays[k].y)==yearSelected)&&(parseInt(Holidays[k].y)!=0))) {
						sStyle+= 'background-color:#fdd;';
						sHint += sHint=="" ? Holidays[k].desc : "\n"+Holidays[k].desc;
					}
				}
			}

			sHint = sHint.replace('/\"/g', '&quot;');

			dateMessage = 'onmousemove="window.status=\''+selectDateMessage[language].replace('[date]',constructDate(datePointer,monthSelected,yearSelected))+'\'" onmouseout="window.status=\'\'" ';


			//////////////////////////////////////////////
			//////////  Modifications PinoToy  //////////
			//////////////////////////////////////////////
			if (enablePast == 0 && ((yearSelected < yearNow) || (monthSelected < monthNow) && (yearSelected == yearNow) || (datePointer < dateNow) && (monthSelected == monthNow) && (yearSelected == yearNow))) {
				selDayAction = '';
				isPast = 1;
			} else {
				selDayAction = 'href="javascript:dateSelected=' + datePointer + ';closeCalendar();"';
				isPast = 0;
			}

			if ((datePointer == dateNow) && (monthSelected == monthNow) && (yearSelected == yearNow)) {	///// today
				sHTML += "<b><a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+"><font color=#ff0000>&nbsp;" + datePointer + "</font>&nbsp;</a></b>";
			} else if (dayPointer % 7 == (startAt * -1)+1) {									///// SI ES DOMINGO
				if (isPast==1)
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;</a>";
				else
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#54A6E2>" + datePointer + "</font>&nbsp;</a>";
			} else if ((dayPointer % 7 == (startAt * -1)+7 && startAt==1) || (dayPointer % 7 == startAt && startAt==0)) {	///// SI ES SABADO
				if (isPast==1)
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;</a>";
				else
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#54A6E2>" + datePointer + "</font>&nbsp;</a>";
			} else {																			///// CUALQUIER OTRO DIA
				if (isPast==1)
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#909090>" + datePointer + "</font>&nbsp;</a>";
				else
					sHTML += "<a "+dateMessage+" title=\"" + sHint + "\" style='"+sStyle+"' "+selDayAction+">&nbsp;<font color=#000066>" + datePointer + "</font>&nbsp;</a>";
			}

			sHTML += '';
			if ((dayPointer+startAt) % 7 == startAt) {
				sHTML += '</tr><tr>';
				if ((showWeekNumber == 1) && (datePointer < numDaysInMonth)) {
					sHTML += '<td align="right">' + (WeekNbr(new Date(yearSelected,monthSelected,datePointer+1))) + '&nbsp;</td>';
				}
			}
		}

		document.getElementById('content').innerHTML   = sHTML
		document.getElementById('spanMonth').innerHTML = '&nbsp;' +	monthName[language][monthSelected] + '&nbsp;<img id="changeMonth" src="'+imgDir+'drop1.gif" width="12" height="10" border="0">'
		document.getElementById('spanYear').innerHTML  = '&nbsp;' + yearSelected	+ '&nbsp;<img id="changeYear" src="'+imgDir+'drop1.gif" width="12" height="10" border="0">';
	}

	function showCalendar(ctl, ctl2, format, lang, past, fx, fy) {
		alert("se ha pulsado sobre showCalendar");
		if (lang != null && lang != '') language = lang;
		if (past != null) enablePast = past;
		else enablePast = 0;
		if (fx != null) fixedX = fx;
		else fixedX = -1;
		if (fy != null) fixedY = fy;
		else fixedY = -1;

		if (showToday == 1) {
			document.getElementById('lblToday').innerHTML = '<font color="#000066">' + todayString[language] + ' <a onmousemove="window.status=\''+gotoString[language]+'\'" onmouseout="window.status=\'\'" title="'+gotoString[language]+'" style="'+styleAnchor+'" href="javascript:monthSelected=monthNow;yearSelected=yearNow;constructCalendar();">'+dayName[language][(today.getDay()-startAt==-1)?6:(today.getDay()-startAt)]+', ' + dateNow + ' ' + monthName[language][monthNow].substring(0,3) + ' ' + yearNow + '</a></font>';
		}

		popUpCalendar(ctl, ctl2, format);
	}

	function popUpCalendar(ctl, ctl2, format) {
		var leftpos = 0;
		var toppos  = 0;
		if (bPageLoaded) {

			if (crossobj.visibility == 'hidden') {
				ctlToPlaceValue = ctl2;
				dateFormat = format;
				formatChar = ' ';
				aFormat = dateFormat.split(formatChar);
				if (aFormat.length < 3) {
					formatChar = '/';
					aFormat = dateFormat.split(formatChar);
					if (aFormat.length < 3) {
						formatChar = '.';
						aFormat = dateFormat.split(formatChar);
						if (aFormat.length < 3) {
							formatChar = '-';
							aFormat = dateFormat.split(formatChar);
							if (aFormat.length < 3) {
								formatChar = '';					// invalid date format

							}
						}
					}
				}

				tokensChanged = 0;
				if (formatChar != "") {
					aData =	ctl2.value.split(formatChar);			// use user's date

					for (i=0; i<3; i++) {
						if ((aFormat[i] == "d") || (aFormat[i] == "dd")) {
							dateSelected = parseInt(aData[i], 10);
							tokensChanged++;
						} else if ((aFormat[i] == "m") || (aFormat[i] == "mm")) {
							monthSelected = parseInt(aData[i], 10) - 1;
							tokensChanged++;
						} else if (aFormat[i] == "yyyy") {
							yearSelected = parseInt(aData[i], 10);
							tokensChanged++;
						} else if (aFormat[i] == "mmm") {
							for (j=0; j<12; j++) {
								if (aData[i] == monthName[language][j]) {
									monthSelected=j;
									tokensChanged++;
								}
							}
						} else if (aFormat[i] == "mmmm") {
							for (j=0; j<12; j++) {
								if (aData[i] == monthName2[language][j]) {
									monthSelected = j;
									tokensChanged++;
								}
							}
						}
					}
				}

				if ((tokensChanged != 3) || isNaN(dateSelected) || isNaN(monthSelected) || isNaN(yearSelected)) {
					dateSelected  = dateNow;
					monthSelected = monthNow;
					yearSelected  = yearNow;
				}

				var	dateSplit = ctl2.value.split(" ");
				if (dateSplit.length > 1 && checkValidTime(dateSplit[1])) {
					includeTime = true;
				} else {
					includeTime = false;
				}

				odateSelected  = dateSelected;
				omonthSelected = monthSelected;
				oyearSelected  = yearSelected;

				aTag = ctl;
				do {
					aTag     = aTag.offsetParent;
					if(aTag != null) {
						leftpos += aTag.offsetLeft;
						toppos  += aTag.offsetTop;
					}else {
						break;
					}
				} while (aTag.tagName != 'BODY');

				crossobj.left = (fixedX == -1) ? (ctl.offsetLeft + leftpos) +"px" : fixedX+"px";
				crossobj.top = (fixedY == -1) ? (ctl.offsetTop + toppos + ctl.offsetHeight + 2 ) +"px": fixedY+"px";
				constructCalendar (1, monthSelected, yearSelected);


			crossobj.visibility = (dom||ie) ? "visible" : "show";
// 					crossobj.display = "block";

// 				hideElement('SELECT', document.getElementById('calendar'));
// 				hideElement('APPLET', document.getElementById('calendar'));
// 				alert("prueba2332");
// 				return false;
				bShow = true;
			} else {
				hideCalendar();
				if (ctlNow!=ctl) popUpCalendar(ctl, ctl2, format);
			}
			ctlNow = ctl;
		}
	}

/*	document.onkeypress = function hidecal1 () {
		if (event.keyCode == 27) hideCalendar();
	}
*/
document.onkeypress = "hidecal1(e)";
	document.onclick = function hidecal2 () {
		if (!bShow) hideCalendar();
		bShow = false;
	}
	if(ie) {
		init();
	} else {
		window.onload = init;
	}
function hidecal1(e){

	if(ie) {
		if (event.keyCode == 27) hideCalendar();
	} else {
		if (e.keyCode == 27) hideCalendar();
	}
}

/// FUNCIONES DE COMPROBACION DE FECHAS DE internet.org
function isValidDate(dateStr)
	{
	var datePat = /^(\d{1,2})(\/)(\d{1,2})\2(\d{4})$/; // requires 4 digit year

	var matchArray = dateStr.match(datePat); // is the format ok?
	if (matchArray == null)
		{
		alert(dateStr + ", no es un formato de fecha válido.")
		return false;
		}

	month = matchArray[3]; // parse date into variables
	day = matchArray[1];
	year = matchArray[4];

	if (month < 1 || month > 12)
		{ // check month range
		alert("El mes debe estar entre 1 y 12. "+dateStr);
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
	a = new Array((month-1), day, year);

	return a;
	}

function isValidTime(timeStr)
	{
	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?$/;

	var matchArray = timeStr.match(timePat);
	if (matchArray == null)
		{
		alert("Formato invalido de hora: "+timeStr);
		return false;
		}

	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];

	if (second=="") { second = null; }


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

	if (second != null && (second < 0 || second > 59))
		{
		alert (timeStr+", los segundos deben estar entre 00 y 59");
		return false;
		}

	a = new Array(hour, minute, second);
	return a;
	}


function checkValidTime(timeStr)
	{
	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?$/;

	var matchArray = timeStr.match(timePat);
	if (matchArray == null)
		{
		return false;
		}

	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];

	if (second=="") { second = null; }


	if (hour < 0  || hour > 23)
		{
		return false;
		}

	if (minute < 0 || minute > 59)
		{
		return false;
		}

	if (second != null && (second < 0 || second > 59))
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

function isValidInput()
	{
	dateA = 'update';
	hourA = 'uphour';
	minA  = 'upmin';
	secsA = 'upsecs';
	dateB = 'downdate';
	hourB = 'downhour';
	minB  = 'downmin';
	secsB = 'downsecs';

	dateA =	document.getElementById(dateA).value;
	dateB =	document.getElementById(dateB).value;
	timeA = document.getElementById(hourA).value+":"+document.getElementById(minA).value+":"+document.getElementById(secsA).value;
	timeB = document.getElementById(hourB).value+":"+document.getElementById(minB).value+":"+document.getElementById(secsB).value;

	if(dateA == 'Ahora' && dateB == 'Sin Determinar')
		{
		return true;
		}
	else if(dateA == 'Ahora' && dateB != 'Sin Determinar')
		{
		if(isValidDate(dateB) && isValidTime(timeB))
			return true;
		else
			return false;
		}
	else if(dateB == 'Sin Determinar' && dateA != 'Ahora')
		{
		if(isValidDate(dateA) && isValidTime(timeA))
			return true;
		else
			return false;
		}
	else if(dateDiff(dateA, timeA, dateB, timeB) >= 60 && dateA != 'Ahora' && dateB != 'Sin Determinar')
		{
		return true;
		}
	else
		{
		alert('La fecha de baja debe ser al menos un minuto despues que la fecha de alta.');
		return false;
		}
	}

function PublicateNow()
	{
	dateA  = 'update';
	hourA  = 'uphour';
	minA   = 'upmin';
	secA   = 'upsecs';
	buttonA= 'upfix';

	date = document.getElementById(dateA).value;
	if(date == 'Ahora')
		{
		document.getElementById(buttonA).value	= 'Ahora';
		document.getElementById(dateA).value	= 'Click Aqui...';
		document.getElementById(hourA).value	= '00';
		document.getElementById(minA).value		= '00';
		document.getElementById(secA).value		= '00';

		document.getElementById(dateA).disabled	= false;
		document.getElementById(hourA).disabled	= false;
		document.getElementById(minA).disabled	= false;
		document.getElementById(secA).disabled	= false;
		}
	else
		{
		document.getElementById(buttonA).value	= 'Seleccionar Fecha';
		document.getElementById(dateA).value	= 'Ahora';
		document.getElementById(hourA).value	= '';
		document.getElementById(minA).value		= '';
		document.getElementById(secA).value		= '';

		document.getElementById(dateA).disabled	= true;
		document.getElementById(hourA).disabled	= true;
		document.getElementById(minA).disabled	= true;
		document.getElementById(secA).disabled	= true;
		}
	}

function PublicateNowMulti(sufix)
	{
	dateA  = 'update'+sufix;
	hourA  = 'uphour'+sufix;
	minA   = 'upmin'+sufix;
	secA   = 'upsecs'+sufix;
	buttonA= 'upfix'+sufix;

	date = document.getElementById(dateA).value;
	if(date == 'Ahora')
		{
		document.getElementById(buttonA).value	= 'Ahora';
		document.getElementById(dateA).value	= 'Click Aqui...';
		document.getElementById(hourA).value	= '00';
		document.getElementById(minA).value		= '00';
		document.getElementById(secA).value		= '00';

		document.getElementById(dateA).disabled	= false;
		document.getElementById(hourA).disabled	= false;
		document.getElementById(minA).disabled	= false;
		document.getElementById(secA).disabled	= false;
		}
	else
		{
		document.getElementById(buttonA).value	= 'Seleccionar Fecha';
		document.getElementById(dateA).value	= 'Ahora';
		document.getElementById(hourA).value	= '';
		document.getElementById(minA).value		= '';
		document.getElementById(secA).value		= '';

		document.getElementById(dateA).disabled	= true;
		document.getElementById(hourA).disabled	= true;
		document.getElementById(minA).disabled	= true;
		document.getElementById(secA).disabled	= true;
		}
	}


function PublicateWithoutLimit()
	{
	dateB = 'downdate';
	hourB = 'downhour';
	minB  = 'downmin';
	secB  = 'downsecs';
	buttonB='downfix';

	date = document.getElementById(dateB).value;
	if(date == 'Sin Determinar')
		{
		document.getElementById(buttonB).value	= 'Sin Determinar';
		document.getElementById(dateB).value	= 'Click Aqui...';
		document.getElementById(hourB).value	= '00';
		document.getElementById(minB).value		= '00';
		document.getElementById(secB).value		= '00';

		document.getElementById(dateB).disabled	= false;
		document.getElementById(hourB).disabled	= false;
		document.getElementById(minB).disabled	= false;
		document.getElementById(secB).disabled	= false;
		}
	else
		{
		document.getElementById(buttonB).value	= 'Seleccionar Fecha';
		document.getElementById(dateB).value	= 'Sin Determinar';
		document.getElementById(hourB).value	= '';
		document.getElementById(minB).value		= '';
		document.getElementById(secB).value		= '';

		document.getElementById(dateB).disabled	= true;
		document.getElementById(hourB).disabled	= true;
		document.getElementById(minB).disabled	= true;
		document.getElementById(secB).disabled	= true;
		}
	}

function PublicateWithoutLimitMulti(sufix) {
	dateB = 'downdate' + sufix;
	hourB = 'downhour' + sufix;
	minB  = 'downmin' + sufix;
	secB  = 'downsecs' + sufix;
	buttonB='downfix' + sufix;

	date = document.getElementById(dateB).value;
	if(date == 'Sin Determinar') {
		document.getElementById(buttonB).value	= 'Sin Determinar';
		document.getElementById(dateB).value	= 'Click Aqui...';
		document.getElementById(hourB).value	= '00';
		document.getElementById(minB).value		= '00';
		document.getElementById(secB).value		= '00';

		document.getElementById(dateB).disabled	= false;
		document.getElementById(hourB).disabled	= false;
		document.getElementById(minB).disabled	= false;
		document.getElementById(secB).disabled	= false;
		}
	else {
		document.getElementById(buttonB).value	= 'Seleccionar Fecha';
		document.getElementById(dateB).value	= 'Sin Determinar';
		document.getElementById(hourB).value	= '00';
		document.getElementById(minB).value		= '00';
		document.getElementById(secB).value		= '00';

		document.getElementById(dateB).disabled	= true;
		document.getElementById(hourB).disabled	= true;
		document.getElementById(minB).disabled	= true;
		document.getElementById(secB).disabled	= true;
	}
}

function setDateUp(dateString)
	{
	dateB = 'downdate';
	hourB = 'downhour';
	minB  = 'downmin';
	secB  = 'downsecs';
	buttonB='downfix';

	dateA  = 'update';
	hourA  = 'uphour';
	minA   = 'upmin';
	secA   = 'upsecs';
	buttonA= 'upfix';

	date = dateString.split('-');
	up = date[0];
	down = date[1];

	if(up)
		{
		up = up.split(' ');
		dateUp1= up[0];
		dateUp = isValidDate(up[0]);
		timeUp = isValidTime(up[1]);

		if(dateUp.length && timeUp.length)
			{
			document.getElementById(buttonA).value	= 'Ahora';
			document.getElementById(dateA).value	= dateUp1;
			document.getElementById(hourA).value	= timeUp[0];
			document.getElementById(minA).value		= timeUp[1];
		//	document.getElementById(secA).value		= timeUp[2];

			document.getElementById(dateA).disabled	= false;
			document.getElementById(hourA).disabled	= false;
			document.getElementById(minA).disabled	= false;
		//	document.getElementById(secA).disabled	= false;
			}
		}

	if(down)
		{
		down = down.split(' ');
		dateDown1= down[0];
		dateDown = isValidDate(down[0]);
		timeDown = isValidTime(down[1]);

		if(dateDown.length && timeDown.length)
			{
			document.getElementById(buttonB).value	= 'Sin Determinar';
			document.getElementById(dateB).value	= dateDown1;
			document.getElementById(hourB).value	= timeDown[0];
			document.getElementById(minB).value		= timeDown[1];
		//	document.getElementById(secB).value		= timeDown[2];

			document.getElementById(dateB).disabled	= false;
			document.getElementById(hourB).disabled	= false;
			document.getElementById(minB).disabled	= false;
		//	document.getElementById(secB).disabled	= false;
			}
		}
	else
		{
		document.getElementById(buttonB).value	= 'Seleccionar Fecha';
		document.getElementById(dateB).value	= 'Sin Determinar';
		document.getElementById(hourB).value	= '';
		document.getElementById(minB).value		= '';
	//	document.getElementById(secB).value		= '';

		document.getElementById(dateB).disabled	= true;
		document.getElementById(hourB).disabled	= true;
		document.getElementById(minB).disabled	= true;
	//	document.getElementById(secB).disabled	= true;
		}

	}
