<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { border: 1px solid #444; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; }
        h1, h2 { margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 1em; }
    </style>
</head>
<body>
    <h1>Terminations Report</h1>
    <h2>{{ $start }} – {{ $end }}</h2>
    <table border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $r->name }}</td>
                <td>{{ $r->termination_date }}</td>
                <td>{{ $r->reason }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
