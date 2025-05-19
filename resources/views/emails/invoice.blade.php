<x-mail::message>
# Monthly Invoice — {{ $invoice['period'] }}

**To:** {{ $invoice['user']->first_name }} {{ $invoice['user']->last_name }}

Below is the breakdown of your hours and amounts:

<x-mail::table>
| Date       | Hours   | Rate/hr  | Amount    |
| ---------- | ------: | --------:| --------: |
@forelse($invoice['daily'] as $day)
| {{ $day['date'] }} | {{ $day['hours'] }}h | ${{ number_format($day['rate'],2) }} | ${{ number_format($day['amount'],2) }} |
@empty
| _No records for this period._ |  |  |  |
@endforelse
</x-mail::table>

<x-mail::table>
| Concept            | Amount                       |
| ------------------ | ---------------------------: |
| Subtotal           | ${{ number_format($invoice['subtotal'],2) }} |
| Productivity Bonus | ${{ number_format($invoice['auto_adjustment'],2) }} |
| Manual Adjustment  | ${{ number_format($invoice['manual_adjustment'],2) }} |
| **Total**          | **${{ number_format($invoice['total'],2) }}** |
</x-mail::table>

Thanks you,
{{ config('app.name') }}
</x-mail::message>

