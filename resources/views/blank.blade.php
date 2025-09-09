<html>
<head>
</head>
<body > <!--it will wait to load-->
<a href="javascript:window_close_onclick();"> close this</a>
<!-- your html... -->
<script>
	function window_close_onclick(){
		if(confirm("DODODOD")){
var customWindow = window.open('', '_blank', '');
    customWindow.close();
		// var popup = window.open('/close_tab', '_self', '');
		// 	popup.close();
		}
	}
window.addEventListener("beforeunload", function (e) {
  var confirmationMessage = "\o/";
  window.close()
  (e || window.event).returnValue = confirmationMessage; //Gecko + IE
  return confirmationMessage;                            //Webkit, Safari, Chrome
});
</script>

</body>
</html>