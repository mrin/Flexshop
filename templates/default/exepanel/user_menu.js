// Меню
var ie4, nn4, nn6;
var rX, lX, tY, bY;
var zi=100;
ie4 = nn4 = nn6 = 0;
if(document.all)
	{ie4=1; document.body.onmousemove=updateIt;}
if(document.layers)
	{nn4=1; window.captureEvents(Event.MOUSEMOVE); window.onmousemove=updateIt;}
if(document.getElementById&&!ie4)
	{nn6=1; document.body.onmousemove=updateIt;}


// Изменение фона по RGB
function colorbgchange(src, colr){
		src.style.backgroundColor=colr;
}
//Изменение фона через стиль
function colorstyle(src, style){
	src.className=style;
}

//Подтверждение действия
function submiturl(url,mes){
	if(confirm(mes)) window.location=url;
}


// вывод подсказки 
function show_help(hi){
	document.all(hi).style.top = window.event.clientY + document.body.scrollTop + 5;
	document.all(hi).style.left = window.event.clientX + document.body.scrollLeft + 5;
	document.all(hi).style.visibility="visible";
}
function show_help_other(hi){
	document.all(hi).style.top = window.event.clientY + document.body.scrollTop + 5;
	document.all(hi).style.left = window.event.clientX + document.body.scrollLeft - 25;
	document.all(hi).style.visibility="visible";
}
// скрытие подсказки
function hide_help(hi){
	document.all(hi).style.visibility="hidden";
}

// Открытие окна
function open_window(link,w,h)
	{
		var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=yes";
		newWin = window.open(link,'newWin',win);
	}

//select, unselect all checkbox	
function check_all(form, flag) {
	var obj = document.getElementById(form);
	var msg = document.getElementById('msgs');
	for(i=0;i<obj.elements.length;i++)
		if(obj.elements[i].type=='checkbox')
			if(flag) obj.elements[i].checked=true;
				else obj.elements[i].checked=false;
}

// Открытие модального окна
function popUp(theUrl, w, h)
{   	
		if(navigator.appName == 'Microsoft Internet Explorer')
			window.showModalDialog(theUrl,'','center=yes; scroll=yes; unadorned=yes; help=no; status=no; dialogWidth:50'); 
			else open_window(theUrl,w,h)
}

// Проверка на INT
function  check_int(summa){
	var LenValue = summa.toString().length;
	//if(LenValue == 0) return false;
	var LastSimvol;
	for(i=0; i<LenValue; i++){
		LastSimvol = summa.toString().substring(i, i+1);
		if(LastSimvol != '1' && LastSimvol != '2' && LastSimvol != '3' && LastSimvol != '4' && LastSimvol != '5'
		&& LastSimvol != '6' && LastSimvol != '7' && LastSimvol != '8' && LastSimvol != '9' && LastSimvol != '0'
		&& LastSimvol != '.' && LastSimvol != ','){
			return false;
		}
	}
	return true;
}

function loadDoc(sRequestUrl,method) {
    var req;
    var fileContent;
    //для IE/Windows ActiveX
    if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        req.open(method, sRequestUrl, false);
        req.onreadystatechange=function() {
                                    if (req.readyState == 4) {
                                        fileContent = req.responseText;
                                    }
                                }
        req.send(null);
    // Для других XMLHttpRequest object
    } else if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.open(method, sRequestUrl, false);
        req.send(null);
        fileContent = req.responseText;
    }
    return fileContent;
}