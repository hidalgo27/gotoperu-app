<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\THotel;
use App\Models\THotelDestino;
use App\Models\TInquire;
use App\Models\TPais;
use App\Models\TPaquete;
use App\Models\TPaqueteCategoria;
use App\Models\TPaqueteDestino;
use App\Models\TPost;
use App\Models\TTeam;
use App\Models\TTestimonio;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function packages(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes', 'imagen_paquetes', 'paquetes_categoria.categoria')->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function packages_top(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes', 'paquetes_categoria.categoria')
                ->where('estado', '1')
                ->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function packages_offers(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes', 'paquetes_categoria.categoria')
                ->where('offers_home', '1')
                ->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function packages_detail($latam,$url) {
        try {
            $paquetes = TPaquete::with('paquete_itinerario.itinerarios.itinerario_imagen', 'paquetes_destinos.destinos.pais','paquetes_categoria.categoria','paquetes_destinos.destinos.destino_imagen', 'paquetes_destinos.destinos.hoteles', 'precio_paquetes', 'imagen_paquetes')->where('url', $url)->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function hotels_destinations() {
        try {
            $hoteles = THotel::with('hotel_destinos.destinos','imagenes')->get();
            return response()->json($hoteles, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function destinations_hotels() {
        try {
            // Obtener todos los destinos con sus hoteles relacionados
            $destinos = TDestino::with('hoteles')->get();

            return response()->json($destinos, 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function team(){
        try {
            $teams = TTeam::with([
                'destinos:id,codigo,nombre,url',
                'paises:id,codigo,nombre,url'
            ])->select(
                'id', 'nombre', 'actividad', 'cargo', 'frase', 'email', 'descripcion',
                'fun_facts', 'favorite_quote', 'favorite_travel_memory', 'imagen_perfil', 'imagen_portada'
            )->get();

            return response()->json([
                'teams' => $teams->map(function ($team) {
                    return [
                        'id' => $team->id,
                        'nombre' => $team->nombre,
                        'actividad' => $team->actividad,
                        'cargo' => $team->cargo,
                        'frase' => $team->frase,
                        'email' => $team->email,
                        'descripcion' => $team->descripcion,
                        'fun_facts' => $team->fun_facts,
                        'favorite_quote' => $team->favorite_quote,
                        'favorite_travel_memory' => $team->favorite_travel_memory,
                        'imagen_perfil' => $team->imagen_perfil,
                        'imagen_portada' => $team->imagen_portada,
                        'destinos' => $team->destinos,
                        'paises' => $team->paises
                    ];
                })
            ]);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function team_show(TTeam $team){
        try {
//            $team = TTeam::orderBy('id', 'desc')->get();
//
//            return response()->json($team);

            $team = TTeam::with([
                'destinos' => function ($query) {
                    $query->select(
                        'tdestinos.id',  // Se especifica la tabla
                        'tdestinos.codigo',
                        'tdestinos.nombre',
                        'tdestinos.url',
                        'tdestinos.imagen',
                        'tdestinos.idpais'
                    )->with([
                        'pais:id,codigo,nombre,url,imagen' // Se mantiene la relación del país
                    ]);
                },
                'paises:id,codigo,nombre,url,imagen'
            ])->select(
                'tteam.id',  // Se especifica la tabla para evitar ambigüedades
                'tteam.nombre',
                'tteam.actividad',
                'tteam.cargo',
                'tteam.frase',
                'tteam.email',
                'tteam.descripcion',
                'tteam.fun_facts',
                'tteam.favorite_quote',
                'tteam.favorite_travel_memory',
                'tteam.imagen_perfil',
                'tteam.imagen_portada'
            )->find($team->id);

            if (!$team) {
                return response()->json([
                    'error' => 'Miembro del equipo no encontrado'
                ], 404);
            }

            return response()->json([
                'team' => [
                    'id' => $team->id,
                    'nombre' => $team->nombre,
                    'actividad' => $team->actividad,
                    'cargo' => $team->cargo,
                    'frase' => $team->frase,
                    'email' => $team->email,
                    'descripcion' => $team->descripcion,
                    'fun_facts' => $team->fun_facts,
                    'favorite_quote' => $team->favorite_quote,
                    'favorite_travel_memory' => $team->favorite_travel_memory,
                    'imagen_perfil' => $team->imagen_perfil,
                    'imagen_portada' => $team->imagen_portada,
                    'destinos' => $team->destinos->map(function ($destino) {
                        return [
                            'id' => $destino->id,
                            'codigo' => $destino->codigo,
                            'nombre' => $destino->nombre,
                            'url' => $destino->url,
                            'imagen' => $destino->imagen,
                            'pais' => $destino->pais // Aquí se agrega el país dentro del destino
                        ];
                    }),
                    'paises' => $team->paises
                ]
            ]);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function team_destino(TDestino $destino)
    {
        // Obtener el destino con sus equipos
        $destino = TDestino::with('teams:id,nombre,actividad,cargo,frase,email,descripcion,fun_facts,favorite_quote,favorite_travel_memory,imagen_perfil,imagen_portada')
            ->select('id', 'codigo', 'nombre', 'url')
            ->find($destino->id);

        if (!$destino) {
            return response()->json([
                'error' => 'Destino no encontrado'
            ], 404);
        }

        return response()->json([
            'destino' => [
                'id' => $destino->id,
                'codigo' => $destino->codigo,
                'nombre' => $destino->nombre,
                'url' => $destino->url,
                'teams' => $destino->teams
            ]
        ]);
    }

    public function team_country(TPais $country)
    {
        // Obtener el país con sus equipos
        $pais = TPais::with('teams:id,nombre,actividad,cargo,frase,email,descripcion,fun_facts,favorite_quote,favorite_travel_memory,imagen_perfil,imagen_portada')
            ->select('id', 'codigo', 'nombre', 'url','imagen')
            ->find($country->id);

        if (!$pais) {
            return response()->json([
                'error' => 'País no encontrado'
            ], 404);
        }

        return response()->json([
            'pais' => [
                'id' => $pais->id,
                'codigo' => $pais->codigo,
                'nombre' => $pais->nombre,
                'url' => $pais->url,
                'imagen' => $pais->imagen,
                'teams' => $pais->teams
            ]
        ]);
    }

    public function pais(){
        try {
            $pais = TPais::with('destino')->get();
            return response()->json($pais, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function destinations(TPais $pais){
        try {
            $destinations = TDestino::where('idpais',$pais->id)->get();
            return response()->json($destinations, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function destinations_show(TPais $pais, TDestino $destinos){

        try {
//            $paquetes_api = TPaqueteDestino::
//            with('paquetes.precio_paquetes','destinos', 'paquetes.paquetes_destinos.destinos','paquetes.paquetes_categoria.categoria')
//                ->where('iddestinos', $destinos->id)
//                ->get();
//            return response()->json($paquetes_api, 200);

            $destino = TDestino::with(['pais:id,codigo,nombre,url,population,languages,currency_name,currency_code,capital','imagenes:id,iddestinos,nombre,alt','posts:id,titulo,url,estado,imagen_miniatura,categoria_id'])
                ->select('id', 'codigo', 'nombre', 'url','titulo','resumen','descripcion','imagen','wtext','wimage','wtitle','idpais','longitud','latitud')
                ->find($destinos->id);

            if (!$destino) {
                return response()->json([
                    'error' => 'Destino no encontrado'
                ], 404);
            }

            // Obtener los paquetes relacionados con el destino
            $paquetes = $destino->paquetes()->select(
                'tpaquetes.id',
                'tpaquetes.titulo',
                'tpaquetes.url',
                'tpaquetes.duracion',
                'tpaquetes.estado',
                'tpaquetes.offers_home',
                'tpaquetes.descuento',
                'tpaquetes.imagen'
            )
                ->with([
                    'categorias:id,nombre,url',
                    'destinos:id,codigo,nombre,url',
                    'precio_paquetes'
                ])->get()->map(function ($paquete) {
                    return [
                        'id' => $paquete->id,
                        'titulo' => $paquete->titulo,
                        'duracion' => $paquete->duracion,
                        'url' => $paquete->url,
                        'estado' => $paquete->estado,
                        'offers_home' => $paquete->offers_home,
                        'descuento' => $paquete->descuento,
                        'imagen' => $paquete->imagen,
                        'categorias' => $paquete->categorias,
                        'precio_paquetes' => $paquete->precio_paquetes,
                        'destinos' => $paquete->destinos->map(function ($dest) {
                            return [
                                'id' => $dest->id,
                                'codigo' => $dest->codigo,
                                'nombre' => $dest->nombre,
                                'url' => $dest->url
                            ];
                        }),
                    ];
                });


            return response()->json([
                'destino' => [
                    'id' => $destino->id,
                    'codigo' => $destino->codigo,
                    'nombre' => $destino->nombre,
                    'url' => $destino->url,
                    'titulo' => $destino->titulo,
                    'resumen' => $destino->resumen,
                    'descripcion' => $destino->descripcion,
                    'imagen' => $destino->imagen,
                    'wtitle' => $destino->wtitle,
                    'wtext' => $destino->wtext,
                    'wimage' => $destino->wimage,
                    'pais' => $destino->pais,
                    'longitud' => $destino->longitud,
                    'latitud' => $destino->latitud,
                    'imagenes' => $destino->imagenes,
                    'paquetes' => $paquetes,
                    'posts' => $destino->posts
                ]
            ]);

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function categories_show(TCategoria $categoria){

        try {
            $categoria = TCategoria::with('paquetes.paquetes_categoria.categoria')->find($categoria->id);
            return response()->json($categoria, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function packages_by_country_and_category(TPais $pais, TCategoria $categoria)
    {
        $pais = TPais::with('destino.paquetes.categorias')->find($pais->id);

        if (!$pais) {
            return response()->json([
                'error' => 'País no encontrado'
            ], 404);
        }

        $categoria = TCategoria::find($categoria->id);

        if (!$categoria) {
            return response()->json([
                'error' => 'Categoría no encontrada'
            ], 404);
        }

        // Filtrar los paquetes que pertenecen al país y la categoría
        $paquetes = collect();
        foreach ($pais->destino as $destino) {
            foreach ($destino->paquetes as $paquete) {
                if ($paquete->categorias->contains('id', $categoria->id)) {
                    $paquetes->push($paquete);
                }
            }
        }

        return response()->json([
            'pais' => $pais->nombre,
            'categoria' => $categoria->nombre,
            'paquetes' => $paquetes->unique('id')->values()
        ]);
    }

    public function packages_by_country(TPais $pais)
    {
        $pais = TPais::with('destino.paquetes.paquetes_categoria.categoria','destino.paquetes.precio_paquetes', 'destino.paquetes.paquetes_destinos.destinos')->find($pais->id);

        if (!$pais) {
            return response()->json([
                'error' => 'País no encontrado'
            ], 404);
        }

        // Extraer todos los paquetes asociados al país
        $paquetes = collect();
        foreach ($pais->destino as $destino) {
            foreach ($destino->paquetes as $paquete) {
                $paquetes->push($paquete);
            }
        }

        return response()->json([
            'pais' => $pais->nombre,
            'paquetes' => $paquetes->unique('id')->values()
        ]);
    }

    public function country_properties2(TPais $country){
        try {
            $pais = TPais::with('propiedades', 'destino')->find($country->id);

            if (!$pais) {
                return response()->json(['message' => 'País no encontrado'], 404);
            }

            return response()->json([
                'pais' => $pais->nombre,
                'resumen' => $pais->resumen,
                'imagen' => $pais->imagen,
                'propiedades' => $pais->propiedades
            ], 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function CountryDetails(TPais $country)
    {

        $pais = TPais::with([
            'destino' => function ($query) {
                $query->select('id', 'idpais', 'codigo', 'nombre','url','imagen')->with('imagenes:id,iddestinos,nombre,alt','posts:id,titulo,url,imagen_miniatura,categoria_id');
            },
            'propiedades:id,idpais,nombre,descripcion,imagen'
        ])->find($country->id);

        if (!$pais) {
            return response()->json([
                'error' => 'País no encontrado'
            ], 404);
        }

        $paquetes = collect();
        foreach ($pais->destino as $destino) {
            foreach ($destino->paquetes()->select(
                'tpaquetes.id',
                'tpaquetes.titulo',
                'tpaquetes.url',
                'tpaquetes.duracion',
                'tpaquetes.estado',
                'tpaquetes.offers_home',
                'tpaquetes.descuento',
                'tpaquetes.imagen'
            )->with([
                'categorias:id,nombre,url',
                'destinos:id,codigo,nombre,url',
                'precio_paquetes'
            ])->get() as $paquete) {
                $paquetes->push([
                    'id' => $paquete->id,
                    'titulo' => $paquete->titulo,
                    'duracion' => $paquete->duracion,
                    'url' => $paquete->url,
                    'estado' => $paquete->estado,
                    'offers_home' => $paquete->offers_home,
                    'descuento' => $paquete->descuento,
                    'imagen' => $paquete->imagen,
                    'categorias' => $paquete->categorias,
                    'precio_paquetes' => $paquete->precio_paquetes,
                    'destinos' => $paquete->destinos->map(function ($dest) {
                        return [
                            'id' => $dest->id,
                            'codigo' => $dest->codigo,
                            'nombre' => $dest->nombre,
                            'url' => $dest->url
                        ];
                    }),
                ]);
            }
        }

        return response()->json([
            'pais' => [
                'id' => $pais->id,
                'nombre' => $pais->nombre,
                'titulo' => $pais->titulo,
                'codigo' => $pais->codigo,
                'url' => $pais->url,
                'resumen' => $pais->resumen,
                'descripcion' => $pais->descripcion,
                'imagen' => $pais->imagen,
                'imagen_s' => $pais->imagen_s,
                'population' => $pais->population,
                'languages' => $pais->languages,
                'currency_name' => $pais->currency_name,
                'currency_code' => $pais->currency_code,
                'capital' => $pais->capital,
                'propiedades' => $pais->propiedades,
                'destinos' => $pais->destino,
                'paquetes' => $paquetes->unique('titulo')->values(),
                'posts' => $pais->posts
            ]
        ]);

    }

    public function country(TPais $country){
        try {
            return response()->json($country, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function offers_country(TPais $country){
        try {
            // Obtener el país con sus destinos
            $pais = TPais::with('destino')->find($country->id);

            if (!$pais) {
                return response()->json([
                    'error' => 'País no encontrado'
                ], 404);
            }

            // Obtener los paquetes con offers_home = 1
            $paquetes = collect();
            foreach ($pais->destino as $destino) {
                foreach ($destino->paquetes()->where('offers_home', 1)
                             ->select(
                                 'tpaquetes.id',
                                 'tpaquetes.titulo',
                                 'tpaquetes.url',
                                 'tpaquetes.duracion',
                                 'tpaquetes.estado',
                                 'tpaquetes.offers_home',
                                 'tpaquetes.descuento',
                                 'tpaquetes.imagen'
                             )
                             ->with([
                                 'categorias:id,nombre,url',
                                 'destinos:id,codigo,nombre,url',
                                 'precio_paquetes'
                             ])
                             ->get() as $paquete) {
                    $paquetes->push([
                        'id' => $paquete->id,
                        'titulo' => $paquete->titulo,
                        'duracion' => $paquete->duracion,
                        'url' => $paquete->url,
                        'estado' => $paquete->estado,
                        'offers_home' => $paquete->offers_home,
                        'descuento' => $paquete->descuento,
                        'imagen' => $paquete->imagen,
                        'categorias' => $paquete->categorias,
                        'precio_paquetes' => $paquete->precio_paquetes,
                        'destinos' => $paquete->destinos->map(function ($dest) {
                            return [
                                'id' => $dest->id,
                                'codigo' => $dest->codigo,
                                'nombre' => $dest->nombre,
                                'url' => $dest->url,
                            ];
                        }),
                    ]);
                }
            }

            return response()->json([
                'pais' => [
                    'id' => $pais->id,
                    'codigo' => $pais->codigo,
                    'nombre' => $pais->nombre,
                    'url' => $pais->url,
                    'imagen' => $pais->imagen
                ],
                'paquetes' => $paquetes->unique('titulo')->values()
            ]);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function faq(){
        try {
            $faq = Faq::all();
            return response()->json($faq, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function category(TCategoria $category){
        try {
            $categories = TCategoria::all();
            return response()->json($categories, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function blog(){
        try {
            $blogs = TPost::latest('id')->get();
            $blogs_first = TPost::latest('id')->first();
            $category = TCategoria::all();
            return response()->json(['blog_first'=>$blogs_first, 'blogs'=>$blogs, 'category'=>$category], 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
    public function blog_show(TPost $post){

//        return view('page.blog-show', compact('post', 'category'));

        $posts = TPost::with('categoria', 'imagenes')->where('id', $post->id)->get();


        try {
            return response()->json($posts, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    public function list_inquires()
    {
        try {
            $inquires = TInquire::paginate(10);
            return response()->json($inquires, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los registros de TInquire',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store_inquire(Request $request)
    {
        // Convertir arrays a cadenas separadas por comas
        $category_all = implode(', ', $request->input('category_d', []));
        $destination_all = implode(', ', $request->input('destino_d', []));
        $duration_all = implode(', ', $request->input('duracion_d', []));

        // Guardar los datos en la base de datos
        $inquire = new TInquire();


        $inquire->package = $request->input('package');
        $inquire->hotel = $category_all;
        $inquire->destinos = $destination_all;
        $inquire->duracion = $duration_all;
        $inquire->pasajeros = $request->input('pasajeros_d');
        $inquire->nombre = $request->input('el_nombre');
        $inquire->email = $request->input('el_email');
        $inquire->travel_date = $request->input('el_fecha');
        $inquire->telefono = $request->input('el_telefono');
        $inquire->comentario = $request->input('el_textarea');

        $inquire->codigo_pais = $request->input('codigo_pais');
        $inquire->country = $request->input('country');
        $inquire->device = $request->input('device');
        $inquire->browser = $request->input('browser');
        $inquire->origen = $request->input('origen');
        $inquire->producto = $request->input('producto');
        $inquire->company = $request->input('company');
        $inquire->company_country = $request->input('company_country');

        $inquire->inquire_date = $request->input('inquire_date');

        $inquire->save();

        return response()->json(['message' => 'Data saved successfully']);

    }

    public function update_inquire(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'precio_inicial' => 'nullable|numeric',
            'precio_venta' => 'nullable|numeric',
            'sub_profit' => 'nullable|numeric',
            'profit' => 'nullable|numeric',
            'estado' => 'nullable|numeric',
            'sent' => 'nullable|numeric',
            'vendedor' => 'nullable|int',
            'sale_date' => 'nullable|date',
        ]);
        // Buscar el registro que se va a actualizar
        $inquire = TInquire::findOrFail($id); // Encuentra el registro por ID o falla si no existe
//        dd($inquire);

        // Actualizar los datos en la base de datos
        $inquire->precio_inicial = $request->input('precio_inicial');
        $inquire->precio_venta = $request->input('precio_venta');
        $inquire->sub_profit = $request->input('sub_profit');
        $inquire->profit = $request->input('profit');
        $inquire->estado = $request->input('estado');
        $inquire->sent = $request->input('sent');
        $inquire->vendedor = $request->input('vendedor');
        $inquire->sale_date = $request->input('sale_date');

        // Guardar los cambios
        $inquire->save();

        return response()->json([
            'message' => 'Data updated successfully',
            'updated_data' => [
                'precio_inicial' => $inquire->precio_inicial,
                'precio_venta' => $inquire->precio_venta,
                'sub_profit' => $inquire->sub_profit,
                'profit' => $inquire->profit,
                'estado' => $inquire->estado,
                'sent' => $inquire->sent,
                'vendedor' => $inquire->vendedor,
                'sale_date' => $inquire->sale_date,
                'updated_at' => $inquire->updated_at, // Retorna la fecha de la última actualización
            ]
        ], 200);

//        return response()->json(['message' => 'Data updated successfully']);
    }

    public function filter_inquires(Request $request)
    {
        try {
            // Obtener los parámetros del filtro
            $vendedor = $request->input('vendedor');
            $producto = $request->input('producto');
            $device = $request->input('device');
            $browser = $request->input('browser');
            $origen = $request->input('origen');
            $estado = $request->input('estado');

            $startSaleDate = $request->input('start_sale_date');
            $endSaleDate = $request->input('end_sale_date');

            $StartTravelDate = $request->input('start_travel_date');  // Fecha de inicio para el rango de 'fecha'
            $EndTravelDate = $request->input('end_travel_date');      // Fecha de fin para el rango de 'fecha'

            $createdStart = $request->input('created_start');  // Fecha de inicio para el rango de 'created_at'
            $createdEnd = $request->input('created_end');      // Fecha de fin para el rango de 'created_at'

            $perPage = $request->input('per_page', 10);

            // Construir la consulta para la paginación
            $query = TInquire::query();

            // Filtrar según los criterios dados
            if ($vendedor) {
//                $query->where('vendedor', 'like', '%' . $vendedor . '%');
                $query->where('vendedor', '=', $vendedor);
            }
//            if ($producto) {
//                $query->where('producto', 'like', '%' . $producto . '%');
//            }
            if ($producto) {
//                $query->where('producto', '=', $producto);
                $query->whereRaw('LOWER(producto) = ?', [strtolower($producto)]);
            }
            if ($device) {
                $query->where('device', 'like', '%' . $device . '%');
            }
            if ($browser) {
                $query->where('browser', 'like', '%' . $browser . '%');
            }
            if ($origen) {
                $query->where('origen', 'like', '%' . $origen . '%');
            }
            if ($estado) {
                $query->where('estado', 'like', '%' . $estado . '%');
            }
//            if ($startSaleDate && $endSaleDate) {
//                $query->whereBetween('sale_date', [$startSaleDate, $endSaleDate]);
//            }
//            if ($StartTravelDate && $EndTravelDate) {
//                $query->whereBetween('travel_date', [$StartTravelDate, $EndTravelDate]);
//            }
//            if ($createdStart && $createdEnd) {
//                $query->whereBetween('created_at', [$createdStart, $createdEnd]);
//            }
            // Manejo de sale_date con Carbon
            if ($startSaleDate && $endSaleDate) {
                $startSaleDate = Carbon::parse($startSaleDate)->startOfDay();
                $endSaleDate = Carbon::parse($endSaleDate)->endOfDay();
                $query->whereBetween('sale_date', [$startSaleDate, $endSaleDate]);
            }

            // Manejo de travel_date con Carbon
            if ($StartTravelDate && $EndTravelDate) {
                $StartTravelDate = Carbon::parse($StartTravelDate)->startOfDay();
                $EndTravelDate = Carbon::parse($EndTravelDate)->endOfDay();
                $query->whereBetween('travel_date', [$StartTravelDate, $EndTravelDate]);
            }
            // Manejo de created_at con Carbon
            if ($createdStart && $createdEnd) {
                $startDate = Carbon::parse($createdStart)->startOfDay();
                $endDate = Carbon::parse($createdEnd)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $query->orderBy('created_at', 'desc');

            // Clonar la consulta para obtener las sumas de todas las filas
            $totalsQuery = clone $query;

            // Obtener los resultados paginados
            $inquires = $query->paginate($perPage);

            // Calcular la suma de las columnas sin la paginación
            $totalPrecioInicial = $totalsQuery->sum('precio_inicial');
            $totalPrecioVenta = $totalsQuery->sum('precio_venta');
            $totalSubProfit = $totalsQuery->sum('sub_profit');
            $totalProfit = $totalsQuery->sum('profit');

            // Retornar los resultados en formato JSON
            return response()->json([
                'inquires' => $inquires,
                'totals' => [
                    'total_precio_inicial' => $totalPrecioInicial,
                    'total_precio_venta' => $totalPrecioVenta,
                    'total_sub_profit' => $totalSubProfit,
                    'total_profit' => $totalProfit
                ]
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'message' => 'Error al filtrar los registros de TInquire',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function formulario_diseno(Request $request)
    {


        $from = env('MAIL_EMAIL');
        $product = env('APP_NAME');
        $logo = env('APP_LOGO');
        $domain = env('APP_DOMAIN');

        $category_all = '';
        if ($request->category_d){
            foreach ($request->category_d as $categorias){
                if (isset($categorias)){
                    $category_all.=$categorias.',';
                }
            }
        }

        $destination_all = '';
        if ($request->destino_d){
            foreach ($request->destino_d as $destinos){
                if (isset($destinos)){
                    $destination_all.=$destinos.',';
                }
            }
        }



//        $travellers_all = '';
//        if ($request->pasajeros_d){
//            foreach ($request->pasajeros_d as $pasajeros){
//                if (isset($pasajeros)){
//                    $travellers_all.=$pasajeros.',';
//                }
//            }
//        }

        $travellers_all = '';
        if ($request->pasajeros_d){
            $travellers_all = $request->pasajeros_d;
        }

        $duration_all = '';
        if ($request->duracion_d){
            foreach ($request->duracion_d as $duracion){
                if (isset($duracion)){
                    $duration_all.=$duracion.',';
                }
            }
        }


        $package = '';
        if ($request->el_package){
            $package = $request->el_package;
        }

        $nombre = '';
        if ($request->el_nombre){
            $nombre = $request->el_nombre;
        }

        $email = '';
        if ($request->el_email){
            $email = $request->el_email;
        }

        $fecha = '';
        if ($request->el_fecha){
            $fecha = $request->el_fecha;
        }

//        $fecha = '';
//        if ($request->el_fecha){
//            foreach ($request->el_fecha as $date){
//                if (isset($date)){
//                    $fecha.=$date;
//                }
//            }
//        }

        $telefono = '';
        if ($request->el_telefono){
            $telefono = $request->el_telefono;
        }

        $country = '';
        if ($request->country){
            $country = $request->country;
        }


        $comentario = '';
        if ($request->el_textarea){
            $comentario = $request->el_textarea;
        }

//        $inquire = new TInquire();
//        $inquire->hotel = $category_all;
//        $inquire->destinos = $destination_all;
//        $inquire->pasajeros = $travellers_all;
////        $inquire->duracion = $duration_all;
//        $inquire->nombre = $nombre;
//        $inquire->email = $email;
//        $inquire->fecha = $fecha;
//        $inquire->telefono = $telefono;
//        $inquire->comentario = $comentario;
//        $inquire->save();

        $subject = $product ?? 'Default Subject';

        if ($email){
            try {
                Mail::send(['html' => 'notifications.page.client-form-design'], ['nombre' => $nombre, 'logo' => $logo, 'domain' => $domain, 'product' => $product], function ($messaje) use ($email, $nombre, $product, $from, $subject) {
                    $messaje->to($email, $nombre)
                        ->subject($product)
                        /*->attach('ruta')*/
                        ->from($from, $product);
                });
                Mail::send(['html' => 'notifications.page.admin-form-contact'], [
                    'package' => $package,
                    'category_all' => $category_all,
                    'destination_all' => $destination_all,
                    'travellers_all' => $travellers_all,
                    'duration_all' => $duration_all,

                    'nombre' => $nombre,
                    'email' => $email,
                    'fecha' => $fecha,
                    'telefono' => $telefono,
                    'comentario' => $comentario,

                    'country' => $country,
                    'logo' => $logo, 'domain' => $domain, 'product' => $product

                ], function ($messaje) use ($from, $product, $subject) {
                    $messaje->to($from, $product)
                        ->subject($product)
//                    ->cc($from2, $product)
                        /*->attach('ruta')*/
                        ->from($from, $product);
                });

                return response()->json('Thank you.', 200);
            }
            catch (Exception $e){
                return $e;
            }
        }

    }

    public function sendInquire(Request $request)
    {
        // Validar los datos entrantes
        $request->validate([
            'to_mail' => 'nullable|string',
            'package' => 'nullable|string',
            'category_d' => 'nullable|string',
            'destino_d' => 'nullable|string',
            'pasajeros_d' => 'nullable|string',
            'duracion_d' => 'nullable|string',
            'el_nombre' => 'nullable|string',
            'el_email' => 'nullable|email',
            'el_fecha' => 'nullable|date',
            'el_telefono' => 'nullable|string',
            'el_textarea' => 'nullable|string',
            'country' => 'nullable|string'
        ]);

        // Recoger los datos del request
        $to_mail = $request->input('to_mail');
        $package = $request->input('package');
        $category_all = $request->input('category_d');
        $destination_all = $request->input('destino_d');
        $travellers_all = $request->input('pasajeros_d');
        $duration_all = $request->input('duracion_d');
        $nombre = $request->input('el_nombre');
        $email = $request->input('el_email');
        $fecha = $request->input('el_fecha');
        $telefono = $request->input('el_telefono');
        $comentario = $request->input('el_textarea');
        $country = $request->input('country');

        // Datos adicionales para el correo
        $from = env('MAIL_EMAIL');
        $product = env('APP_NAME');
        $logo = env('APP_LOGO');
        $domain = env('APP_DOMAIN');

        // Envío del correo
        Mail::send(['html' => 'notifications.page.admin-send-inquire'], [
            'package' => $package,
            'category' => $category_all,
            'destination' => $destination_all,
            'travellers' => $travellers_all,
            'duration' => $duration_all,
            'nombre' => $nombre,
            'email' => $email,
            'fecha' => $fecha,
            'telefono' => $telefono,
            'comentario' => $comentario,
            'country' => $country,
            'logo' => $logo,
            'domain' => $domain,
            'product' => $product
        ], function ($message) use ($from, $product, $to_mail, $country, $nombre, $travellers_all) {
            $message->to($to_mail, $country.': '.$nombre.' x '.$travellers_all)
                ->subject($country.': '.$nombre.' x '.$travellers_all)
                ->from($from, 'ADMIN');
        });

        return response()->json(['message' => 'Correo enviado con éxito.'], 200);
    }

}
