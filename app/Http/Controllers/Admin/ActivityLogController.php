<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'email')])
            // Pesquisa
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('object_id', 'like', "%{$search}%");
                });
            })
            // Filtro de Módulo
            ->when($request->module, function ($query, $module) {
                $query->where('module', $module);
            })
            // Ordenação
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'data_asc' => $query->orderBy('created_at', 'asc'),
                    'data_desc' => $query->orderBy('created_at', 'desc'),
                    'acao_az' => $query->orderBy('action', 'asc'),
                    'acao_za' => $query->orderBy('action', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(50, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'module']);

        if (! in_array($filters['sort'] ?? null, ['data_asc', 'data_desc', 'acao_az', 'acao_za'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $logs,
            'filters' => $filters,
        ]);
    }
}
