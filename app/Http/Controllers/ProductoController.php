<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productos = Producto::paginate(5);
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('productos.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required', 'editorial' => 'required', 'cantidad' => 'required', 'genero' => 'required', 'imagen' => 'required|image|mimes:jpeg,png,svg|max:1024'
        ]);

        try{

            //Guardar imagen en AWS S3
            $folder = "imagenes";

            $producto = new Producto;
            $producto->nombre = $request->nombre;
            $producto->editorial = $request->editorial;
            $producto->cantidad = $request->cantidad;
            $producto->genero = $request->genero;
            $image_path = Storage::disk('s3')->put($folder, $request->image, 'public');

            $producto->image_path = $image_path;
            $producto->save();

            return redirect()->route('productos.index')
            ->with('success','Manga registrado correctamente!');

        }catch(\Exception $e){

            return redirect()->route('productos.index')
            ->with('error','No se pudo registrar el post. Error: '.$e->getMessage());
        }

        /*guardar imagen local
         $producto = $request->all();

         if($imagen = $request->file('imagen')) {
             $rutaGuardarImg = 'imagen/';
             $imagenProducto = date('YmdHis'). "." . $imagen->getClientOriginalExtension();
             $imagen->move($rutaGuardarImg, $imagenProducto);
             $producto['imagen'] = "$imagenProducto";
         }

         Producto::create($producto);
         return redirect()->route('productos.index');
         */
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idmanga)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto)
    {
        return view('productos.editar', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required', 'editorial' => 'required', 'cantidad' => 'required', 'genero' => 'required'
        ]);
         $prod = $request->all();
         if($imagen = $request->file('imagen')){
            $rutaGuardarImg = 'imagen/';
            $imagenProducto = date('YmdHis') . "." . $imagen->getClientOriginalExtension();
            $imagen->move($rutaGuardarImg, $imagenProducto);
            $prod['imagen'] = "$imagenProducto";
         }else{
            unset($prod['imagen']);
         }
         $producto->update($prod);
         return redirect()->route('productos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto){
    /* Borrar manga de manera LOCAL
    {
        $producto->delete();
        return redirect()->route('productos.index');
    }
    */
    try{
        $producto = Producto::findOrFail($idmanga);
        Storage::disk('s3')->delete($producto->image_path);

        $producto->delete();

       return redirect()->route('productos.index')
       ->with('success','Manga eliminado correctamente!');

   }catch(\Exception $e){

       return redirect()->route('productos.index')
       ->with('error','No se pudo eliminar el manga. Error: '.$e->getMessage());
   }
}
}
