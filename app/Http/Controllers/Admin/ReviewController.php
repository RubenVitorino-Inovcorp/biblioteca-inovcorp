<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ReviewController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Review::class, 'review');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $reviews = Review::query()
            ->with(['book', 'user'])
            ->latest()
            ->paginate(10, ['*'], 'pag');

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'pending_reviews' => Review::query()->where('status', ReviewStatus::PENDING)->with('book', 'user')->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review): Response
    {
        return Inertia::render('Reviews/Show', [
            'review' => $review->load(['book', 'user']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Opinião removida com sucesso.');
    }

    public function approve(Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update([
            'status' => ReviewStatus::APPROVED,
            'rejection_reason' => null,
        ]);

        // TODO: Enviar email de notificação de aprovação ao Cidadão

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Opinião aprovada com sucesso e publicada no catálogo.');
    }

    public function reject(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $review->update([
            'status' => ReviewStatus::REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // TODO: Enviar email de notificação de rejeição com a $validated['rejection_reason']

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Opinião rejeitada com sucesso.');
    }
}
