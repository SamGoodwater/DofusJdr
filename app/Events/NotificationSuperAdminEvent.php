<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSuperAdminEvent
{
    use Dispatchable, SerializesModels;

    public $model;
    public $state;
    public $newObj;
    public $oldObj;

    /**
     * Create a new event instance.
     */
    public function __construct(string $model, string $state = 'update', Object $newObj, Object $oldObj = null) {}
}
