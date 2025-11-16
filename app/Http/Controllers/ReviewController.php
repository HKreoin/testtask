<?php

namespace App\Http\Controllers;

use App\Services\YandexParsingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function __construct(private YandexParsingService $parsingService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $profile = $request->user()->yandexProfile;

        $summary = ['average' => null, 'count' => 0];
        $reviews = collect();

        if ($profile) {
            ['summary' => $summary, 'reviews' => $reviews] = $this->parsingService->fetch($profile);
        }

        return Inertia::render('Reviews/Index', [
            'summary' => $summary,
            'reviews' => $reviews->values(),
            'profileExists' => (bool) $profile,
        ]);
    }
}

