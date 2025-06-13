<?php

namespace App\Services;

use App\Models\Worker;

class WorkerRoleService
{
    public function isAdmin(Worker $worker): bool
    {
        return $worker->hasRole('admin');
    }

    public function isWorker(Worker $worker): bool
    {
        return $worker->hasRole('worker');
    }

    public function hasRole(Worker $worker, string $role): bool
    {
        return $worker->hasRole($role);
    }

    public function getPrimaryRole(Worker $worker): string
    {
        return $worker->getRoleNames()->first() ?? 'unknown';
    }
}
