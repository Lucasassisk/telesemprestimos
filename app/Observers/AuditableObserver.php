<?php

namespace App\Observers;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AuditableObserver
{
    private function log(Model $model, string $action, array $changes): void
    {
        if (! Schema::hasTable('audits')) {
            return;
        }

        if ($model instanceof Audit) {
            return;
        }

        $request = request();
        Audit::create([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => $action,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }

    public function created(Model $model): void
    {
        $this->log($model, 'created', ['new' => $model->getAttributes()]);
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $original = array_intersect_key($model->getOriginal(), $changes);
        $this->log($model, 'updated', ['old' => $original, 'new' => $changes]);
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted', ['old' => $model->getAttributes()]);
    }
}
