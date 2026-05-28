<?php

namespace App\Http\Controllers\User;

use App\Enums\LoanStatus;
use App\Enums\ReviewStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\ReviewSubmittedAdminMail;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Review::class, 'review');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::query()
            ->where('user_id', auth()->id())
            ->with(['book'])
            ->paginate(10, ['*'], 'pag');

        return Inertia::render('User/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
        ]);

        $loan = Loan::query()
            ->where('id', $validated['loan_id'])
            ->with(['book'])
            ->firstOrFail();

        return Inertia::render('User/Reviews/Create', [
            'loan' => $loan,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'review_title' => ['required', 'string', 'min:5', 'max:255'],
            'review_text' => ['required', 'string', 'min:10', 'max:10000'],
            'rating' => ['required', 'numeric', 'between:1,10'],
            'loan_id' => [
                'required',
                'integer',
                Rule::exists('loans', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', auth()->id())
                        ->where('book_id', $request->book_id)
                        ->where('status', LoanStatus::RETURNED);
                }),
            ],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = ReviewStatus::PENDING;

        $review = Review::create($validated);

        $admins = User::where('role', UserRole::ADMIN->value)->get();
        foreach ($admins as $index => $admin) {
            try {
                Mail::to($admin)->later(now()->addSeconds(5), new ReviewSubmittedAdminMail($review));
            } catch (\Exception $e) {
                Log::error('Failed to queue ReviewSubmittedAdminMail', [
                    'admin_id' => $admin->id,
                    'review_id' => $review->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Opinião enviada para moderação.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return Inertia::render('User/Reviews/Show', [
            'review' => $review->load('book', 'user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $userId = auth()->id();
        $book = $review->book;
        $loan = $review->loan;

        $userReview = Review::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        return Inertia::render('User/Reviews/Edit', [
            'book' => $book->load([
                'authors',
                'publisher',
                'reviews' => function ($query) {
                    $query->with('user')->where('status', ReviewStatus::APPROVED)->latest();
                },
            ]),
            'loan' => $loan->load('book.authors', 'book.publisher', 'user'),
            'userReview' => $userReview,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'review_title' => ['required', 'string', 'min:5', 'max:255'],
            'review_text' => ['required', 'string', 'min:10', 'max:10000'],
            'rating' => ['required', 'numeric', 'between:1,10'],
        ]);

        $validated['status'] = ReviewStatus::PENDING;
        $validated['rejection_reason'] = null;

        $review->update($validated);

        $admins = User::where('role', UserRole::ADMIN->value)->get();
        foreach ($admins as $index => $admin) {
            try {
                $delay = 3 + ($index * 3);
                Mail::to($admin)->later(now()->addSeconds($delay), new ReviewSubmittedAdminMail($review));
            } catch (\Exception $e) {
                Log::error('Failed to queue ReviewSubmittedAdminMail on update', [
                    'admin_id' => $admin->id,
                    'review_id' => $review->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Opinião atualizada e enviada para moderação.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('user.reviews.index');
    }
}
