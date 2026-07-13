<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\Audit\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    protected const FILTER_FIELD_LABELS = [
        'action' => 'Action',
        'type' => 'Record type',
        'user' => 'Team member',
    ];

    protected const ACTION_LABELS = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'published' => 'Published',
        'archived' => 'Archived',
        'closed' => 'Closed',
        'restored' => 'Restored',
        'login' => 'Login',
        'login_failed' => 'Login failed',
    ];

    protected const TYPE_LABELS = [
        'article' => 'Article',
        'career' => 'Career',
        'media' => 'Media',
        'tag' => 'Tag',
        'article_category' => 'Article category',
        'career_category' => 'Career category',
        'user' => 'User',
    ];

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $filterKeys = array_keys(self::FILTER_FIELD_LABELS);
        $filters = collect($filterKeys)
            ->mapWithKeys(fn ($key) => [
                $key => collect((array) $request->input($key, []))
                    ->filter()
                    ->values()
                    ->all(),
            ])
            ->all();

        $filters['created_from'] = $request->string('created_from')->toString();
        $filters['created_to'] = $request->string('created_to')->toString();

        $hasActiveFilters = collect($filters)->contains(fn ($values) => ! empty($values));

        $filterOptions = [
            'action' => $this->mapOptions(self::ACTION_LABELS),
            'type' => $this->mapOptions(self::TYPE_LABELS),
            'user' => User::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($user) => ['value' => $user->id, 'label' => $user->name])
                ->all(),
        ];

        $filterFields = collect(self::FILTER_FIELD_LABELS)
            ->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
                'placeholder' => "Select {$label}",
                'options' => $filterOptions[$key],
                'selected' => $filters[$key] ?? [],
            ])
            ->values()
            ->all();

        $filterFields[] = [
            'type' => 'date_range',
            'key' => 'created',
            'label' => 'Date',
            'from' => $filters['created_from'],
            'to' => $filters['created_to'],
        ];

        return view('audit.index', [
            'auditLogs' => $this->auditLogService->getPaginatedAuditLogs($search, $filters),
            'stats' => $this->auditLogService->getStats(),
            'search' => $search,
            'filters' => $filters,
            'hasActiveFilters' => $hasActiveFilters,
            'filterFields' => $filterFields,
            'searchSuggestions' => collect(self::ACTION_LABELS)
                ->values()
                ->merge(User::query()->orderBy('name')->pluck('name'))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    protected function mapOptions(array $options): array
    {
        return collect($options)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    public function restore(Request $request, AuditLog $auditLog): RedirectResponse
    {
        try {
            $this->auditLogService->restore($auditLog);
        } catch (\InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            return back()->with('error', 'Restore failed, a record with the same slug or ID already exist.');
        }

        return back()->with('success', 'Record restored successfully.');
    }
}
