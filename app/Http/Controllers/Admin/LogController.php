<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    private const PER_PAGE = 50;

    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function store(Request $request): View
    {
        $cleared = false;

        if ($request->input('action') === 'clear_logs') {
            ActivityLogger::clear();
            $cleared = true;
        }

        return $this->render($request, $cleared);
    }

    private function render(Request $request, bool $cleared = false): View
    {
        $filterEvent = trim((string) $request->query('filter_event', ''));
        $filterUser = trim((string) $request->query('filter_user', ''));
        $filterDate = trim((string) $request->query('filter_date', ''));
        $search = trim((string) $request->query('search', ''));

        $entries = collect(ActivityLogger::entries());

        $filtered = $entries
            ->when($filterEvent !== '', fn ($c) => $c->filter(fn (array $e) => str_contains($e['event'] ?? '', $filterEvent)))
            ->when($filterUser !== '', fn ($c) => $c->filter(fn (array $e) => ($e['username'] ?? '') === $filterUser))
            ->when($filterDate !== '', fn ($c) => $c->filter(fn (array $e) => str_starts_with($e['timestamp'] ?? '', $filterDate)))
            ->when($search !== '', fn ($c) => $c->filter(fn (array $e) => stripos((string) json_encode($e), $search) !== false))
            ->values();

        $page = max(1, $request->integer('page', 1));
        $total = $filtered->count();

        return view('admin.logs', [
            'username' => $request->user()->username,
            'cleared' => $cleared,
            'filter_event' => $filterEvent,
            'filter_user' => $filterUser,
            'filter_date' => $filterDate,
            'search_term' => $search,
            'all_users' => $entries->pluck('username')->filter()->unique()->sort()->values()->all(),
            'all_events' => $entries->pluck('event')->filter()->unique()->sort()->values()->all(),
            'page' => $page,
            'per_page' => self::PER_PAGE,
            'total' => $total,
            'total_pages' => max(1, (int) ceil($total / self::PER_PAGE)),
            'offset' => ($page - 1) * self::PER_PAGE,
            'slice' => $filtered->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values()->all(),
        ]);
    }
}
