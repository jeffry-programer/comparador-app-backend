<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Listas;
use App\Models\Notification;
use App\Models\SubCategoria;
use App\Models\ProdList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneralController extends Controller
{
    //Query all categories
    public function allCategories(){
        $categories = Categoria::where('controlCategoria','!=',1)->get();
        return response()->json($categories);
    }
    
    //Query subcategories by name category
    public function querySubCategoriesByNameCategory(Request $request){
        $subCategories = SubCategoria::join('categoria', 'subcategoria.Categoria_idCategoria', '=', 'categoria.idCategoria')->where('nombreCategoria','=',$request->nameCategory)->get();
        return response()->json($subCategories);
    }

    //Query all list by user
    public function queryList(){
        $list = Listas::all();
        return response()->json($list);   
    }

    //Query all list by user
    public function queryDetaiList(Request $request){
        $list = Listas::where('idListaCompra','=',$request->listId)->leftjoin('productoslista', 'productoslista.listaCompra_idListaCompra', '=', 'listacompra.idListaCompra')->get();
        return response()->json($list);   
    }

    //Insert prod list
    public function addProdList(Request $request){
        //Registering prod list
        $prod = new ProdList;
        $prod->listaCompra_idListaCompra = $request->listId;
        $prod->nombreProducto = $request->nameProd;
        $prod->cantidad = 1;
        $prod->estado = 0;
        $prod->idUsuario = 0;
        $prod->idProducto = 0;
        $prod->PrecioProductoLista = 0;
        $prod->save();
        //Return response
        return response($prod, Response::HTTP_CREATED);
    }
    //Change status prod
    public function changeStatusProd(Request $request){
        $response = ProdList::where('idproductosLista','=',$request->prodListId)->update(['estado' => $request->status]);
        return response($response, Response::HTTP_OK);
    }
    //Delete prod of list
    public function deleteProdList(Request $request){
        $response = ProdList::where('idproductosLista','=',$request->prodListId)->delete();
        return response($response, Response::HTTP_OK);
    }
    //Edit prod of list
    public function editProdList(Request $request){
        $response = ProdList::where('idproductosLista','=',$request->prodListId)->update(['cantidad' => $request->quantityProd, 'PrecioProductoLista' => $request->priceProd]);
        return response($response, Response::HTTP_OK);
    }
    //Delte list
    public function deleteList(Request $request){
        $response = Listas::where('idListaCompra','=',$request->listId)->leftjoin('productoslista', 'productoslista.listaCompra_idListaCompra', '=', 'listacompra.idListaCompra')->delete();
        return response($response, Response::HTTP_OK);
    }
    //Add list
    public function addList(Request $request){
        //Save list
        $list = new Listas;
        $list->nombreLista = $request->nameList;
        $list->estado = $request->estado;
        $list->save();
        //Return response
        return response($list, Response::HTTP_CREATED);
    }
    //Share list
    public function shareList(Request $request){
        //Change state list
        $list = Listas::where('idListaCompra','=',$request->listId)->update(['estado' => 3]);
        //Save data notification
        $notification = new Notification;
        $notification->Usuario_idUsuario = 0;
        $notification->asuntoNotificacion = 'Lista compartida';
        $notification->descripcionNotificacion = 'Te han compartido una lista';
        $notification->idUsuarioNotifica = 0;
        $notification->estadoNotificacion = 0;
        $notification->linkNotificacion = 'http://localhost:8100/detalle-lista/'.$request->idList;
        $notification->save();
        //Return response
        return response($notification, Response::HTTP_OK);
    }
    //Compare the list
    public function compareList(Request $request){
        //Get data
        $list = ProdList::where('listaCompra_idListaCompra','=',$request->listId)->get();
        return response()->json($list);
    }
}
