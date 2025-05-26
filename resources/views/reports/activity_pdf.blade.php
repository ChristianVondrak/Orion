<!DOCTYPE html><html><head><meta charset="utf-8"><style>/* estilos simples */</style></head><body>
<h1>Activity Index Report ({{ $start }} – {{ $end }})</h1>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead><tr><th>Name</th><th>Email</th><th>Activity Index</th></tr></thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->name }}</td>
            <td>{{ $r->email }}</td>
            <td>{{ $r->activity_index }}%</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body></html>
