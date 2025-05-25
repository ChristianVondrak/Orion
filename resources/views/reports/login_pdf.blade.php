<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f8f9fa; }
        h1 { color: #2d3748; margin-bottom: 1em; }
        .report-period { margin-bottom: 1em; color: #4a5568; }
        .delayed { background-color: #FEE2E2; }
        .text-red { color: #DC2626; }
        .text-green { color: #059669; }
        .time { font-size: 11px; }
    </style>
</head>
<body>
    <h1>Login por Profesional</h1>
    <div class="report-period">
        Período: {{ $start }} - {{ $end }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Primer Login</th>
                <th>Último Login</th>
                <th>Hora Promedio</th>
                <th>Retrasos</th>
                <th>Minutos Retrasados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr class="{{ $row->is_delayed ? 'delayed' : '' }}">
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->email }}</td>
                    <td class="{{ strtotime(substr($row->first_login, 11, 5)) > strtotime('9:00 AM') ? 'text-red' : 'text-green' }}">
                        {{ $row->first_login }}
                        <div>{{ $row->first_login_time }}</div>
                    </td>
                    <td>
                        {{ $row->last_login }}
                        <div>{{ $row->last_login_time }}</div>
                    </td>
                    <td class="{{ strtotime($row->average_start_time) > strtotime('9:00 AM') ? 'text-red' : 'text-green' }}">
                        {{ $row->average_start_time }}
                    </td>
                    <td class="{{ $row->delays_count > 0 ? 'text-red' : 'text-green' }}">
                        {{ $row->delays_count }}
                    </td>
                    <td class="{{ $row->is_severely_delayed ? 'text-red font-bold' : ($row->total_delay_minutes > 0 ? 'text-yellow' : 'text-green') }}">
                        {{ $row->total_delay_minutes_formatted }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 