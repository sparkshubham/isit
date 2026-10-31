<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\ProductMedia;
use App\Models\Product;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\Api\ProductRequest;
use DB;
use File;
use Auth;
use Storage;
use Redirect;
use FFMpeg;

class StoreController extends Controller
{
    public function __construct(Route $route){
        // $this->middleware(function ($request, $next) use ($route){
        //     // Getting controller function
        //     $routeNameStr = $route->getActionName();
        //     $routeName = explode('@',$routeNameStr);

        //     $moduleName = request()->segment(2);
        //     return $next($request);
        // });
    }

    public function index(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // $request->user_id=$user->id;
        try{
            if(empty($user->id)){ throw new \Exception(__('api-messages.6000')); }

            if(getBlockByUser($user->id,$user->id)){
                return getResponse(638,404);
            }
            $productSortBy = 'id desc';
            $userId =$user->id;
            $productType='';
            if($request->product_type){
                $productType = $request->product_type;
            }
          
            $products = Product::with([
                'Country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'Category'=>function($query){
                    $query->select(['categories.id','categories.name']);
                },
                'getMedia'=>function($query){
                    $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->whereNull('products.deleted_at')
            ->select('products.id','products.name','products.user_id','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.status','products.price_type','products.price','products.currency')
            ->where(function($query) use($userId,$productType){
                $query->where('products.user_id', $userId);
                if($productType!=0){
                    $query->where('products.product_type', $productType);
                }
                $query->whereRaw('products.deleted_at IS NULL');
            })
            ->orderByRaw($productSortBy)
            ->paginate(10);

            $response['products'] = $products;
        
            if($request->ajax()){
                if($request->page==1 && count($products)==0){
                    $data_arr=['error_name'=>'No product found'];
                    $view = view('frontend.common.no-data',$data_arr)->render();
                    return response()->json(['html'=>$view]);
                }else{
                    $view = view('frontend.store.list',$response)->render();
                    return response()->json(['html'=>$view]);
                }
        
             }

        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        $response['title'] = 'Store';
        return view ('frontend.store.store',$response);
    }


    public function getPostboardProduct(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        // echo $user->id;exit;
        try{
            // if(empty($request->product_type)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $productSortBy = 'id desc';
            $userId = $user->id;
            $productType = $request->product_type;
            $blockUserIds = getBlockUserIds($userId);
            // echo $blockUserIds;exit;
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $products = Product::with([
                'Country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'Category'=>function($query) use($search_keyword){
                    $query->select(['categories.id','categories.name']);
                },
                'getMedia'=>function($query){
                    $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('products.id','products.name','products.user_id','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.status','products.price_type','products.price','products.currency')
            ->orWhere(function ($query) use ($search_keyword) {
                $query->with('Category')->whereHas('Category', function ($query) use ($search_keyword) {
                    $query->whereRaw('LOWER(categories.name) like "%' . strtolower($search_keyword) . '%"');
                });
            })
            ->orWhere(function($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->orWhere(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(products.name) like "%'.strtolower($search_keyword).'%"');
                       // $query->orWhereRaw('LOWER(products.description) like "%'.strtolower($search_keyword).'%"');
                       // $query->orWhereRaw('LOWER(products.city) like "%'.strtolower($search_keyword).'%"');
                     //   $query->orWhereRaw('LOWER(products.zip_code) like "%'.strtolower($search_keyword).'%"');
                    });
                }
            })
            ->where(function($query) use($userId,$productType,$search_keyword,$blockUserIds){
                $query->whereRaw('products.user_id NOT IN ('.$userId.') AND products.user_id NOT IN ('.$blockUserIds.')');
                if($productType !=0){
                    if($productType == 1){
                        $productType = 2;
                    }else if($productType == 2){
                        $productType = 1;
                    }
                    $query->where('products.product_type', $productType);
                }
                $query->where('products.status', 1);
                $query->whereRaw('products.deleted_at IS NULL');
            })
          
            ->whereNull('products.deleted_at')
            ->orderByRaw($productSortBy)
            ->paginate(10);
            $response['products'] = $products;
            
            if($request->ajax()){
                   if($request->page==1 && count($products)==0){
                       $data_arr=['error_name'=>'No product found'];
                       $view = view('frontend.common.no-data',$data_arr)->render();
                       return response()->json(['html'=>$view]);
                   }else{
                       $view = view('frontend.store.list',$response)->render();
                       return response()->json(['html'=>$view]);
                   }
           
           }
            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        $response['title'] = 'Store';
        return view ('frontend.store.store',$response);
    }
    

    public function createProduct(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            $product_idm=$request->filled('product_id');
            if($request->filled('product_id')){
                $product = Product::findOrFail($request['product_id']);
                if(isset($request['deleted_media_ids']) && $request['deleted_media_ids']!=''){
                    $array = $request['deleted_media_ids'];
                    // array_filter(array_unique(explode(',',$request['deleted_media_ids'])));
                    deleteMediaProduct($array);
                }
                $code = 642;
            }else{
                $product = new Product();
                $code = 641;
            }
            $user = Auth::user();
            $product->user_id = $user->id;
            $product->product_type = $request['product_type'];
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->country_id = $request['country_id'];
            $product->category_id = $request['category_id'];
            $product->city = $request['city'];
            $product->zip_code = $request['zip_code'];
            $product->price_type = $request['price_type'];
            $product->currency = $request['currency'];
            $product->price = ($request->filled('price') ? $request['price'] : 0.00);
            $product->save();
            $response['product_id'] = $product->id;
            if(!empty($request->file_media))
            {
                $width = 0;
                $height = 0;
                foreach ($request->file_media as $key => $value) {
                    $media_type=getFileTypeUsingName($value);
                    $is_default=0;
                    if($key==0)
                    {
                        $is_default=1;
                    }
                    $request->merge([
                        'media_type' => $media_type,
                        'product_id'=>$product->id,
                        'media_order'=>$key+1,
                        'is_default'=>$is_default,
                        'width'=>$width,
                        'height'=>$height,
                        'file_name'=>$value,
                    ]);
                    $this->movePostIntoS3($request);
                }
            }
            if($product){
                $response_type='success';
                if($product_idm){
                    $response_message='Product updated successfully';
                }
                else{
                    $response_message='Product added successfully';
                }
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }

        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            $response_type='error';
            return Redirect::back()->with($response_type,$this->optionalData['message']);;
            
        }
        DB::commit();
        return Redirect::back()->with($response_type,$response_message);
    }

    
    public function movePostIntoS3($request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('product_media_id')){
                $productMedia = ProductMedia::findOrFail($request['product_media_id']);
                $code = 617;
            }else{
                $productMedia = new ProductMedia();
                $code = 616;
            }

            $destinationPathTmp = config('constants.temp_post_media.upload_path');

            $destinationPathNew = config('constants.product_media.upload_path');
            if($request->file_name){
                $file = $request->file_name;
                if($request['media_type'] == 2){
                    $fileName = $request->file_name;
                    $old_path= $destinationPathTmp.'/'.$fileName;
                    $new_path = $destinationPathNew.'/'.$fileName;
                    Storage::disk('s3')->move($old_path, $new_path);
                    $productMedia->file_name = $fileName;
                    if(!empty($request->thumbFileName))
                    {
                        $thumbFileName = $request->thumbFileName;
                        $old_path_tmp = $destinationPathTmp.'/'.$thumbFileName; 
                        $new_path_tmp = $destinationPathNew.'/'.$thumbFileName;
                        Storage::disk('s3')->move($old_path_tmp, $new_path_tmp);
                        $productMedia->thumb_image_name = $thumbFileName; 
                    }

                }else{

                    $fileName = $request->file_name;
                    $old_path= $destinationPathTmp.'/'.$fileName;
                    $new_path = $destinationPathNew.'/'.$fileName;
                    Storage::disk('s3')->move($old_path, $new_path);
                    $productMedia->file_name = $fileName;
                }
            }            
            $productMedia->media_type = $request['media_type'];
            $productMedia->product_id = $request['product_id'];
            $productMedia->media_order = ($request->filled('media_order') ? $request['media_order'] : 1);
            $productMedia->is_default = ($request->filled('is_default') ? $request['is_default'] : 0);
            $productMedia->width = ($request->filled('width') ? $request['width'] : 0);
            $productMedia->height = ($request->filled('height') ? $request['height'] : 0);
            $save = $productMedia->save();
            $response['product_media_id'] = $productMedia->id;
            
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return $this->optionalData;
        }
        DB::commit();
        return $response;
    }

    public function view(Request $request,$product_id){
        $response = [];
        $request->product_id=$product_id;
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->product_id)){
                $response['title'] = 'Products details';
                $response['error_name']='No product found';
                 return view('frontend.common.no-post',$response);
             }
            $isDeleted = isProductDeleted($request->product_id);
            if($isDeleted == 1){
                $response['title'] = 'Products details';
                $response['error_name']='No product found';
                return view('frontend.common.no-post',$response);
            }
            $productId = $request->product_id;
            $products = Product::with([
                'Country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'Category'=>function($query){
                    $query->select(['categories.id','categories.name']);
                },
                'getMedia'=>function($query){
                    $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('products.id','products.name','products.status','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.price_type','products.price','products.currency')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(products.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($productId){
                $query->where('products.id', $productId);
                $query->whereRaw('products.deleted_at IS NULL');
            })
            ->first();
            // var_dump($products->getUser->is_block);
            if(empty($products) || $products == null){
                $response['title'] = 'Products details';
                
                $response['error_name']='No product found';
                return view('frontend.common.no-post',$response);
            }else if($products->getUser->is_block == 1){
                $response['title'] = 'Products details';
                
                $response['error_name']='No product found';
                return view('frontend.common.no-post',$response);
            }
            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }

        $response['products'] = $products;
        $response['title'] = 'Products details';
        return view('frontend.store.product-view',$response);
    }


    public function createEditProduct(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->product_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isProductDeleted($request->product_id);
            if($isDeleted == 1){
                return getResponse(643,400);
            }
            $productId = $request->product_id;
            $products = Product::with([
                'Country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'Category'=>function($query){
                    $query->select(['categories.id','categories.name']);
                },
                'getMedia'=>function($query){
                    $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('products.currency','products.id','products.name','products.status','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.price_type','products.price')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(products.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($productId){
                $query->where('products.id', $productId);
                $query->whereRaw('products.deleted_at IS NULL');
            })
            ->first();
            // var_dump($products->getUser->is_block);
            if(empty($products) || $products == null){
                return getResponse(644,400);
            }else if($products->getUser->is_block == 1){
                return getResponse(645,400);
            }
            $response['data'] = $products->toArray();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
     
    }


    public function deleteProduct(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->product_id)){ 
                
            }
            $product = Product::findOrFail($request->product_id);
            if(empty($product)){
                return getResponse(112,400);
            }
            $product->deleted_at = date('Y-m-d H:i:s');
            $product->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(646,200,$response);
    }


}
