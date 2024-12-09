<?php

namespace App\Listeners;

use App\Events\NotificationSuperAdminEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSuperAdminListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSuperAdminEvent $event): void
    {
        $differences = [];

        // Vérifiez que les propriétés existent et sont valides
        if (isset($event->model)) {
            $differences['model'] = $event->model;
        } else {
            $differences['model'] = 'Inconnu';
        }

        if (isset($event->state)) {
            switch ($event->state) {
                case 'update':
                    $differences['state'] = 'Mise à jour';
                    if (isset($event->newObj) && is_object($event->newObj) && isset($event->oldObj) && is_object($event->oldObj)) {
                        foreach ($event->newObj as $property => $newValue) {
                            if (property_exists($event->oldObj, $property)) {
                                $oldValue = $event->oldObj->$property;
                                if ($oldValue !== $newValue) {
                                    $differences[$property] = [
                                        'old' => $oldValue,
                                        'new' => $newValue,
                                    ];
                                }
                            }
                        }
                    }
                    break;

                case 'create':
                    $differences['state'] = 'Création';
                    if (isset($event->newObj) && is_object($event->newObj)) {
                        foreach ($event->newObj as $property => $newValue) {
                            $differences[$property] = $newValue;
                        }
                    }
                    break;

                case 'delete':
                    $differences['state'] = 'Suppression';
                    if (isset($event->newObj) && is_object($event->newObj)) {
                        foreach ($event->newObj as $property => $newValue) {
                            $differences[$property] = $newValue;
                        }
                    }
                    break;

                case 'forced_delete':
                    $differences['state'] = 'Suppression définitive';
                    if (isset($event->newObj) && is_object($event->newObj)) {
                        foreach ($event->newObj as $property => $newValue) {
                            $differences[$property] = $newValue;
                        }
                    }
                    break;

                default:
                    $differences['state'] = 'Inconnu';
                    break;
            }
        }

        // Emit a notification to users with the role 'super_admin'
        $superAdmins = \App\Models\User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new \App\Notifications\SuperAdminNotification($differences));
        }
    }
}
