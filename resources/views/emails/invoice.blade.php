<x-mail::message>
    # Factura Mensual — {{ $invoice['period'] }}

    **Para:** {{ $invoice['user']->first_name }} {{ $invoice['user']->last_name }}

    A continuación el detalle de tus horas y montos:

    <table style="width:100%; border-collapse: collapse; margin-bottom: 1rem;">
        <thead>
        <tr>
            <th style="border-bottom:1px solid #ddd; text-align:left; padding: 0.5rem;">Fecha</th>
            <th style="border-bottom:1px solid #ddd; text-align:right; padding: 0.5rem;">Horas</th>
            <th style="border-bottom:1px solid #ddd; text-align:right; padding: 0.5rem;">Tarifa/h</th>
            <th style="border-bottom:1px solid #ddd; text-align:right; padding: 0.5rem;">Monto</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice['daily'] as $day)
            <tr>
                <td style="padding: 0.5rem;">{{ $day['date'] }}</td>
                <td style="padding: 0.5rem; text-align:right;">{{ $day['hours'] }}</td>
                <td style="padding: 0.5rem; text-align:right;">${{ number_format($day['rate'],2) }}</td>
                <td style="padding: 0.5rem; text-align:right;">${{ number_format($day['amount'],2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    **Subtotal:** ${{ number_format($invoice['subtotal'],2) }}
    **Ajuste automático:** ${{ number_format($invoice['auto_adjustment'],2) }}
    **Ajuste manual:** ${{ number_format($invoice['manual_adjustment'],2) }}

    ## Total: ${{ number_format($invoice['total'],2) }}

    <x-mail::button :url="$invoice['url']">
        Ver Proyecto
    </x-mail::button>

    Gracias por tu dedicación,<br>
    {{ config('app.name') }}
</x-mail::message>
