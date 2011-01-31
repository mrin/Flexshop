<script type="text/javascript">
    $(function(){
       $(".menutext").mouseenter(function(){
            $(".menutext").next().hide();
            $('#' + $(this).attr('rel')).show();
       });
       $(".menutop").mouseleave(function(){
            $(this).parent().hide();
       })
    });
</script>
<div style="height:28px;">
    <div class="menuContainer">
        <a href="#" class="menutext" rel="dropmenu1">{_CP_SETTING}</a>
        <div id="dropmenu1" style="position:absolute;top:190px;background-color:#02124c;width:170px;display:none;border-left:1px solid #866216;border-right:1px solid #866216;border-bottom:1px solid #b2b2b2;padding:0px">
        <table width=100% cellpadding=3 cellspacing=0 class="menutop">
            <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/profile.php" title="{_CP_PROFILE}">{_CP_PROFILE}</a></td></tr>
            <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/s_shop.php" title="{_CP_CONFIGURATION}">{_CP_CONFIGURATION}</a></td></tr>
        </table>
        </div>
    </div>
    <div class="menuContainer">
        <a href="#" class="menutext" rel="dropmenu2">{_CP_CONTROL}</a>
        <div id=dropmenu2 style="position:absolute;top:190px;background-color:#02124c;width:180;display:none;border-left:1px solid #866216;border-right:1px solid #866216;border-bottom:1px solid #866216;">
            <table width=100% cellpadding=3 cellspacing=0 class="menutop">
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/news.php" title="{_CP_NEWS}">{_CP_NEWS}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/mygoods.php" title="{_CP_CUSTOMERS}">{_CP_CUSTOMERS}</a></td></tr>
            </table>
        </div>
    </div>
    <div class="menuContainer">
        <a href="#" class="menutext" rel="dropmenu3">{_CP_GOODS}</a>
        <div id=dropmenu3 style="position:absolute;top:190px;background-color:#02124c;width:180;display:none;border-left:1px solid #866216;border-right:1px solid #866216;border-bottom:1px solid #866216;">
            <table width=100% cellpadding=3 cellspacing=0 class="menutop">
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/modshipping.php" title="{_CP_MODULESHIPPING}">{_CP_MODULESHIPPING}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/category.php" title="{_CP_CATGOODS}">{_CP_CATGOODS}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/type_good.php" title="{_CP_TYPECOODS}">{_CP_TYPECOODS}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/goods.php" title="{_CP_LISTGOODS}">{_CP_LISTGOODS}</a></td></tr>
            </table>
        </div>
    </div>
    <div class="menuContainer">
        <a href="#" class="menutext" rel="dropmenu4">{_CP_PAYMENTS}</a>
        <div id=dropmenu4 style="position:absolute;top:190px;background-color:#02124c;width:160;display:none;border-left:1px solid #866216;border-right:1px solid #866216;border-bottom:1px solid #866216;">
            <table width=100% cellpadding=3 cellspacing=0 class="menutop">
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/payment_history.php" title="{_CP_PAYHISTORY}">{_CP_PAYHISTORY}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/sell_stat.php" title="{_CP_STATSELLING}">{_CP_STATSELLING}</a></td></tr>
            </table>
        </div>
    </div>
    <div class="menuContainer">
        <a href="#" class="menutext" rel="dropmenu5">{_CP_MSGS}</a>
        <div id=dropmenu5 style="position:absolute;top:190px;background-color:#02124c;width:160;display:none;border-left:1px solid #866216;border-right:1px solid #866216;border-bottom:1px solid #866216;">
            <table width=100% cellpadding=3 cellspacing=0 class="menutop">
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/new_message.php" title="{_CP_CREATEMSG}">{_CP_CREATEMSG}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/mail_setting.php" title="{_CP_MAIL_SETTING}">{_CP_MAIL_SETTING}</a></td></tr>
                <tr onMouseOver="colorbgchange(this, '#246cce')" onmouseout="colorbgchange(this,'#02124c')"><td><a class=submenu href="{URLSITE}/exepanel/ticket.php" title="{_CP_VIEWMSG}">{_CP_VIEWMSG}</a></td></tr>
            </table>
        </div>
    </div>
    <div class="menuContainer"><a href="{URLSITE}/exepanel/?logout" class="menutext"><b><font color=#f30303>{_CP_LOGOUT}</font></b></a></div>
</div>