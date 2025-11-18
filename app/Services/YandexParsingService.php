<?php

namespace App\Services;

use App\Models\YandexProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class YandexParsingService
{
    public function fetch(YandexProfile $profile): array
    {
        $cacheKey = "yandex-parsed-{$profile->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($profile) {
            $html = $this->fetchHtml($profile->reviews_link);

            if (! $html) {
                return ['summary' => null, 'reviews' => collect()];
            }

            // Всегда используем DOM-парсинг
            $result = $this->extractFromDom($html);
            if ($result['summary'] || $result['reviews']->isNotEmpty()) {
                return $result;
            }
            return ['summary' => null, 'reviews' => collect()];
        });
    }

    protected function fetchHtml(string $url): ?string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        ])->get($url);

        if ($response->failed()) {
            return null;
        }
        Log::info('YandexParsingService: HTML fetched via HTTP client');
        return $response->body();
    }

    protected function extractFromDom(string $html): array
    {
        $crawler = new Crawler($html);

        $average = $this->parseTotalRatingFromDom($crawler);
        $count = $this->parseTotalReviewsFromDom($crawler);
        $reviews = $this->parseReviewsFromDom($crawler);

        $summary = null;
        if ($average || $count) {
            $summary = ['average' => $average ?: null, 'count' => $count ?: 0];
        }

        return ['summary' => $summary, 'reviews' => $reviews];
    }

    protected function parseTotalRatingFromDom(Crawler $crawler): float
    {
        $selectors = [
            '[class*="rating-value"]',
            '.business-summary-rating-badge__rating-text',
            '.business-rating-badge-view__rating-text',
            '.card-rating-score',
            '[itemprop="ratingValue"]',
            '.orgpage-header-view__rating-text',
            '.rating-badge__text',
            '.business-header-rating__rating-text',
        ];

        foreach ($selectors as $selector) {
            try {
                if ($crawler->filter($selector)->count() > 0) {
                    $node = $crawler->filter($selector)->first();
                    $text = trim($node->text(''));
                    if ($text === '') {
                        $text = (string) ($node->attr('content') ?? '');
                    }
                    $value = (float) str_replace(',', '.', $text);
                    if ($value > 0) {
                        return $value;
                    }
                }
            } catch (\Throwable $e) {
                // ignore selector errors
            }
        }

        return 0.0;
    }

    protected function parseTotalReviewsFromDom(Crawler $crawler): int
    {
        $selectors = [
            '[class*="review-count"]',
            '.business-review-rating-badge__review-count',
            '.business-reviews-block-view__header-counter',
            '.tabs-select-view__count',
            '[itemprop="reviewCount"]',
            '.business-header-rating__reviews-count',
            '.rating-badge__count',
        ];

        foreach ($selectors as $selector) {
            try {
                if ($crawler->filter($selector)->count() > 0) {
                    $node = $crawler->filter($selector)->first();
                    $text = trim($node->text(''));
                    if ($text === '') {
                        $text = (string) ($node->attr('content') ?? '');
                    }
                    if (preg_match('/\d[\d\s]*/u', $text, $m)) {
                        return (int) preg_replace('/\D+/', '', $m[0]);
                    }
                }
            } catch (\Throwable $e) {
                // ignore selector errors
            }
        }

        return 0;
    }

    protected function parseReviewsFromDom(Crawler $crawler): Collection
    {
        $reviewSelectors = [
            '.business-reviews-card-view__review',
            '.business-reviews-card-view__item',
            '.business-reviews-view__review',
            '.business-review',
            '.review-item',
            '[class*="review-view"]',
            '.review-card',
        ];

        $placeName = $this->guessPlaceName($crawler);

        foreach ($reviewSelectors as $selector) {
            try {
                $nodes = $crawler->filter($selector);
                if ($nodes->count() > 0) {
                    $mapped = collect($nodes->each(function (Crawler $node) use ($placeName) {
                        $author = $this->extractFirstText($node, [
                            '.business-review-view__author',
                            '.review-author',
                            '.user-name',
                            '[class*="author-name"]',
                            '.business-review-view__author .link__text',
                            '.review-view__author-name',
                            '.user-link__name',
                        ]) ?? __('Аноним');

                        $text = $this->extractFirstText($node, [
                            '.business-review-view__body',
                            '.review-text',
                            '.business-review-view__text',
                            '.review-content',
                            '[class*="review-text"]',
                            '.business-review-view__body-text',
                            '.review-view__text',
                        ]);

                        if (trim((string) $text) === '') {
                            return null;
                        }

                        $rating = $this->extractRatingFromNode($node);
                        $dateIso = $this->extractDateFromNode($node);
                        $timestamp = $dateIso ? strtotime($dateIso) : null;

                        return [
                            'id' => (string) Str::uuid(),
                            'author' => $author,
                            'rating' => (int) round($rating),
                            'date' => $timestamp ? date('d.m.Y', $timestamp) : null,
                            'time' => $timestamp ? date('H:i', $timestamp) : null,
                            'place' => $placeName,
                            'phone' => '',
                            'text' => trim((string) $text),
                        ];
                    }))->filter();

                    if ($mapped->isNotEmpty()) {
                        return $mapped->take(10)->values();
                    }
                }
            } catch (\Throwable $e) {
                // ignore selector errors
            }
        }

        return collect();
    }

    protected function guessPlaceName(Crawler $crawler): string
    {
        // 1) Явные заголовки и itemprop
        $candidates = [
            '.orgpage-header-view__title',
            '.business-header-view__title',
            'h1[itemprop="name"]',
            '[itemprop="name"]',
            'h1',
        ];
        foreach ($candidates as $selector) {
            try {
                if ($crawler->filter($selector)->count() > 0) {
                    $text = trim($crawler->filter($selector)->first()->text());
                    if ($text !== '') {
                        return $this->sanitizePlaceName($text);
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 2) og:title / meta
        try {
            $metaSelectors = [
                'meta[property="og:title"]',
                'meta[name="og:title"]',
            ];
            foreach ($metaSelectors as $metaSel) {
                if ($crawler->filter($metaSel)->count() > 0) {
                    $content = trim((string) $crawler->filter($metaSel)->first()->attr('content'));
                    if ($content !== '') {
                        return $this->sanitizePlaceName($content);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // 3) <title>
        try {
            if ($crawler->filter('title')->count() > 0) {
                $title = trim($crawler->filter('title')->first()->text());
                if ($title !== '') {
                    return $this->sanitizePlaceName($title);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return __('Компания');
    }

    protected function sanitizePlaceName(string $raw): string
    {
        $name = trim($raw);
        // Удаляем типичные хвосты “— отзывы”, “- отзывы”, “Отзывы — …”
        $name = preg_replace('/\s*[—\-]\s*отзывы.*$/iu', '', $name);
        $name = preg_replace('/^отзывы\s*[—\-]\s*/iu', '', $name);
        // Убираем лишние пробелы
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name) !== '' ? trim($name) : __('Компания');
    }

    protected function extractFirstText(Crawler $node, array $selectors): ?string
    {
        foreach ($selectors as $selector) {
            try {
                if ($node->filter($selector)->count() > 0) {
                    return trim($node->filter($selector)->first()->text());
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
        return null;
    }

    protected function extractRatingFromNode(Crawler $node): float
    {
        try {
            if ($node->filter('meta[itemprop="ratingValue"]')->count() > 0) {
                $val = (float) ($node->filter('meta[itemprop="ratingValue"]')->first()->attr('content') ?? 0);
                if ($val >= 0.5 && $val <= 5) {
                    return $val;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            if ($node->filter('.business-rating-badge-view__stars')->count() > 0) {
                $aria = (string) ($node->filter('.business-rating-badge-view__stars')->first()->attr('aria-label') ?? '');
                if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:из|Из)\s*5/u', $aria, $m)) {
                    return (float) str_replace(',', '.', $m[1]);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Подсчёт заполненных звёзд, если встречаются соответствующие классы
        try {
            $candidates = [
                '.i-stars__rating',
                '.mini-stars-group',
                '[class*="stars"]',
            ];
            foreach ($candidates as $selector) {
                if ($node->filter($selector)->count() > 0) {
                    $container = $node->filter($selector)->first();
                    $filled = $container->filter('[class*="_filled"], .i-star_filled, .filled')->count();
                    if ($filled > 0 && $filled <= 5) {
                        return (float) $filled;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 0.0;
    }

    protected function extractDateFromNode(Crawler $node): ?string
    {
        try {
            if ($node->filter('meta[itemprop="datePublished"]')->count() > 0) {
                return (string) $node->filter('meta[itemprop="datePublished"]')->first()->attr('content');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $selectors = [
            '.business-review-view__date span',
            '.business-review-view__date',
            '.review-date',
        ];
        foreach ($selectors as $selector) {
            try {
                if ($node->filter($selector)->count() > 0) {
                    $text = trim($node->filter($selector)->first()->text());
                    return $this->parseRelativeRuDate($text);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return null;
    }

    protected function parseRelativeRuDate(string $dateText): string
    {
        $dateText = mb_strtolower(trim($dateText));
        $now = now();

        if ($dateText === 'сегодня') {
            return $now->format('Y-m-d H:i:s');
        }
        if ($dateText === 'вчера') {
            return $now->copy()->subDay()->format('Y-m-d H:i:s');
        }

        $months = [
            'января' => '01', 'февраля' => '02', 'марта' => '03',
            'апреля' => '04', 'мая' => '05', 'июня' => '06',
            'июля' => '07', 'августа' => '08', 'сентября' => '09',
            'октября' => '10', 'ноября' => '11', 'декабря' => '12',
        ];

        foreach ($months as $ru => $num) {
            if (str_contains($dateText, $ru)) {
                if (! preg_match('/(\d{1,2})/u', $dateText, $m)) {
                    break;
                }
                $day = (int) $m[1];
                $year = (int) $now->year;
                $candidate = \DateTime::createFromFormat('Y-m-d', sprintf('%d-%s-%02d', $year, $num, $day));
                if ($candidate && $candidate > $now) {
                    $year--;
                }
                return sprintf('%d-%s-%02d 00:00:00', $year, $num, $day);
            }
        }

        // d.m.Y
        if (preg_match('/\b(\d{1,2})\.(\d{1,2})\.(\d{4})\b/u', $dateText, $m)) {
            return sprintf('%04d-%02d-%02d 00:00:00', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        return $now->format('Y-m-d H:i:s');
    }
}

