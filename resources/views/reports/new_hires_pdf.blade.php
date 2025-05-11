<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <style>
        body{font-family:sans-serif;font-size:12px;}
        table{width:100%;border-collapse:collapse;margin-top:1em;}
        th,td{border:1px solid #444;padding:4px;text-align:left;}
        th{background:#f0f0f0;}
        h1,h2{margin:0;padding:0;}
        .header{text-align:center;margin-bottom:1em;}
    </style>
</head>
<body>
<div class="header">
    <h1>New Hires Report</h1>
    <h2>{{ $start }} - {{ $end }}</h2>
</div>
<table>
    <thead>
    <tr>
        <th>Name</th><th>Country</th><th>Position</th><th>Start Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->name }}</td>
            <td>{{ $r->country }}</td>
            <td>{{ $r->position }}</td>
            <td>{{ $r->start_date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
