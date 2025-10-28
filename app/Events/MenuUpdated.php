<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuUpdated
{
    use Dispatchable, SerializesModels;

    public string $action;
    public ?int $menuId;

    /**
     * Create a new event instance.
     *
     * @param string $action The action performed (created, updated, deleted, reordered, bulk_created)
     * @param int|null $menuId The ID of the affected menu (null for bulk operations)
     */
    public function __construct(string $action = 'updated', ?int $menuId = null)
    {
        $this->action = $action;
        $this->menuId = $menuId;
    }
}
