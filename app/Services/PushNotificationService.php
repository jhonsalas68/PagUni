<?php

namespace App\Services;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT', 'mailto:admin@example.com'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        try {
            $this->webPush = new WebPush($auth);
        } catch (\Exception $e) {
            Log::error('PushNotificationService Error: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a specific user.
     */
    public function sendToUser($userId, $title, $body, $url = '/')
    {
        if (!$this->webPush) return;

        $subscriptions = \DB::table('push_subscriptions')
            ->where('user_id', $userId)
            ->get();

        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]);

                $this->webPush->sendOneNotification(
                    $subscription,
                    json_encode([
                        'title' => $title,
                        'body' => $body,
                        'url' => $url,
                        'icon' => '/images/icons/icon-192x192.png'
                    ])
                );
            } catch (\Exception $e) {
                Log::error('Error sending push to user ' . $userId . ': ' . $e->getMessage());
                // If 410 Gone, remove subscription
                if (strpos($e->getMessage(), '410 Gone') !== false) {
                     \DB::table('push_subscriptions')->where('endpoint', $sub->endpoint)->delete();
                }
            }
        }
    }

    /**
     * Notify students and professor about a new class schedule.
     */
    public function notifyNewClass($horario)
    {
        $horario->load('cargaAcademica.grupo.inscriptions.estudiante.user', 'cargaAcademica.profesor.user', 'aula', 'cargaAcademica.grupo.materia');

        $materia = $horario->cargaAcademica->grupo->materia->nombre ?? 'Clase';
        $hora = $horario->hora_inicio . ' - ' . $horario->hora_fin;
        $aula = $horario->aula->codigo_aula ?? 'N/A';
        $dia = ucfirst($horario->dia_semana);

        $title = "Nueva Clase Programada: $materia";
        $body = "$dia de $hora en Aula $aula";
        $url = '/estudiante/dashboard'; // Adjust based on user role

        // Notify Professor
        if ($horario->cargaAcademica->profesor && $horario->cargaAcademica->profesor->user) {
             $this->sendToUser(
                 $horario->cargaAcademica->profesor->user->id, 
                 "Nueva Asignación: $materia", 
                 $body, 
                 '/profesor/mi-horario'
             );
        }

        // Notify Students
        if ($horario->cargaAcademica->grupo && $horario->cargaAcademica->grupo->inscriptions) {
            foreach ($horario->cargaAcademica->grupo->inscriptions as $inscripcion) {
                if ($inscripcion->estudiante && $inscripcion->estudiante->user) {
                    $this->sendToUser(
                        $inscripcion->estudiante->user->id,
                        $title,
                        $body,
                        $url
                    );
                }
            }
        }
    }
}
