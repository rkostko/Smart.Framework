[%%%%COMMENT%%%%]
	Partial Template: Core.HTMLEditorCallBack (Js)
	!!! Do NOT add JS or HTML comments. This will be used for inline Javascript into a HTML on*="" and will break it !!!
[%%%%/COMMENT%%%%]
var url = '[####URL|js####]';
if([####IS_POPUP|bool####]) {
	if(window.opener) {
		window.opener.CLEditor_SmartFrameworkComponents_fileBrowserCallExchange(url);
	} else {
		parent.CLEditor_SmartFrameworkComponents_fileBrowserCallExchange(url);
	}
	SmartJS_BrowserUtils.CloseModalPopUp();
	return false;
} else {
	return CLEditor_SmartFrameworkComponents_fileBrowserCallExchange(url);
}