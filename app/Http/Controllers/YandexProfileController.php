<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreYandexProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class YandexProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Settings/Index', [
            'profile' => $request->user()->yandexProfile,
        ]);
    }

    public function store(StoreYandexProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $profile = $user->yandexProfile()
            ->updateOrCreate(
                ['user_id' => $user->id],
                ['reviews_link' => $request->input('reviews_link')],
            );

        Cache::forget("yandex-parsed-{$profile->id}");

        return back()->with('status', __('Ссылка успешно сохранена'));
    }
}

