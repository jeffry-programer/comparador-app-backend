<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusquedaUsuario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdController extends Controller
{
    public function allProds(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','like','%'.$request->nameProd.'%')->select('Nombre')->take(50)->distinct()->get();
        for($i = 0; $i < count($prods); $i++){
            $prods2[$i] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->orderBy('precioReal', 'asc')->first();
            $prods2[$i]['empresas'] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->where('logoRuta','!=',$prods2[$i]['logoRuta'])->select('logoRuta')->take(2)->get();
        }
        return response()->json($prods2);
    }

    public function productSugerations(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->where('Nombre','like','%'.$request->nameProd.'%')->where('Nombre','!=',$request->nameProdComplete)->select('Nombre')->take(2)->distinct()->get();
        for($i = 0; $i < 2; $i++){
            $prods2[$i] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->orderBy('precioReal', 'asc')->first();
            $prods2[$i]['empresas'] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->where('logoRuta','!=',$prods[$i]['logoRuta'])->select('logoRuta')->take(2)->get();
        }
        return response()->json($prods2);
    }

    public function queryProdsMoreSearch(){
        $prods = BusquedaUsuario::join('producto','busquedausuario.idProdBusqueda','=','producto.idProducto')->join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->select('descripcionBusqueda', DB::raw('COUNT(descripcionBusqueda) as total'))->groupBy('descripcionBusqueda')->orderBy('total', 'desc')->take(4)->distinct()->get();
        if(count($prods) != 0){
            for($i = 0; $i < count($prods); $i++){
                $prods2[$i] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['descripcionBusqueda'])->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->orderBy('precioReal', 'asc')->first();
                $prods2[$i]['empresas'] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods2[$i]['Nombre'])->where('logoRuta','!=',$prods2[$i]['logoRuta'])->select('logoRuta')->take(2)->get();
            }
        }else{
            $prods2 =  null;
        }
        return response()->json($prods2);
    }
    

    public function queryDetailProd(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$request->nameProd)->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta','producto.Descripcion')->orderBy('precioReal', 'asc')->first();
        $prods['empresas'] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods['Nombre'])->where('logoRuta','!=',$prods['logoRuta'])->select('logoRuta','precioReal')->take(2)->get();
        return response()->json($prods);
    }

    public function queryOtherPrice(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('producto_has_empresa.Producto_idProducto','=',$request->idProd)->select('precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta')->get();
        return response()->json($prods);
    }
    
    public function queryProdsBySubCategoryProd(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->join('subcategoria', 'subcategoria.idSubCategoria', '=', 'producto.subCategoria_idsubCategoria')->where('nombreSubCategoria','=',$request->nameSubCategory)->select('Nombre')->take(50)->distinct()->get();
        for($i = 0; $i < count($prods); $i++){
            $prods2[$i] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->orderBy('precioReal', 'asc')->first();
            $prods2[$i]['empresas'] = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$prods[$i]['Nombre'])->where('logoRuta','!=',$prods2[$i]['logoRuta'])->select('logoRuta')->take(2)->get();
        }
        return response()->json($prods2);
    }

    //Query prods for add prod to list
    public function queryProdList(Request $request){
        $response = Producto::where('Nombre','like',$request->nameProd.'%')->take(4)->get();
        return response()->json($response);
    }
}
