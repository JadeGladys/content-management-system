<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->writeAudit('created', [], $model->auditValues($model->getAttributes()));
        });

        static::updated(function (Model $model) {
            $changes = $model->auditValues($model->getChanges());

            if (empty($changes)) {
                return;
            }

            $original = collect($model->getOriginal())
                ->only(array_keys($changes))
                ->all();

            $model->writeAudit('updated', $original, $changes);
        });

        static::deleted(function (Model $model) {
            $model->writeAudit('deleted', $model->auditValues($model->getOriginal()), []);
        });
    }

    public function auditAction(string $action): void
    {
        $this->writeAudit($action, [], []);
    }

    protected function auditValues(array $values): array
    {
        $excluded = array_merge(
            ['created_at', 'updated_at'],
            $this->auditExclude ?? []
        );

        return collect($values)
            ->except($excluded)
            ->all();
    }

    public function writeAudit(string $action, array $oldValues, array $newValues): void
    {
        if (! AuditLog::isRecording()) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
