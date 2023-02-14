<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProdController extends Controller
{
    public function allProds(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','like','%'.$request->nameProd.'%')->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->get();
        return response()->json($prods);
    }

    public function productSugerations(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','like','%'.$request->nameProd.'%')->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','producto.Descripcion','logoRuta')->take(2)->get();
        return response()->json($prods);
    }

    public function queryProdsMoreSearch(){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta','producto.Descripcion')->take(4)->get();
        return response()->json($prods);
    }

    public function queryDetailProd(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('Nombre','=',$request->nameProd)->select('Nombre','precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta','producto.Descripcion')->get();
        return response()->json($prods);
    }

    public function queryOtherPrice(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->where('producto_has_empresa.Producto_idProducto','=',$request->idProd)->select('precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta')->get();
        return response()->json($prods);
    }
    
    public function queryProdsBySubCategoryProd(Request $request){
        $prods = Producto::join('producto_has_empresa', 'producto.idProducto', '=', 'producto_has_empresa.Producto_idProducto')->leftjoin('empresa', 'empresa.idEmpresa', '=', 'producto_has_empresa.Empresa_idEmpresa')->join('subcategoria', 'subcategoria.idSubCategoria', '=', 'producto.subCategoria_idsubCategoria')->where('nombreSubCategoria','=',$request->nameSubCategory)->select('precioReal','FotoPrincipal','empresa.nombreEmpresa','logoRuta','producto.Nombre')->get();
        return response()->json($prods);
    }
}
