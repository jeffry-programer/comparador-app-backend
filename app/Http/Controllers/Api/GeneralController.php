<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusquedaUsuario;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\List_Has_User;
use App\Models\Listas;
use App\Models\Notification;
use App\Models\Persona;
use App\Models\SubCategoria;
use App\Models\ProdList;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Termwind\Components\Raw;

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
    public function queryList(Request $request){
        $list = Listas::join('listacompra_has_usuario','listacompra_has_usuario.listaCompra_idListaCompra','=','listacompra.idListaCompra')->where('Usuario_idUsuario','=',$request->userId)->get();
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
        $prod->idProducto = $request->prodId;
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
        //Save list user
        $listUser = new List_Has_User;
        $lastId = Listas::latest('idListaCompra')->select('idListaCompra')->first();        
        $listUser->listaCompra_idListaCompra = $lastId['idListaCompra'];
        $listUser->Usuario_idUsuario = $request->userId;
        $listUser->creadorLista = $request->userId;
        $listUser->save();
        //Return response
        return response($lastId['idListaCompra'], Response::HTTP_CREATED);
    }
    //Share list
    public function shareList(Request $request){
        //Change state list
        $userShare = User::where('correo','=',$request->email)->select('idUsuario')->first();
        $list = Listas::where('idListaCompra','=',$request->listId)->update(['estado' => 3]);
        //Save data list has user
        $listUser = new List_Has_User;
        $listUser->listaCompra_idListaCompra = $request->listId;
        $listUser->Usuario_idUsuario = $userShare['idUsuario'];
        $listUser->creadorLista = $request->userId;
        $listUser->save();
        //Save data notification
        $notification = new Notification;
        $notification->Usuario_idUsuario = $userShare['idUsuario'];
        $notification->asuntoNotificacion = 'te ha compartido una lista';
        $notification->descripcionNotificacion = 'Haz click para ver la lista';
        $notification->idUsuarioNotifica = $request->userId;
        $notification->estadoNotificacion = $request->status;
        $notification->fechaNotificacion = Carbon::now();
        $listIdEncrypt = $this->encrypt($request->listId);
        $notification->linkNotificacion = 'https://tucomparas.com.co/verEditar?'.$listIdEncrypt.'?compartida';
        $notification->save();
        //Return response
        return response($notification, Response::HTTP_OK);
    }
    //Compare the list
    public function compareList(Request $request){
        //Get data
        $res = ProdList::where('listaCompra_idListaCompra','=',$request->listId)->get();
        $res2 = [];
        for($i = 0; $i < count($res); $i++){
            $res2[$i] = Producto::join('producto_has_empresa','producto_has_empresa.Producto_idProducto','=','producto.idProducto')->join('empresa','empresa.idEmpresa','=','producto_has_empresa.Empresa_idEmpresa')->where('idProducto','=',$res[$i]['idProducto'])->where('precioReal','>','0')->select('Nombre','precioReal','logoRuta')->orderBy('precioReal', 'asc')->first();
            if($res2[$i] == null){
                $res2[$i]['Nombre'] = $res[$i]["nombreProducto"];
                $res2[$i]['precioReal'] = 0;
                $res2[$i]['logoRuta'] = "";
            }
        }
        return response()->json($res2);
    }
    //Compare the list of bussiness
    public function compareListBussiness(Request $request){
        $res = ProdList::where('listaCompra_idListaCompra','=',$request->listId)->where('idProducto','!=','0')->get();
        $res2 = [];
        for($i = 0; $i < count($res); $i++){
            $res2[$i] = Producto::join('producto_has_empresa','producto_has_empresa.Producto_idProducto','=','producto.idProducto')->leftjoin('empresa','empresa.idEmpresa','=','producto_has_empresa.Empresa_idEmpresa')->where('idProducto','=',$res[$i]['idProducto'])->where('precioReal','>','0')->where('empresa.nombreEmpresa','=',$request->nameBussiness)->select('Nombre','precioReal','logoRuta')->orderBy('precioReal', 'asc')->first();
            if($res2[$i] == null){
                $res2[$i]['Nombre'] = $res[$i]["nombreProducto"];
                $res2[$i]['precioReal'] = 0;
                $res2[$i]['logoRuta'] = "";
            }
        }
        return response()->json($res2);
    }
    //Query bussines
    public function queryBussiness(){
        $bussiness = Empresa::select('nombreEmpresa')->get();
        $bussiness[count($bussiness)] = ["nombreEmpresa" => "Precios sugeridos"];
        return response()->json($bussiness);
    }
    //Query Notifications
    public function queryNotifications(Request $request){
        $notification = Notification::join('usuario','usuario.idUsuario','=','notificaciones.Usuario_idUsuario')->leftjoin('persona','persona.Usuario_idUsuario','=','notificaciones.idUsuarioNotifica')->where('idUsuario','=',$request->userId)->select('linkNotificacion','estadoNotificacion','Nombres','Apellidos','asuntoNotificacion','idnotificaciones')->get();
        for($i=0; $i < count($notification); $i++){
            if(explode(" ", $notification[$i]['asuntoNotificacion'])[4] == 'lista'){
                $notification[$i]['asuntoNotificacion'] = 'lista';
                $string = explode('?', $notification[$i]['linkNotificacion'])[1];
                $notification[$i]['linkNotificacion'] = $this->decrypt($string);
            }else{
                $notification[$i]['asuntoNotificacion'] = 'producto';
                $notification[$i]['linkNotificacion'] = str_replace('-',' ', explode("productos/",$notification[$i]['linkNotificacion'])[1]);
            }           
        }
        return response()->json($notification);
    }
    //Query Detail profeil
    public function queryDetailProfile(Request $request){
        $user = User::join('persona','persona.Usuario_idUsuario','=','usuario.idUsuario')->where('idUsuario','=',$request->userId)->select('Nombres','Apellidos','sexo','NumeroTelefono','direccion','correo')->first();
        return response()->json($user);
    }

    //decrypt a string
    public function decrypt($string){
        for ($i=0; $i < 10; $i++){
            $string = base64_decode($string);
        }
        return $string;
    }

    //encrypt a string
    public function encrypt($string){
        for ($i=0; $i < 10; $i++){
            $string = base64_encode($string);
        }
        return $string;
    }

    //Change state of notifications
    function changeStateNotification(Request $request){
        $response = Notification::where('idnotificaciones','=',$request->notificationId)->update(['estadoNotificacion' => 1]);
        return response($response, Response::HTTP_OK);
    }

    //Query notifications not read
    function queryNotificatioNotRead(Request $request){
        $notification = Notification::where('Usuario_idUsuario','=',$request->userId)->where('estadoNotificacion','==', 0)->get();
        return response(count($notification));
    }

    //Save profile
    function saveProfile(Request $request){
        $response = Persona::where('Usuario_idUsuario','=',$request->userId)->update(['Nombres' => $request->name, 'Apellidos' => $request->lastName, 'sexo' => $request->sexo, 'NumeroTelefono' => $request->phoneNumber, 'direccion' => $request->address]);
        return response($response, Response::HTTP_OK);
    }

    //Save info search
    function saveInfoSearch(Request $request){
        //query prod
        $prod = Producto::where('Nombre','=',$request->description)->select('idProducto')->first();
        if($prod != null){
            $prodId = $prod['idProducto'];
        }else{
            $prodId = 0;
        }
        $searchUser = new BusquedaUsuario;
        $searchUser->Usuario_idUsuario = $request->userId;
        $searchUser->descripcionBusqueda = $request->description;
        $searchUser->fechaBusqueda = Carbon::now();
        $searchUser->ipBusqueda = request()->ip();
        $searchUser->idProdBusqueda = $prodId;
        $searchUser->save();
        return response($searchUser, Response::HTTP_OK);
    }

    //Change password user
    function changePassword(Request $request){
        $user = User::where('idUsuario','=',$request->userId)->update(['Clave' => Hash::make($request->password)]);
        return response($user, Response::HTTP_OK);
    }
}
