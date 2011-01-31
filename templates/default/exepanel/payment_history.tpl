<script src="{URLSITE}/templates/{TEMPLDEF}/exepanel/JsHttpRequest.js"></script>
<script language="JavaScript">
var invoice='';
var pages = '';

//Check key for edit 
function check_mem_key( key ) {
var LenValue = key.toString().length;
 if(!LenValue) { alert('{_GOODS_UPDATING_KEY_EMTPY}!'); return false; }
var req = new JsHttpRequest();
	    req.onreadystatechange = function() {
	        if (req.readyState == 4) {
				if(req.responseJS.key_flag) {
					document.getElementById('mem_key').innerHTML = '{_GOODS_UPDATING_KEY_IN_SESSION}';
					lock_unclock(0);
				} else 
					alert('{_GOODS_UPDATING_KEY_ERROR}!');
			}	
		};
		req.open(null, '{URLSITE}/exepanel/goods.php?type=mem_key&key='+key, true);
	    req.send( {q:'mem_key'} );
}

//check search
function checkfield(field){
	if(!check_int(field.value) || field.value <= 0 || !field.value.toString().length) {
		alert('{_PAYMENT_SEARCH_EMPTYFIELD}!'); return false; }
	updating(4, '', '', field.value, false);
}

//LOCK - 1/UNLOCK - 0
function lock_unclock(flag) {
	var obj = document.getElementById('p_c_h');
	for(i=0;i<obj.elements.length;i++)
		if(obj.elements[i].type=='select-one' ||obj.elements[i].type =='button' || obj.elements[i].type=='text')
			if(flag) obj.elements[i].disabled=true;
				else obj.elements[i].disabled=false;
}


function pagelist(num) {
	updating(1, num, '', '', false);
}

function updating(status, page, form, link, on_off_btn) {
	var msgs = document.getElementById('msgs');
	var pg = document.getElementById('pages');
	var lst = document.getElementById('listing');
	var mn = document.getElementById('main');
	var loader = document.getElementById('loader');
	loader.innerHTML="<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/loader.gif'><br><br>";
	switch(status) {
		//Listing
		case 1:
			pages = page;
			msgs.innerHTML='';
			link = '?js=yes&page='+page;
			var zap = { q: 'listing'};
		break;
		//Delete
		case 2:
			if(confirm('{_CP_DEL} ?')) {
				link ='?type=delete&invoice='+link;
				var zap = { q: 'delete'};
			} else return false;
		break;
		//Update
		case 3:
			link ='?type=edit';
			var defname = '{_INST_SAVE}';
			var zap = { q: form };
		break;
		//Search
		case 4:
			link ='?js=yes&inv='+link;
			var zap = { q: 'search'};
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
			loader.innerHTML='';
				switch(status) {
					case 1:
						pg.innerHTML = req.responseJS.pages;
						lst.innerHTML = req.responseJS.listing;
					break;
					case 2:
						if(req.responseJS.key_flag) {
							pagelist(1);
							msgs.innerHTML=req.responseJS.msg;
						} else 
							alert('{_GOODS_UPDATING_KEY_ERROR}!');
					break;
					case 3:
						if(req.responseJS.key_flag) 
							msgs.innerHTML=req.responseJS.msg;
						else
							alert('{_GOODS_UPDATING_KEY_ERROR}!');
					break;
					case 4:
						msgs.innerHTML=req.responseJS.msg;
						lst.innerHTML = req.responseJS.listing+req.responseJS.back;
						pg.innerHTML='';
					break;
				}
				
				mn.style.cursor='default';
				if(on_off_btn && form.btn) {
					form.btn.value=defname;
					form.btn.disabled = false;
				}
			}	
		};

		req.open(null, '{URLSITE}/exepanel/payment_history.php'+link, true);
	    req.send( zap );
	
}
</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<div id="infolabel">{_PAYMENT_INFO}</div>
<p>
<table border="1"  align="center" width="700" cellpadding="5" class="dash">
	<tr height="35">
		<td align="right" class="contdark" width="180"><b>{_PAYMENT_INVOICE}</b>:</td>
		<td align="left" class="contlight">
		<input class="inputcenter" type="text" name="inv" size="15">
		<input class="inputbutton" type="button" onClick="checkfield(document.getElementById('inv'));" value="{_PAYMENT_SEARCH}">
		</td>
		
	</tr>
	<tr height="35">
		<td align="right" class="contdark" width="180"><b>{_GOODS_UPDATING_KEY}</b>:</td>
		<td align="left" class="contlight">{MEM_KEY}</td>
	</tr>
</table>
</p>
<div id="loader"></div>
<div id="msgs">{MSGS}</div>
<div id="listing">{LIST_PAYMENTS}</div>
<div id="pages">{PAGES_NUMBER}</div>
</div>