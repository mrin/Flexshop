var iMinYear = 2007;  // пока не везде проставлено!
var iMaxYear = 2015;  // --||--

var stl = '<style type="text/css">';
stl += '.control        {font-size: 8px; height: 18px; padding: 0px; vertical-align: middle; font-family:Verdana;}';
stl += '.control_text   {font-size: 8px; height: 18px; padding: 0px; width: 35px; vertical-align: middle; margin-bottom: 1px;font-family:Verdana;}';
stl += '.control_button {font-size: 8px; height: 18px; padding: 1px; vertical-align: middle; margin-bottom: 1px;font-family:Verdana;}';
stl += 'tr.days td      {font-size: 8px; font-weight: bold; width: 20px; height: 14px; text-align: center; padding: 0px; cursor: default;font-family:Verdana;}';
stl += '.day_off_header {background-color: #F33; color: #FFF; border: 1px #F99 outset;font-family:Verdana;}';
stl += '.day_header     {background-color: #666; color: #FFF; border: 1px #CCC outset;font-family:Verdana;}';
stl += '.day_disabled   {background-color: buttonface; color: #999; border: 1px #B4B4B4 solid;font-family:Verdana;}';
stl += '.day            {background-color: buttonface; border: 1px #FFF outset;font-family:Verdana;}';
stl += '.day_selected   {background-color: #FFC; border: 1px #CCC inset;font-family:Verdana;}';
stl += '.day_off        {background-color: buttonface; color: #F00; border: 1px #FFF outset;font-family:Verdana;}';
stl += '.day_mouseover  {background-color: buttonface; color: #900; border: 1px #FFF outset;font-family:Verdana;}';
stl += '</style>';
document.write(stl);

var day;
var MyObj  = null;
var calDiv = document.createElement("div");

var html = '<div id=cal_body style="position: absolute; top: 0px; left: 0px; width: 180px; height: 160px; border: 1px solid threeddarkshadow; border-top: 1px solid buttonface; border-left: 1px solid buttonface">';
html += '<div style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: buttonface; border: 1px solid buttonshadow; border-top: 1px solid buttonhighlight; border-left: 1px solid white">';
html += '<div style="position: absolute; top: 5px; left: 0px; width: 176px">';
html += '<center><nobr><select id=month class=control onchange="displayCalendar(year.selectedIndex + 2007, this.selectedIndex);" style="width: 65px">';
for (var i = 0; i < 12; i++)
	html += '<option>' + aMonths[i] + '</option>';
html += '</select> ';
html += '<select id=year class=control onchange="displayCalendar(this.selectedIndex + 2007, month.selectedIndex);" style="width: 52px">';
for (var i = iMinYear; i <= iMaxYear; i++)
	html += '<option>' + i + '</option>';
html += '</select> ';
html += '<input id=more type=button class=control_button value=">>" onClick="setWindowsWidth();"> ';
html += '<input id=close type=button class=control_button value="X" onClick="closeCal();"></nobr><br>';

html += '<table id=cal_table border=0 cellspacing=1 style="margin-top: 2px; margin-bottom: 3px">';
html += '<tr class=days>';
for (var i = 0; i < 7; i++)
	html += '<td class=' + (i < 5 ? 'day_header' : 'day_off_header') + '>' + aDaysOfWeek[i] + '</td>';
html += '</tr>';
for (var i = 0; i < 6; i++)
	html += '<tr class=days><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
html += '</table>';

html += '<nobr><input type=button class=control_button value="<<" onClick="setYear(-1);"> ';
html += '<input type=button class=control_button value="<" onClick="setMonth(-1);"> ';
html += '<input type=button class=control_button value="Сегодня" onClick="setToday();"> ';
html += '<input type=button class=control_button value=">" onClick="setMonth(1);"> ';
html += '<input type=button class=control_button value=">>" onClick="setYear(1);"></nobr>';
html += '</center></div>';

html += '<div id=ext_counter style="position: absolute; visibility: hidden; left: 180px; top: 10px; width: 70px; height: 150px; z-index: 10"><center>';
html += '<nobr><b>день:</b></font><br><input type=button class=control_button value="&mdash;" onClick="countDate(-parseInt(text1.value));">';
html += ' <input type=text class=control_text value="" id=text1> ';
html += '<input type=button class=control_button value="+" onClick="countDate(parseInt(text1.value));"></nobr><br>';
html += '<nobr><b>месяц:</b></font><br><input type=button class=control_button value="&mdash;" onClick="setMonth(-parseInt(text2.value));">';
html += ' <input type=text class=control_text value="" id=text2> ';
html += '<input type=button class=control_button value="+" onClick="setMonth(parseInt(text2.value));"></nobr><br>';
html += '<nobr><b>год:</b></font><br><input type=button class=control_button value="&mdash;" onClick="setYear(-parseInt(text3.value));">';
html += ' <input type=text class=control_text value="" id=text3> ';
html += '<input type=button class=control_button value="+" onClick="setYear(parseInt(text3.value));"></nobr><br>';
html += '<br><input type=button class=control_button value="OK" onClick="eventHandlerDblClick();">';
html += '</center></div>';
html += '</div></div>';

function setWindowsWidth()
{
	oCalBody = document.getElementById('cal_body');
	oMore = document.getElementById('more');
	oExtCounter = document.getElementById('ext_counter');
	if (oCalBody.style.width == "180px")
	{
		oCalBody.style.width = "265px";
		oMore.value = "<<";
		oExtCounter.style.visibility = "visible";
	}
	else
	{
		oCalBody.style.width = "180px";
		oMore.value = ">>";
		oExtCounter.style.visibility = "hidden";
	}
}

// открывает календарь: x_l - координата отступа слева, x_t - координата отступа с верху (минимум 1, иначе глючит
// в эксплорере, obj - объект в котором хранится дата (тэг input, например)
function openCalendar(obj, x_l, x_t)
{
	if (!x_l)
		x_l = 0;
	if (!x_t)
		x_t = -50;
	MyObj = obj;
	calDiv.innerHTML = html;
	calDiv.style.position = "absolute";
	calDiv.style.zIndex = 301;
	calDiv.style.left = calcLeft(obj) - 3 + x_l;
	calDiv.style.top = calcTop(obj) + obj.offsetHeight - 3 + x_t;
	document.body.appendChild(calDiv);
	// считывание начальной даты
	if (obj.value != "")
	{
		var x_day   = parseInt(obj.value.substr(0,2));
		var x_month = parseInt(obj.value.substr(3,2));
		var x_year  = parseInt(obj.value.substr(6,4));
		if (x_day > 0 && x_day <= getDaysInMonth(x_month, x_year) && x_month > 0 && x_month < 13 && x_year > 1899 && x_year < 2100)
		{
			day = x_day;
			document.getElementById('month').selectedIndex = x_month - 1;
			document.getElementById('year').selectedIndex = x_year - 2007;
			displayCalendar(x_year, x_month - 1, x_day);
		}
		else
			setToday();
	}
	else
		setToday();
}

// выделяет день при наведении кусора
function eventHandlerOver(anEvObj)
{
	if (this.className != "day_selected")
		this.className = "day_mouseover";
}

// убирает выделение
function eventHandlerOut(anEvObj)
{
	if (this.className != "day_selected")
		(this.cellIndex < 5) ? this.className = "day" : this.className="day_off";
}

// устанавливает значение объекта переданного в функцию открытия календаря, в выбранное значение
function eventHandlerDblClick(anEvObj)
{
	var str_day   = day.toString();
	var str_month = document.getElementById('month').selectedIndex + 1;
	var str_year  = document.getElementById('year').selectedIndex + 2007;
	if (str_day.length == 1)
		str_day = "0" + str_day;
	if (str_month < 10)
		str_month = "0" + str_month;
	MyObj.value = (str_day + "." + str_month + "." + str_year);
	closeCal();
}

// закрывает календарь
function closeCal()
{
	document.body.removeChild(calDiv);
}

// обрабатывает клик на днях других месяцев
function eventHandlerClick(anEvObj)
{
	day = this.innerHTML;
	if (this.className == "day_disabled")
		(parseInt(day) > 20) ? setMonth(-1) : setMonth(1);
	else
		displayCalendar(document.getElementById('year').selectedIndex + 2007, document.getElementById('month').selectedIndex, day);
}

function setYear(val)
{
	if (!isNaN(val))
	{
		x_year = document.getElementById('year').selectedIndex + 2007;
		x_year = Number(x_year) + val;
		if (x_year < iMinYear)
			x_year = iMinYear;
		if (x_year > iMaxYear)
			x_year = iMaxYear;
		document.getElementById('year').selectedIndex = x_year - 2007;
		displayCalendar(document.getElementById('year').selectedIndex + 2007, document.getElementById('month').selectedIndex);
	}
}

function setMonth(val)
{
	if (!isNaN(val))
	{
		var x_month = document.getElementById('month').selectedIndex;
		var i = x_month + val;
		x_month = i % 12;
		if (x_month < 0)
			x_month = x_month + 12;
		document.getElementById('month').selectedIndex = x_month;
		setYear(Math.floor(i / 12));
		displayCalendar(document.getElementById('year').selectedIndex + 2007, document.getElementById('month').selectedIndex);
	}
}

function countDate(x_diff)
{
	if (!isNaN(x_diff))
	{
		curr_d = new Date(document.getElementById('year').selectedIndex + 2007, document.getElementById('month').selectedIndex, day);
		newDate = curr_d.getDate() + x_diff;
		curr_d.setDate(newDate);
		day = curr_d.getDate();
		document.getElementById('year').selectedIndex = GetAbsoluteYear(curr_d.getYear()) - 2007;
		document.getElementById('month').selectedIndex = curr_d.getMonth();
		displayCalendar(document.getElementById('year').selectedIndex + 2007, document.getElementById('month').selectedIndex, day);
	}
}

function setToday()
{
	var now = new Date();
	var x_day = now.getDate();
	var x_month = now.getMonth();
	var x_year = now.getYear();        // example 106 for 2006 in FF and Op, 2006 in IE
	x_year = GetAbsoluteYear(x_year);  // always 2006
	day = x_day;
	document.getElementById('month').selectedIndex = x_month;
	document.getElementById('year').selectedIndex = x_year - 2007;
	displayCalendar(x_year, x_month, x_day);
}

function GetAbsoluteYear(iYear)
{
	if (iYear < 1900)
		iYear += 1900;
	if (iYear > iMaxYear)
		iYear = iMaxYear;
	if (iYear < iMinYear)
		iYear = iMinYear;
	return iYear;
}

function getDaysInMonth(x_month, x_year)
{
	if (x_month == 1 || x_month == 3 || x_month == 5 || x_month == 7 || x_month == 8 || x_month == 10 || x_month == 12)
		return 31;
	if (x_month == 4 || x_month == 6 || x_month == 9 || x_month == 11)
		return 30;
	if (x_month == 2)
	{
		if (isLeapYear(x_year))
			return 29;
		else
			return 28;
	}
	return 0;
}

function isLeapYear(x_year)
{
	if (x_year % 4 == 0)
		return true;
	else
		return false;
}

function displayCalendar(x_year, x_month, x_day)
{
	x_day   = parseInt(x_day);
	x_month = parseInt(x_month);
	x_year  = parseInt(x_year);
	var days = getDaysInMonth(x_month + 1, x_year);
	if (x_month > 1)
		var days_before = getDaysInMonth(x_month, x_year);
	else
		var days_before = getDaysInMonth(12, x_year - 1);
	if (day > days)
		day = days;
	if (!x_day)
		x_day = day;
	var curr_day = 0;
	var firstOfMonth = new Date(x_year, x_month, 1);
	var startingPos = firstOfMonth.getDay();
	if (startingPos == 0)
		startingPos = 7;
	oCalTable = document.getElementById('cal_table');
	for (i = 0; i < 42; i++)
	{
		var rw = Math.floor(i / 7) + 1;
		var cl = i % 7;
		curr_day = i - startingPos + 2;
		if (oCalTable.rows[rw].cells[cl].cellIndex < 5)
			oCalTable.rows[rw].cells[cl].className = "day";
		else
			oCalTable.rows[rw].cells[cl].className = "day_off";
		if (curr_day <= 0)
		{
			oCalTable.rows[rw].cells[cl].innerHTML = curr_day + days_before;
			oCalTable.rows[rw].cells[cl].className = "day_disabled";
			oCalTable.rows[rw].cells[cl].onmouseover = "";
			oCalTable.rows[rw].cells[cl].onmouseout = "";
		}
		if (curr_day > 0 && curr_day <= days)
		{
			oCalTable.rows[rw].cells[cl].innerHTML = curr_day;
			oCalTable.rows[rw].cells[cl].onmouseover = eventHandlerOver;
			oCalTable.rows[rw].cells[cl].onmouseout = eventHandlerOut;
		}
		if (curr_day > days)
		{
			oCalTable.rows[rw].cells[cl].innerHTML = curr_day - days;
			oCalTable.rows[rw].cells[cl].className = "day_disabled"
			oCalTable.rows[rw].cells[cl].onmouseover = "";
			oCalTable.rows[rw].cells[cl].onmouseout = "";
		}
		if (curr_day == x_day)
			oCalTable.rows[rw].cells[cl].className = "day_selected";
		oCalTable.rows[rw].cells[cl].onmousedown = eventHandlerClick;
		oCalTable.rows[rw].cells[cl].onmouseup = eventHandlerDblClick;
	}
}

function calcTop(ele)
{
	var oParent = ele.offsetParent;
	if (oParent == null)
		return 0;
	return ele.offsetTop + (!isNaN(oParent.clientTop) ? oParent.clientTop : 0) + calcTop(oParent);
}                         // hack to fight mozilla bug

function calcLeft(ele)
{
	var oParent = ele.offsetParent;
	if (oParent == null)
		return 0;
	return ele.offsetLeft + (!isNaN(oParent.clientLeft) ? oParent.clientLeft : 0) + calcLeft(oParent);
}

function SetNextDateValue(tx)
{
	var date = new Date(tx.value);
	if (date == 'NaN')
	date  = new Date();
	date1 = new Date(date.getYear(), date.getMonth(), date.getDate() + 1);
	var day = date1.getDate();
	if (day < 10)
		day = '0' + day;
	var month = date1.getMonth() + 1;
	if (month < 10)
		month = '0' + month;
	var year = date1.getYear();
	tx.value = '' + day + '.' + month + '.' + year;
}

function SetPrevDateValue(tx)
{
	var date = new Date(tx.value);
	if (date == 'NaN')
		date = new Date();
	date1 = new Date(date.getYear(), date.getMonth(), date.getDate() - 1);
	var day = date1.getDate();
	if (day < 10)
		day = '0' + day;
	var month = date1.getMonth() + 1;
	if (month < 10)
		month = '0' + month;
	var year  = date1.getYear();
	tx.value = '' + day + '.' + month + '.' + year;
}