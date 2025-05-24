<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected string $baseUrl;
    protected string $token;
    protected string $phoneNumberId;
    protected string $version;

    public function __construct()
    {
        $this->version        = config('services.whatsapp.version');
        $this->phoneNumberId  = config('services.whatsapp.phone_number_id');
        $this->token          = config('services.whatsapp.token');
        $this->baseUrl        = "https://graph.facebook.com/{$this->version}/{$this->phoneNumberId}";
    }

    /**
     * Envía un texto libre vía WhatsApp.
     */
    public function sendText(string $to, string $message): void
    {
        Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ])->throw();
    }

    /**
     * Envía un documento (p.ej. PDF) vía WhatsApp.
     */
    public function sendDocument(string $to, string $pdfUrl): void
    {
        Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'document',
                'document'          => [
                    'link'     => $pdfUrl,
                    'caption'  => 'Tu factura mensual',
                    'filename' => 'Factura.pdf',
                ],
            ])->throw();
    }

    /**
     * Envía un template de WhatsApp para recordatorio de pago.
     *
     * @param  string $to    Número en E.164 sin prefijo "whatsapp:"
     * @param  string $name  Nombre del cliente ({{1}})
     * @param  string $date  Fecha ({{2}})
     * @param  float  $amt   Monto ({{3}})
     */
    public function sendAutoPayReminder(string $to, string $name, string $date, float $amt): void
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'     => 'auto_pay_reminder_2',
                'language' => ['code' => 'en_US'], // o 'es_ES' según tu template
                'components' => [[
                    'type'       => 'body',
                    'parameters' => [
                        ['type'=>'text','text'=> $name],          // {{1}}
                        ['type'=>'text','text'=> $date],          // {{2}}
                        ['type'=>'text','text'=> number_format($amt,2)], // {{3}}
                    ],
                ]],
            ],
        ];

        Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", $payload)
            ->throw();
    }
}
