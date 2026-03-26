<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ISC  PR ALLOCATION</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>

            <div class="row">
            @foreach($MemberFinalData as $group=>$MemberTable)
            <div style="page-break-inside: avoid">
                <h4 style="margin-bottom:0">{{strtoupper($group)}}</h4>
                @include('patronage_refunds.export-create-table',['MemberTable'=>$MemberTable])
                </div>
            @endforeach
        </div>
</body>
</html>