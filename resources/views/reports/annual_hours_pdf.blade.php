<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        h1, h2 { margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 1em; }
    </style>
</head>
<body>
<div class="header">
    <h1>Reporte anual de horas trabajadas por profesional</h1>
    <h2>{{ $year }}</h2>
</div>

<table>
    <thead>
    <tr>
        <th>Nombre</th>
        <th>Email</th>
        @for($m = 1; $m <= 12; $m++)
            <th>{{ \Carbon\Carbon::create()->month($m)->locale('es')->isoFormat('MMM') }}</th>
        @endfor
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
        <tr>
            <td>{{ $row['name'] }}</td>
            <td>{{ $row['email'] }}</td>
            @php $total = 0; @endphp
            @for($m = 1; $m <= 12; $m++)
                @php $h = $row['months'][$m] ?? 0; $total += $h; @endphp
                <td style="text-align:right; @if($h < 160 && $h > 0) background-color:#fee2e2;color:#b91c1c; @elseif($h >= 160) background-color:#dcfce7;color:#166534; @endif">
                    {{ $h > 0 ? number_format($h,2) : '-' }}
                </td>
            @endfor
            <td style="font-weight:bold;text-align:right; @if($total < 160*12 && $total > 0) background-color:#fee2e2;color:#b91c1c; @elseif($total >= 160*12) background-color:#dcfce7;color:#166534; @endif">
                {{ number_format($total,2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html> 