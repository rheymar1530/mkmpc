
<html>
<head>
	<title>BLACK PANTHER</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		body{
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0px;
			background-image: linear-gradient(315deg, #000000 0%, #414141 74%);

		}
		.center {
			margin: auto;
			width: 60%;
			padding: 10px;
		}
		div.center{
			font-size: 10px;
			line-height: 10px;
			background:url("{{URL::asset('dist/img/perry.png')}}");
			background-size: 100% 100%;
			background-position: center;
			-webkit-background-clip:text;
			-webkit-text-fill-color:rgba(115,115,115,0);
			word-break: break-all;
			height: 800px;
			text-transform: uppercase;

		}
	</style>
</head>
<body>
	<div class="center">
		@for($i=0;$i<1000;$i++)
		PERRY THE PLATYPUS
		@endfor
	</div>
</body>
</html>
