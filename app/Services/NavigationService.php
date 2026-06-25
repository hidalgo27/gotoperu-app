<?php
// gotoperu-app/app/Services/NavigationService.php

namespace App\Services;

use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\TPais;
use App\Models\TPaquete;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NavigationService
{
    /**
     * Construye toda la información necesaria
     * para el menú de navegación.
     *
     * Reglas:
     *
     * - is_p_t = 0: tour.
     * - is_p_t = 1: package.
     * - estado no controla la exposición del producto.
     * - La pertenencia al país se determina mediante destinos.
     * - Laravel devuelve route.key + route.params.
     * - Nuxt construye el URL final.
     */
    public function build(array $filters = []): array
    {
        $countrySlug = (string) (
            $filters['country'] ?? 'peru'
        );

        $sectionsLimit = $this->normalizeLimit(
            value: $filters['sections_limit'] ?? 8,
            default: 8,
            maximum: 20,
        );

        /*
         * Cantidad de productos dentro de cada sección.
         */
        $productsLimit = $this->normalizeLimit(
            value: $filters['products_limit'] ?? 4,
            default: 4,
            maximum: 12,
        );

        /*
         * Cantidad de paquetes y tours destacados.
         */
        $featuredLimit = $this->normalizeLimit(
            value: $filters['featured_limit'] ?? 12,
            default: 12,
            maximum: 20,
        );

        $countries = $this->getCountries();

        $country = $this->findCountry(
            countrySlug: $countrySlug,
        );

        if (!$country) {
            return [
                'countries' => $countries,
                'selected_country' => null,
                'destinations' => [],
                'categories' => [],

                'product_types' => $this->productTypes(
                    countrySlug: $countrySlug,
                ),

                'featured' => [
                    'packages' => [],
                    'tours' => [],
                ],

                'sections' => [],
                'selected_category' => null,

                'totals' => [
                    'products' => 0,
                    'packages' => 0,
                    'tours' => 0,
                    'destinations' => 0,
                    'categories' => 0,
                ],
            ];
        }

        /*
         * No se filtra por estado.
         */
        $destinations = $this->getDestinations(
            countryId: (int) $country->id,
        );

        /*
         * Se recuperan todos los productos relacionados
         * con destinos pertenecientes al país.
         *
         * No se filtra por estado.
         */
        $countryProducts = $this->getCountryProducts(
            countryId: (int) $country->id,
        );

        $packageProducts = $countryProducts
            ->filter(
                fn (TPaquete $package): bool =>
                    $this->resolveProductType($package) === 'package',
            )
            ->values();

        $tourProducts = $countryProducts
            ->filter(
                fn (TPaquete $package): bool =>
                    $this->resolveProductType($package) === 'tour',
            )
            ->values();

        $categories = $this->getCountryCategories(
            country: $country,
            products: $countryProducts,
        );

        $mappedCategories = $categories
            ->map(function (
                TCategoria $category,
            ) use (
                $countryProducts,
                $country,
            ): array {
                return $this->mapCategory(
                    category: $category,
                    countrySlug: $country->url,
                    productsCount: $this->countProductsByCategory(
                        products: $countryProducts,
                        categoryId: (int) $category->id,
                    ),
                );
            })
            ->values()
            ->all();

        $featuredPackages = $packageProducts
            ->take($featuredLimit)
            ->map(
                fn (TPaquete $package): array =>
                $this->mapPackage(
                    package: $package,
                    countrySlug: $country->url,
                ),
            )
            ->values()
            ->all();

        $featuredTours = $tourProducts
            ->take($featuredLimit)
            ->map(
                fn (TPaquete $package): array =>
                $this->mapPackage(
                    package: $package,
                    countrySlug: $country->url,
                ),
            )
            ->values()
            ->all();

        return [
            'countries' => $countries,

            'selected_country' => $this->mapCountry(
                country: $country,
            ),

            'destinations' => $destinations
                ->map(
                    fn (TDestino $destination): array =>
                    $this->mapDestination(
                        destination: $destination,
                        countrySlug: $country->url,
                    ),
                )
                ->values()
                ->all(),

            'categories' => $mappedCategories,

            'product_types' => $this->productTypes(
                countrySlug: $country->url,
            ),

            'featured' => [
                'packages' => $featuredPackages,
                'tours' => $featuredTours,
            ],

            'sections' => $this->buildCategorySections(
                categories: $categories,
                products: $countryProducts,
                countrySlug: $country->url,
                sectionsLimit: $sectionsLimit,
                productsLimit: $productsLimit,
            ),

            'selected_category' => null,

            /*
             * Permite comprobar rápidamente si el API
             * está exponiendo todos los registros esperados.
             */
            'totals' => [
                'products' => $countryProducts->count(),
                'packages' => $packageProducts->count(),
                'tours' => $tourProducts->count(),
                'destinations' => $destinations->count(),
                'categories' => $categories->count(),
            ],
        ];
    }

    /**
     * Obtiene todos los países.
     */
    public function getCountries(): array
    {
        return TPais::query()
            ->select([
                'id',
                'codigo',
                'nombre',
                'url',
                'imagen',
                'titulo',
                'title',
            ])
            ->orderBy('nombre')
            ->get()
            ->map(
                fn (TPais $country): array =>
                $this->mapCountry(
                    country: $country,
                ),
            )
            ->values()
            ->all();
    }

    /**
     * Busca un país mediante su slug.
     */
    public function findCountry(
        string $countrySlug,
    ): ?TPais {
        return TPais::query()
            ->select([
                'id',
                'codigo',
                'nombre',
                'url',
                'imagen',
                'titulo',
                'title',
                'population',
                'languages',
                'currency_name',
                'currency_code',
                'capital',
            ])
            ->where(
                'url',
                $countrySlug,
            )
            ->first();
    }

    /**
     * Obtiene todos los destinos del país.
     *
     * El campo estado no se usa como filtro.
     */
    public function getDestinations(
        int $countryId,
    ): Collection {
        return TDestino::query()
            ->select([
                'id',
                'idpais',
                'codigo',
                'nombre',
                'url',
                'imagen',
                'titulo',
                'resumen',
                'estado',
            ])
            ->where(
                'idpais',
                $countryId,
            )
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtiene todos los productos relacionados
     * con destinos del país.
     *
     * El campo estado no se usa como filtro.
     */
    public function getCountryProducts(
        int $countryId,
    ): Collection {
        return $this
            ->baseProductQuery()
            ->whereHas(
                'destinos',
                function (
                    Builder $query,
                ) use ($countryId): void {
                    $query->where(
                        'tdestinos.idpais',
                        $countryId,
                    );
                },
            )
            ->orderByDesc('tpaquetes.offers_home')
            ->orderByDesc('tpaquetes.id')
            ->get()
            ->unique('id')
            ->values();
    }

    /**
     * Obtiene las categorías del país que tienen
     * productos relacionados.
     *
     * No filtra por estado.
     */
    public function getCountryCategories(
        TPais $country,
        Collection $products,
    ): Collection {
        $categoryIdsWithProducts = $products
            ->flatMap(
                fn (TPaquete $package) =>
                $package->categorias->pluck('id'),
            )
            ->map(
                fn ($id): int => (int) $id,
            )
            ->unique()
            ->values();

        if ($categoryIdsWithProducts->isEmpty()) {
            return collect();
        }

        return $country
            ->categorias()
            ->select([
                'tcategoria.id',
                'tcategoria.nombre',
                'tcategoria.titulo',
                'tcategoria.url',
                'tcategoria.imagen',
                'tcategoria.imagen_banner',
                'tcategoria.resumen',
                'tcategoria.estado',
                'tcategoria.orden_block',
            ])
            ->whereIn(
                'tcategoria.id',
                $categoryIdsWithProducts->all(),
            )
            ->orderByRaw(
                'tcategoria.orden_block IS NULL',
            )
            ->orderBy('tcategoria.orden_block')
            ->orderBy('tcategoria.nombre')
            ->get()
            ->unique('id')
            ->values();
    }

    /**
     * Devuelve productos paginados
     * de una categoría y país.
     */
    public function getCategoryProducts(
        int $countryId,
        string $countrySlug,
        string $categorySlug,
        int $perPage,
        int $page = 1,
    ): ?array {
        $category = TCategoria::query()
            ->select([
                'tcategoria.id',
                'tcategoria.nombre',
                'tcategoria.titulo',
                'tcategoria.url',
                'tcategoria.imagen',
                'tcategoria.imagen_banner',
                'tcategoria.resumen',
                'tcategoria.estado',
                'tcategoria.orden_block',
            ])
            ->where(
                'tcategoria.url',
                $categorySlug,
            )
            ->whereHas(
                'paises',
                function (
                    Builder $query,
                ) use ($countryId): void {
                    $query->where(
                        'tpais.id',
                        $countryId,
                    );
                },
            )
            ->first();

        if (!$category) {
            return null;
        }

        $query = $this
            ->baseProductQuery()
            ->whereHas(
                'categorias',
                function (
                    Builder $query,
                ) use ($category): void {
                    $query->where(
                        'tcategoria.id',
                        $category->id,
                    );
                },
            )
            ->whereHas(
                'destinos',
                function (
                    Builder $query,
                ) use ($countryId): void {
                    $query->where(
                        'tdestinos.idpais',
                        $countryId,
                    );
                },
            )
            ->orderByDesc('tpaquetes.offers_home')
            ->orderByDesc('tpaquetes.id');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate(
            perPage: $perPage,
            columns: ['*'],
            pageName: 'page',
            page: $page,
        );

        $products = $paginator
            ->getCollection()
            ->map(
                fn (TPaquete $package): array =>
                $this->mapPackage(
                    package: $package,
                    countrySlug: $countrySlug,
                ),
            )
            ->values()
            ->all();

        return [
            'category' => $this->mapCategory(
                category: $category,
                countrySlug: $countrySlug,
                productsCount: $paginator->total(),
            ),

            'products' => $products,

            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ];
    }

    /**
     * Consulta base de paquetes y tours.
     *
     * No se filtra por estado.
     */
    private function baseProductQuery(): Builder
    {
        return TPaquete::query()
            ->select([
                'tpaquetes.id',
                'tpaquetes.codigo',
                'tpaquetes.titulo',
                'tpaquetes.precio_tours',
                'tpaquetes.url',
                'tpaquetes.duracion',
                'tpaquetes.estado',
                'tpaquetes.is_p_t',
                'tpaquetes.is_paquete',
                'tpaquetes.is_tours',
                'tpaquetes.offers_home',
                'tpaquetes.descuento',
                'tpaquetes.imagen',
            ])
            ->with([
                'categorias' => function ($query): void {
                    $query->select([
                        'tcategoria.id',
                        'tcategoria.nombre',
                        'tcategoria.url',
                    ]);
                },

                'destinos' => function ($query): void {
                    $query->select([
                        'tdestinos.id',
                        'tdestinos.idpais',
                        'tdestinos.codigo',
                        'tdestinos.nombre',
                        'tdestinos.url',
                    ]);
                },

                'precio_paquetes' => function ($query): void {
                    $query
                        ->select([
                            'id',
                            'idpaquetes',
                            'estrellas',
                            'precio_s',
                            'precio_d',
                            'precio_t',
                        ])
                        ->orderBy('estrellas');
                },
            ]);
    }

    /**
     * Construye las secciones agrupadas
     * por categoría.
     */
    private function buildCategorySections(
        Collection $categories,
        Collection $products,
        string $countrySlug,
        int $sectionsLimit,
        int $productsLimit,
    ): array {
        return $categories
            ->map(function (
                TCategoria $category,
            ) use (
                $products,
                $countrySlug,
                $productsLimit,
            ): array {
                $categoryProducts = $products
                    ->filter(
                        fn (TPaquete $package): bool =>
                        $package
                            ->categorias
                            ->contains(
                                fn ($item): bool =>
                                    (int) $item->id ===
                                    (int) $category->id,
                            ),
                    )
                    ->values();

                return [
                    'id' => (int) $category->id,
                    'name' => $category->nombre,

                    'title' => $category->titulo
                        ?: $category->nombre,

                    'slug' => $category->url,
                    'summary' => $category->resumen,
                    'image' => $category->imagen,
                    'banner_image' => $category->imagen_banner,

                    'order' => $category->orden_block !== null
                        ? (int) $category->orden_block
                        : null,

                    'products_count' =>
                        $categoryProducts->count(),

                    'view_all' => [
                        'label' => 'View all',

                        'route' => [
                            'key' => 'category.show',

                            'params' => [
                                'country' => $countrySlug,
                                'category' => $category->url,
                            ],
                        ],
                    ],

                    'products' => $categoryProducts
                        ->take($productsLimit)
                        ->map(
                            fn (TPaquete $package): array =>
                            $this->mapPackage(
                                package: $package,
                                countrySlug: $countrySlug,
                            ),
                        )
                        ->values()
                        ->all(),
                ];
            })
            ->filter(
                fn (array $section): bool =>
                    $section['products_count'] > 0,
            )
            ->take($sectionsLimit)
            ->values()
            ->all();
    }

    private function countProductsByCategory(
        Collection $products,
        int $categoryId,
    ): int {
        return $products
            ->filter(
                fn (TPaquete $package): bool =>
                $package
                    ->categorias
                    ->contains(
                        fn ($category): bool =>
                            (int) $category->id ===
                            $categoryId,
                    ),
            )
            ->count();
    }

    private function mapCountry(
        TPais $country,
    ): array {
        return [
            'id' => (int) $country->id,
            'code' => $country->codigo,
            'name' => $country->nombre,
            'slug' => $country->url,
            'image' => $country->imagen,

            'title' => $country->titulo
                ?: $country->title
                    ?: $country->nombre,

            'route' => [
                'key' => 'country.show',

                'params' => [
                    'country' => $country->url,
                ],
            ],
        ];
    }

    private function mapDestination(
        TDestino $destination,
        string $countrySlug,
    ): array {
        return [
            'id' => (int) $destination->id,
            'code' => $destination->codigo,
            'name' => $destination->nombre,
            'slug' => $destination->url,

            'title' => $destination->titulo
                ?: $destination->nombre,

            'summary' => $destination->resumen,
            'image' => $destination->imagen,

            'route' => [
                'key' => 'destination.show',

                'params' => [
                    'country' => $countrySlug,
                    'destination' => $destination->url,
                ],
            ],
        ];
    }

    private function mapCategory(
        TCategoria $category,
        string $countrySlug,
        ?int $productsCount = null,
    ): array {
        return [
            'id' => (int) $category->id,
            'name' => $category->nombre,

            'title' => $category->titulo
                ?: $category->nombre,

            'slug' => $category->url,
            'summary' => $category->resumen,
            'image' => $category->imagen,
            'banner_image' => $category->imagen_banner,

            'order' => $category->orden_block !== null
                ? (int) $category->orden_block
                : null,

            'products_count' => $productsCount,

            'route' => [
                'key' => 'category.show',

                'params' => [
                    'country' => $countrySlug,
                    'category' => $category->url,
                ],
            ],
        ];
    }

    /**
     * Mapper compacto de paquetes y tours.
     */
    private function mapPackage(
        TPaquete $package,
        string $countrySlug,
    ): array {
        $type = $this->resolveProductType(
            package: $package,
        );

        return [
            'id' => (int) $package->id,
            'code' => $package->codigo,
            'title' => $package->titulo,
            'slug' => $package->url,

            'duration' => $package->duracion !== null
                ? (int) $package->duracion
                : null,

            'type' => $type,
            'image' => $package->imagen,

            'price_from' => $this->resolvePriceFrom(
                package: $package,
            ),

            'discount' => $package->descuento !== null
                ? (float) $package->descuento
                : 0,

            'is_offer' => (bool) $package->offers_home,

            'categories' => $package
                ->categorias
                ->map(
                    fn ($category): array => [
                        'id' => (int) $category->id,
                        'name' => $category->nombre,
                        'slug' => $category->url,

                        'route' => [
                            'key' => 'category.show',

                            'params' => [
                                'country' => $countrySlug,
                                'category' => $category->url,
                            ],
                        ],
                    ],
                )
                ->values()
                ->all(),

            'destinations' => $package
                ->destinos
                ->map(
                    fn ($destination): array => [
                        'id' => (int) $destination->id,
                        'name' => $destination->nombre,
                        'slug' => $destination->url,

                        'route' => [
                            'key' => 'destination.show',

                            'params' => [
                                'country' => $countrySlug,
                                'destination' => $destination->url,
                            ],
                        ],
                    ],
                )
                ->values()
                ->all(),

            'route' => [
                'key' => $type === 'tour'
                    ? 'tour.show'
                    : 'package.show',

                'params' => [
                    'country' => $countrySlug,
                    'slug' => $package->url,
                ],
            ],
        ];
    }

    /**
     * Para tours usa primero precio_tours.
     *
     * Para paquetes usa primero precio_d.
     */
    private function resolvePriceFrom(
        TPaquete $package,
    ): ?float {
        if (
            $this->resolveProductType($package) === 'tour'
            && (float) $package->precio_tours > 0
        ) {
            return (float) $package->precio_tours;
        }

        $doublePrices = $package
            ->precio_paquetes
            ->pluck('precio_d')
            ->map(
                fn ($value): float =>
                (float) $value,
            )
            ->filter(
                fn (float $value): bool =>
                    $value > 0,
            );

        if ($doublePrices->isNotEmpty()) {
            return (float) $doublePrices->min();
        }

        $fallbackPrices = $package
            ->precio_paquetes
            ->flatMap(
                fn ($price): array => [
                    (float) ($price->precio_s ?? 0),
                    (float) ($price->precio_t ?? 0),
                ],
            )
            ->filter(
                fn (float $value): bool =>
                    $value > 0,
            );

        return $fallbackPrices->isNotEmpty()
            ? (float) $fallbackPrices->min()
            : null;
    }

    /**
     * is_p_t = 0: tour.
     * is_p_t = 1 o NULL: package.
     */
    private function resolveProductType(
        TPaquete $package,
    ): string {
        return (int) $package->is_p_t === 0
            ? 'tour'
            : 'package';
    }

    /**
     * Entradas principales del menú.
     */
    private function productTypes(
        string $countrySlug,
    ): array {
        return [
            [
                'key' => 'package',
                'label' => 'Travel Packages',

                'route' => [
                    'key' => 'package.index',

                    'params' => [
                        'country' => $countrySlug,
                    ],
                ],
            ],

            [
                'key' => 'tour',
                'label' => 'Tours',

                'route' => [
                    'key' => 'tour.index',

                    'params' => [
                        'country' => $countrySlug,
                    ],
                ],
            ],

            [
                'key' => 'destination',
                'label' => 'Destinations',

                'route' => [
                    'key' => 'destination.index',

                    'params' => [
                        'country' => $countrySlug,
                    ],
                ],
            ],
        ];
    }

    private function normalizeLimit(
        mixed $value,
        int $default,
        int $maximum,
    ): int {
        $normalizedValue = filter_var(
            $value,
            FILTER_VALIDATE_INT,
        );

        if (
            $normalizedValue === false
            || $normalizedValue < 1
        ) {
            return $default;
        }

        return min(
            $normalizedValue,
            $maximum,
        );
    }
}
