<?php
// gotoperu-app/app/Http/Controllers/Page/NavigationController.php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Services\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class NavigationController extends Controller
{
    public function __construct(
        private readonly NavigationService $navigationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],

            'category' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],

            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'sections_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            /*
             * Cantidad de productos mostrados
             * dentro de cada sección.
             */
            'products_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],

            /*
             * Cantidad máxima de paquetes y tours
             * mostrados dentro de featured.
             */
            'featured_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'refresh' => [
                'nullable',
                'boolean',
            ],
        ]);

        try {
            $countrySlug = $validated['country'] ?? 'peru';
            $categorySlug = $validated['category'] ?? null;

            $page = (int) ($validated['page'] ?? 1);
            $perPage = (int) ($validated['per_page'] ?? 12);

            $sectionsLimit = (int) (
                $validated['sections_limit'] ?? 8
            );

            $productsLimit = (int) (
                $validated['products_limit'] ?? 4
            );

            $featuredLimit = (int) (
                $validated['featured_limit'] ?? 12
            );

            $refresh = (bool) (
                $validated['refresh'] ?? false
            );

            $cacheParameters = [
                'country' => $countrySlug,
                'category' => $categorySlug,
                'page' => $page,
                'per_page' => $perPage,
                'sections_limit' => $sectionsLimit,
                'products_limit' => $productsLimit,
                'featured_limit' => $featuredLimit,

                /*
                 * Versión nueva porque estado ya no
                 * participa como filtro.
                 */
                'response_version' => 4,
            ];

            $cacheKey = 'navigation:' . sha1(
                    json_encode(
                        $cacheParameters,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES,
                    ),
                );

            if ($refresh) {
                Cache::forget($cacheKey);
            }

            $data = Cache::remember(
                $cacheKey,
                now()->addMinutes(30),
                function () use (
                    $countrySlug,
                    $categorySlug,
                    $page,
                    $perPage,
                    $sectionsLimit,
                    $productsLimit,
                    $featuredLimit,
                ): array {
                    $navigation = $this
                        ->navigationService
                        ->build([
                            'country' => $countrySlug,
                            'sections_limit' => $sectionsLimit,
                            'products_limit' => $productsLimit,
                            'featured_limit' => $featuredLimit,
                        ]);

                    $selectedCountry =
                        $navigation['selected_country'] ?? null;

                    if (!$selectedCountry || !$categorySlug) {
                        return $navigation;
                    }

                    $navigation['selected_category'] = $this
                        ->navigationService
                        ->getCategoryProducts(
                            countryId: (int) $selectedCountry['id'],
                            countrySlug: $countrySlug,
                            categorySlug: $categorySlug,
                            perPage: $perPage,
                            page: $page,
                        );

                    return $navigation;
                },
            );

            if (empty($data['selected_country'])) {
                return response()->json([
                    'message' =>
                        'El país solicitado no fue encontrado.',

                    'data' => null,
                ], 404);
            }

            if (
                $categorySlug
                && empty($data['selected_category'])
            ) {
                return response()->json([
                    'message' =>
                        'La categoría solicitada no existe o no pertenece al país.',

                    'data' => null,
                ], 404);
            }

            return response()->json([
                'data' => $data,

                'meta' => [
                    'country' => $countrySlug,
                    'category' => $categorySlug,

                    'page' => $categorySlug
                        ? $page
                        : null,

                    'per_page' => $categorySlug
                        ? $perPage
                        : null,

                    'limits' => [
                        'sections' => $sectionsLimit,
                        'products_per_section' => $productsLimit,
                        'featured_per_type' => $featuredLimit,
                    ],

                    'cached_for_minutes' => 30,

                    'routing' => [
                        'strategy' => 'frontend',
                        'version' => 1,
                    ],

                    'contract_version' => 4,
                ],
            ]);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'message' =>
                    'No se pudo obtener el menú de navegación.',

                'error' => config('app.debug')
                    ? $throwable->getMessage()
                    : null,
            ], 500);
        }
    }
}
