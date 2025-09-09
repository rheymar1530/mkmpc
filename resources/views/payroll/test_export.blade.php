<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TEST</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th style="background:red;color: white;">&nbsp;&nbsp;&nbsp;&nbsp;ID</th>
				<th colspan="2">NAME</th>

				
			</tr>
		</thead>
		<tbody>
			@for($i=0;$i<5000;$i++)
			@foreach($employee as $e)
			<tr>
				<td>{{$e->id_employee}}</td>
				<td>{{$e->name}}</td>
				<td>HAHAHAHAHHAAHHAHHASHDAHSDHSHSHSHDS</td>
				
			</tr>
			@endforeach
			@endfor
		</tbody>
	</table>
</body>
</html>