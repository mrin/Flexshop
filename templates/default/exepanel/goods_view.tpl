<script src="{URLSITE}/templates/{TEMPLDEF}/exepanel/JsHttpRequest.js"></script>
<script language="JavaScript">
var curtype = '{CURTYPE}';
var title='';
var articul='';
var price='';
var goodid='';
var gooddelid='';
// submit form
function submitform(msg) {
	if(confirm(msg)) {
		doLoad('3','', document.getElementById('editgood'), true);
	}
}

//MSG of delete
function del_box(id, nameidood, nameblock) {
	gooddelid = id;
	document.getElementById(nameidood).innerHTML=gooddelid;
	var box = document.getElementById(nameblock);
	leftPosition = (screen.width-box.clientWidth)/2;
	topPosition = (screen.height+document.body.scrollTop - box.clientHeight/2)/2;
	with(box.style) {
		top = topPosition;
		left = leftPosition;
		visibility = 'visible';
	}
}
//ADD form
function update_box(nameblock) {
	document.getElementById('errorupdate').innerHTML = '';
	var box = document.getElementById(nameblock);
	leftPosition = (screen.width-box.clientWidth)/2;
	topPosition = (screen.height+document.body.scrollTop - box.clientHeight/2)/2;
	with(box.style) {
		top = topPosition;
		left = leftPosition;
		visibility = 'visible';
	}
}

function maxi(id) {
	var price_good = document.all['price_good'];
	var skidka_good = document.all['skidka_good'];
	var agent_good = document.all['agent_good'];
	var max = Math.floor((price_good.value - 0.01) / (price_good.value * 0.01));
	if(price_good.value >= 0.01) {
		if(id == 'skidka') if((max - agent_good.value - skidka_good.value) < 0) skidka_good.value=max - agent_good.value;
		if(id == 'agent') if((max - agent_good.value - skidka_good.value) < 0) agent_good.value=max - skidka_good.value;
		document.all['skidka'].innerHTML = max - agent_good.value;
		document.all['agent'].innerHTML = max - skidka_good.value;
	} 
	else {
		document.all['skidka'].innerHTML =0;
		document.all['agent'].innerHTML =0;
		skidka_good.value = 0;
		agent_good.value = 0;
	}
}

// rfr - status
// link - additional field
// DOM object - 
// on_off_btn - button(true/false)

function doLoad(rfr,link,query,on_off_btn) {
	var msgs = document.getElementById('msgs');
	var loader = document.getElementById('loader');
	var lst = document.getElementById('listing');
	var mn = document.getElementById('main');
	var catlist = document.getElementById('catlist');
	var method = 'GET';

	loader.innerHTML="<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/loader.gif'><br>";
	switch(rfr) {
		//view goods in category
		case '1': 
			var defname = '{_CP_SHOW}';
			var vl=query.cat.value;
			var URL = '{URLSITE}/exepanel/goods.php?type=view&cat='+vl+link;
			var zap = { q: 'category' };
		break;

		//form edit
		case '2': 
			var URL = '{URLSITE}/exepanel/goods.php?type=edit'+link;
			var zap = { q: 'goods' };
		break;
		
		//edit
		case '3': 
			var defname = '{_INST_SAVE}';
			var URL = '{URLSITE}/exepanel/goods.php?type=edit';
			var zap = { q: query };
			method = 'POST';
		break;
		
		//refresh category
		case '4':
			var URL = '{URLSITE}/exepanel/goods.php?type=refresh_cat_list'+link;
			var zap = { q: 'update' };
		break;
		
		//select current dir with view goods
		case '5': 
			var defname = '{_CP_SHOW}';
			var vl = query.cat.value;
			var URL = '{URLSITE}/exepanel/goods.php?type=view&cat='+vl;
			var zap = { q: 'category' };
		break;
		
		//special offer
		case '6':
			var URL = '{URLSITE}/exepanel/goods.php'+link;
			var zap = { q: 'status' };
		break;
		
		//for change price and onoff sale
		case '7':
			var defname = '{_INST_SAVE}';
			var URL = '{URLSITE}/exepanel/goods.php'+link;
			var zap = { q: query };
			method = 'POST';
		break;
		
		//for search
		case '8':
			if(link != 'refresh') {
				title = query.name;
				articul = query.articul;
				price = query.price;
				goodid = query.goodid;
				if(title.value.length == 0 && articul.value.length == 0 && ( price.value <= 0 || !check_int(price.value) ) && ( goodid.value <= 0 || !check_int(goodid.value))) {
					alert('{_ERR_SEARCH_EMPTY}'); return false;
				}
			}
			
			var defname = '{_CP_SEARCH}';
			var URL = '{URLSITE}/exepanel/goods.php?type=search&name='+title.value+'&articul='+articul.value+'&price='+price.value+'&goodid='+goodid.value;
			var zap = { q: 'search' };
		break;
		
		// For delete record
		case '9':
			if(gooddelid < 0) { alert('ERROR'); return false;}
			var URL = '{URLSITE}/exepanel/goods.php?type=delete&goodid='+gooddelid;
			var zap = { q: 'delete' };
		break;
	}

	
	mn.style.cursor='wait';

	//On.off button label
	if(on_off_btn && query.btn) {
		query.btn.disabled = true;
		query.btn.value = '{_GOODS_STEP4_WAIT}';
		}
		
 var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
			loader.innerHTML='';
		switch(rfr) {
			case '1':
				lst.innerHTML =  req.responseJS.view;
				msgs.innerHTML = req.responseJS.msg;
			break;
			case '2':
				lst.innerHTML =  req.responseJS.view;
				msgs.innerHTML = req.responseJS.msg;
				maxi('price');
			break;
			case '3':
				msgs.innerHTML =  req.responseJS.msg;
				if(req.responseJS.succ == 'yes') {
					if(curtype != 'search') {
						doLoad('4','&cat='+req.responseJS.cat,'',false);
						catlist.innerHTML = '{_GOODS_STEP4_WAIT}';
					} else doLoad('8','refresh',document.getElementById('search'),false);
				}
				if(curtype != 'search' && req.responseJS.succ == 'redirect') 
					doLoad('5','',document.getElementById('frm'),false);
						else doLoad('8','refresh',document.getElementById('search'),false);
			break;
			case '4':
				catlist.innerHTML =  req.responseJS.view;
				doLoad('5','',document.getElementById('frm'),false);
			break;
			case '5':
				lst.innerHTML =  req.responseJS.view;
				var btn = document.getElementById('btn');
				btn.value=defname;
			break;
			case '6':
				msgs.innerHTML =req.responseJS.msg;
				if(req.responseJS.succ == 'yes') {
					if(curtype != 'search')
					doLoad('5','',document.getElementById('frm'),false);
						else doLoad('8','refresh',document.getElementById('search'),false);
				}
			break;
			case '7':
				msgs.innerHTML =req.responseJS.msg;
				if(req.responseJS.succ == 'no') {
					if(curtype != 'search')
					doLoad('5','',document.getElementById('frm'),false);
						else doLoad('8','refresh',document.getElementById('search'),false);
				}
			break;
			case '8':
			if(link != 'refresh')
				msgs.innerHTML =req.responseJS.msg;
				lst.innerHTML =req.responseJS.view;
			break;
			case '9':
				msgs.innerHTML =req.responseJS.msg;
				if(req.responseJS.succ == 'yes') {
					if(curtype != 'search') {
						//update catlist
						doLoad('4','&cat='+req.responseJS.cat,'',false);
					} else  doLoad('8','refresh',document.getElementById('search'),false);
					document.getElementById('boxblock').style.visibility = 'hidden';
				}
			break;
		}
		
		mn.style.cursor='default';
		if(on_off_btn && query.btn) {
			query.btn.value=defname;
			query.btn.disabled = false;
		}
        }
    }
    req.open(method, URL, true);
    req.send( zap );
}

//Check key for edit 
function check_mem_key( key ) {
var LenValue = key.toString().length;
 if(!LenValue) { alert('{_GOODS_UPDATING_KEY_EMTPY}!'); return false; }
var req = new JsHttpRequest();
	    req.onreadystatechange = function() {
	        if (req.readyState == 4) {
				if(req.responseJS.key_flag) {
					document.getElementById('mem_key').innerHTML = '{_GOODS_UPDATING_KEY_IN_SESSION}';
				} else 
					alert('{_GOODS_UPDATING_KEY_ERROR}!');
			}	
		};
		req.open(null, '{URLSITE}/exepanel/goods.php?type=mem_key&key='+key, true);
	    req.send( {q:'mem_key'} );
}

function hidediv(element) {
	with(element.style) {
		visibility='hidden';
		top=0;
		left=0;
	}
}

//Updating goods
function updating(status, goodnum, form, link, on_off_btn) {
	
	var msgs = document.getElementById('msgs');
	var lst = document.getElementById('listing');
	var mn = document.getElementById('main');
	var act = document.getElementById('action');
	var update_msg = document.getElementById('update_msg');
	var countgood = document.getElementById('kolvo');
	var btnupdating = document.getElementById('btnupdating');
	//pic loader
	if(status > 1) {
		var loader = document.getElementById('loader2');
		loader.innerHTML="<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/loader.gif'><br><br>";
	}
	switch(status) {
		//Show update form
		case 1:
			link = '?type=updating&method=select&goodid='+goodnum;
			var zap = 'showform';
		break;
		
		//Loading secret list
		case 2:
			var secret_list = document.getElementById('secret_list');
			secret_list.innerHTML = '{_GOODS_STEP4_WAIT}';
			var zap  = 'loading_list';
			var link = '?type=return_listing_goods&goodid='+goodnum;
		break;
		
		//Delete one record
		case 3:
			if(gooddelid <= 0) { alert('ERROR ID'); return false;}
			var link = '?type=delete_sub&subid='+gooddelid;
			var zap = { q: 'delete' };
		break;
		
		//Add - update good
		case 4:
			var defname='{_GOODS_STEP4_ADD_UPDATE_ACTION}';
			var link = '?type=updating&method=update&goodid='+goodnum;
			var zap = { q: form };
		break;
		
		//Show form edit
		case 5:
			link = '?type=updating&method=edit&goodid='+link+'&subid='+goodnum;
			var zap = { q: 'formedit' };
		break;
		
		//ACTION edit
		case 6:
			var defname = '{_CP_CHANGE}';
			link = '?type=updating&method=edit&submethod=update&goodid='+link+'&subid='+goodnum;
			var zap = { q: form };
		break;
	}
	
	mn.style.cursor='wait';
	//On.off button label
	if(on_off_btn && form.btn) {
		form.btn.disabled = true;
		form.btn.value = '{_GOODS_STEP4_WAIT}';
		}
		
	var req = new JsHttpRequest();
	    req.onreadystatechange = function() {
	        if (req.readyState == 4) {
			//pic loader
			if(status > 1) loader.innerHTML='';
				switch(status) {
					case 1:
						if(req.responseJS.stat == 'updateform') {
							act.innerHTML = req.responseJS.form;
							lst.innerHTML = '';
							msgs.innerHTML= '';	
							updating(2, req.responseJS.goodid, '', '', false);
						} else 
							msgs.innerHTML = req.responseJS.error;
					break;
					case 2:
						var secret_list = document.getElementById('secret_list');
						if(req.responseJS.stat == 'loading') 
							secret_list.innerHTML = req.responseJS.listing;
								else secret_list.innerHTML = req.responseJS.msg;
					break
					case 3:
						hidediv(document.getElementById('delblock'));
						if(req.responseJS.key_flag)  {
							if(req.responseJS.stat == 'updateform') {
								//show current count good
								countgood.innerHTML = req.responseJS.kolvo;
								//show button
								btnupdating.innerHTML = req.responseJS.btnupdating;
								//refresh secret list
								updating(2, req.responseJS.goodid, '', '', false);
							}
							update_msg.innerHTML=req.responseJS.msg
						} else 	
							alert('{_GOODS_UPDATING_KEY_ERROR}!');
					break;
					case 4:
						if(req.responseJS.stat == 'errorupload') {
							document.getElementById('errorupdate').innerHTML = req.responseJS.msg;
						} 
						if(req.responseJS.stat == 'success') {
							update_msg.innerHTML=req.responseJS.msg;
							hidediv(document.getElementById('addblock'));
							//show current count good
							countgood.innerHTML = req.responseJS.kolvo;
							//show button
							btnupdating.innerHTML = req.responseJS.btnupdating;
							//refresh secret list
							updating(2, req.responseJS.goodid, '', '', false);
						}
						form.reset();
					break;
					case 5:
						var secret_list = document.getElementById('secret_list');
						if(req.responseJS.key_flag) {
							update_msg.innerHTML = '';
							if(req.responseJS.stat == 'success') {
								secret_list.innerHTML = req.responseJS.form;
							} else update_msg.innerHTML = req.responseJS.error;
						} else 
							alert('{_GOODS_UPDATING_KEY_ERROR}!');
					break;
					case 6:
						if(req.responseJS.key_flag) {
							if(req.responseJS.stat == 'success') {
								updating(2, req.responseJS.goodid, '', '', false);
								update_msg.innerHTML = req.responseJS.msg;
							}
							if(req.responseJS.stat == 'errorupload')
								update_msg.innerHTML = req.responseJS.msg;
						} else 
							alert('{_GOODS_UPDATING_KEY_ERROR}!');
					break;
				}
				
				mn.style.cursor='default';
				if(on_off_btn && form.btn) {
					form.btn.value=defname;
					form.btn.disabled = false;
				}
			}	
		};

		req.open(null, '{URLSITE}/exepanel/goods.php'+link, true);
	    req.send( zap );
	
}

//Box 
function BoxDiv(id) {
	var box = document.getElementById(id);	
	var reg=/(\d+)/
	arr=reg.exec(box.style.left);
	MouseX = window.event.clientX - arr[0];
	arr=reg.exec(box.style.top);
	MouseY = window.event.clientY - arr[0];
	box.style.cursor='move';
	document.onmouseup=function() {
		document.onmousemove=null;
		document.onmouseup=null;
		box.style.cursor='default';
	};
	document.onmousemove=function() {
		box.style.left = window.event.clientX - MouseX;
		box.style.top = window.event.clientY - MouseY;
	};
	
}


</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>

<div id="action">{ACTION}</div>
<div id="loader"></div>
<div id="msgs"></div>
<div id="listing"></div>

</div>

<div id="boxblock" class="boxblock">
<div id="boxmoveblock" class="boxmoveblock" onmousedown="BoxDiv('boxblock')"><b>{_GOODS_DELETE}</b><span id="numgood"></span></div>
<br>{_GOODS_DELETE_INFO}</br><br>
<b>{_GOODS_DELETE_CONTINUE} ?</b>
<br><br>
<input type="button" class="inputbutton" value="{_CP_YES}" onclick="doLoad('9', '', '', false)"> &nbsp;
<input type="button"  class="inputbutton" value="{_CP_NO}" onclick="document.getElementById('boxblock').style.visibility='hidden'">
<br><br>
</div>
{SCRIPT_FOR_RETURN_FROM_UPDATING}