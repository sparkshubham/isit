<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Event;
use App\Models\SavedEvent;
use App\Models\UserFollow;
use App\Models\UserStory;
use App\Models\UserStoryLike;
use App\Models\UserStoryView;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use Response;
use Hash;
use DB;
use URL;
use Auth;
use Mail;
use Storage;
use Carbon\Carbon;


use App\Http\Requests\Api\ProductRequest;
use App\Http\Requests\Api\ProductMediaRequest;
use App\Http\Requests\Api\EventRequest;
use App\Http\Requests\Api\SaveEventRequest;
use App\Http\Requests\Api\LikeStoryRequest;
use App\Http\Requests\Api\ViewStoryRequest;


use App\Jobs\UserWelcomeEmailJob;

class ProductController extends Controller
{
    /**
     * @summary : Api for create Product
     * @param : heading, description, user_id
     * @type : post
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 March 2021
     */
    public function createProduct(ProductRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $product = Product::findOrFail($request['id']);
                if(isset($request['deleted_media_ids']) && $request['deleted_media_ids']!=''){
                    $array = array_filter(array_unique(explode(',',$request['deleted_media_ids'])));
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
            $product->price = ($request->filled('price') ? $request['price'] : 0.00);
            $product->currency = ($request->filled('currency') ? $request['currency'] : '');
            $product->save();
            $response['product_id'] = $product->id;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @summary : Api for upload Product Media
     * @type : post
     * @param : product_id, file, media_type, media_order, is_default
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function uploadProductMedia(ProductMediaRequest $request){
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
            $destinationPath = config('constants.product_media.upload_path');
            if($request->hasFile('file')){
                $file = $request->file('file');
                if($request['media_type'] == 2){
                    // prd($file[0]->getMimeType());
                    $extension = $file[0]->extension();    
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    // echo $productMedia->file_name;
                    if(isset($productMedia->file_name) && $productMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$productMedia->file_name)){
                        Storage::disk('s3')->delete($destinationPath.'/'.$productMedia->file_name);
                        // unlink($destinationPath.$productMedia->file_name);
                    }
                    // $file[0]->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file[0]));
                    $productMedia->file_name = $fileName;
                    // echo $productMedia->file_name;exit;
                    // if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif')
                    {
                        $extension = $file[1]->extension();    
                        $fileName = 'thumb_'.rand().time().'.'.$extension;
                        $pathToUpload = $destinationPath.'/'.$fileName;
                        //Move Uploaded File
                        if(isset($productMedia->thumb_image_name) && $productMedia->thumb_image_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$productMedia->thumb_image_name)){
                            Storage::disk('s3')->delete($destinationPath.'/'.$productMedia->thumb_image_name);
                            // unlink($destinationPath.$productMedia->file_name);
                        }
                        Storage::disk('s3')->put($pathToUpload, file_get_contents($file[1]));
                        // $file[1]->move($destinationPath,$fileName);
                        $productMedia->thumb_image_name = $fileName;
                        
                    }
                }else{
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    if(isset($productMedia->file_name) && $productMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$productMedia->file_name)){
                        // unlink($destinationPath.$productMedia->file_name);
                        Storage::disk('s3')->delete($destinationPath.'/'.$productMedia->file_name);
                    }
                    // $file->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
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
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-products
     * @summary : Get All Product By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function getProducts(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }

            if(getBlockByUser($user->id,$request->user_id)){
                return getResponse(638,404);
            }
            $productSortBy = 'id desc';
            $userId = $request->user_id;
            $productType = $request->product_type;
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('products.id','products.name','products.user_id','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.status','products.price_type','products.price','products.currency')
            ->where(function($query) use($userId,$productType){
                $query->where('products.user_id', $userId);
                if($productType !=0){
                    $query->where('products.product_type', $productType);
                }
                $query->whereRaw('products.deleted_at IS NULL');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($productSortBy)
            ->paginate(10);
            $response['products'] = $products->items();
            // prd($response['products']);
            $response['paging'] = [
                'total_item'=>$products->total(),
                'item_per_page'=>$products->perPage(),
                'current_page'=>$products->currentPage(),
                'last_page'=>$products->lastPage(),
                'prev'=>$products->previousPageUrl(),
                'next'=>$products->nextPageUrl(),
                'first'=>$products->url(1),
                'last'=>$products->url($products->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-product
     * @summary : Get Product By Id
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function getProduct(Request $request){
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('products.id','products.name','products.status','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.price_type','products.price','products.currency')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(products.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($productId){
                $query->where('products.id', $productId);
                $query->whereRaw('products.deleted_at IS NULL');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
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
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : delete-product
     * @summary : Api for delete the product
     * @param : product_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function deleteProduct(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->product_id)){ 
                throw new \Exception(__('api-messages.6000')); 
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
    /**
     * @summary : Api for create Event
     * @param : heading, description, user_id
     * @type : post
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 March 2021
     */
    public function createEvent(EventRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $event = Event::findOrFail($request['id']);
                if($event->admin_status == 1){
                    return getResponse(656,404);
                }
                $code = 648;
            }else{
                $event = new Event();
                $code = 647;
            }
            $user = Auth::user();
            $event->user_id = $user->id;
            $event->name = $request['name'];
            $event->date = date('Y-m-d',strtotime($request['date']));
            $event->start_time = $request['start_time'];
            $event->end_time = $request['end_time'];
            $event->timeoffset = $request['timeoffset'];
            $event->timezone = $request['timezone'];
            $event->admin_status = 0;
            if($request->hasFile('image')){
                $file = $request->file('image');
                $destinationPath = config('constants.event_media.upload_path');
                $extension = $file->extension();
                $fileName = rand().time().'.'.$extension;
                $pathToUpload = $destinationPath.'/'.$fileName;
                //Move Uploaded File
                if(isset($event->image) && $event->image !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$event->image)){
                    Storage::disk('s3')->delete($destinationPath.'/'.$event->image);
                    // unlink($destinationPath.$user->profile_picture);
                }
                Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                // $file->move($destinationPath,$fileName);
                $event->image = $fileName;
            }
            $event->save();
            $eventId = $event->id;
            $eventDeail = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(events.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($eventId){
                $query->where('events.id', $eventId);
                $query->whereRaw('events.deleted_at IS NULL');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->first();

            $response['event_id'] = $event->id;
            $response['data'] = $eventDeail;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-events
     * @summary : Get All Events By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function getEvents(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            if(getBlockByUser($user->id,$request->user_id)){
                return getResponse(638,404);
            }
               $eventSortBy = 'id desc';
            $userId = $request->user_id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            ->where(function($query) use($userId,$search_keyword){
                $query->where('events.user_id', $userId);
                $query->whereRaw('events.deleted_at IS NULL');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                    });
                }
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($eventSortBy)
            ->paginate(10);
            $response['events'] = $events->items();
            // prd($response['events']);
            $response['paging'] = [
                'total_item'=>$events->total(),
                'item_per_page'=>$events->perPage(),
                'current_page'=>$events->currentPage(),
                'last_page'=>$events->lastPage(),
                'prev'=>$events->previousPageUrl(),
                'next'=>$events->nextPageUrl(),
                'first'=>$events->url(1),
                'last'=>$events->url($events->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-event
     * @summary : Get Event By Id
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function getEvent(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->event_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $eventId = $request->event_id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(events.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($eventId){
                $query->where('events.id', $eventId);
                $query->whereRaw('events.deleted_at IS NULL');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->first();
            // var_dump($events->getUser->is_block);
            if(empty($events) || $events == null){
                return getResponse(644,400);
            }
            $response['data'] = $events->toArray();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : delete-event
     * @summary : Api for delete the product
     * @param : event_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 11 March 2022
     */
    public function deleteEvent(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->event_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $event = Event::findOrFail($request->event_id);
            if(empty($event)){
                return getResponse(112,400);
            }
            $event->deleted_at = date('Y-m-d H:i:s');
            $event->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(649,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-postboard-products
     * @summary : Get All Product for Post board not including logged in user 
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 March 2022
     */
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
                $query->with('Category')->orWhereHas('Category', function ($query) use ($search_keyword) {
                    $query->whereRaw('LOWER(categories.name) like "%' . strtolower($search_keyword) . '%"');
                });
                if($search_keyword!=''){
                    $query->orWhere(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(products.name) like "%'.strtolower($search_keyword).'%"');
                        $query->orWhereRaw('LOWER(products.description) like "%'.strtolower($search_keyword).'%"');
                        $query->orWhereRaw('LOWER(products.city) like "%'.strtolower($search_keyword).'%"');
                        $query->orWhereRaw('LOWER(products.zip_code) like "%'.strtolower($search_keyword).'%"');
                        
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
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            // ->orWhere(function ($query) use ($search_keyword) {})
            // ->where(function($query) use($search_keyword){                })
            ->orderByRaw($productSortBy)
            ->paginate(10);
            $response['products'] = $products->items();
            // prd($response['products']);
            $response['paging'] = [
                'total_item'=>$products->total(),
                'item_per_page'=>$products->perPage(),
                'current_page'=>$products->currentPage(),
                'last_page'=>$products->lastPage(),
                'prev'=>$products->previousPageUrl(),
                'next'=>$products->nextPageUrl(),
                'first'=>$products->url(1),
                'last'=>$products->url($products->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : product-status
     * @summary : Api for update product status
     * @param : product_id, status
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 March 2022
     */
    public function productStatus(Request $request){
        $user = Auth::guard('api')->user();
        $request['user_id'] = $user->id;
        $response = [];
        if(empty($request->product_id) || $request->status == NULL){ throw new \Exception(__('api-messages.6000')); }
        try{
            Product::where('id', '=', $request->product_id)->update(['status' => $request->status]);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(651,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-postboard-event
     * @summary : Get All Product for Post board not including logged in user 
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 March 2022
     */
    public function getPostboardEvent(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $userId = Auth::user()->id;
        $search_keyword = '';
        $blockUserIds = getBlockUserIds($userId);
        // echo $blockUserIds;exit;
        if($blockUserIds == ''){
            $blockUserIds = "''";
        }
        // echo $user->id;exit;
        try{
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $eventSortBy = 'events.date asc';
            $userId = $user->id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('events.id','events.name','events.user_id','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            ->where(function($query) use($userId,$search_keyword,$blockUserIds){
                $query->whereRaw('events.user_id NOT IN ('.$userId.') AND events.user_id NOT IN ('.$blockUserIds.')');
                $query->where('events.admin_status', 1);
                $query->whereRaw('events.deleted_at IS NULL');
                $query->whereRaw('events.end_time >= "'.gmdate('Y-m-d H:i:s').'"');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                    });
                }
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($eventSortBy)
            ->paginate(10);
            $response['events'] = $events->items();
            // prd($response['events']);
            $response['paging'] = [
                'total_item'=>$events->total(),
                'item_per_page'=>$events->perPage(),
                'current_page'=>$events->currentPage(),
                'last_page'=>$events->lastPage(),
                'prev'=>$events->previousPageUrl(),
                'next'=>$events->nextPageUrl(),
                'first'=>$events->url(1),
                'last'=>$events->url($events->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : save-event
     * @summary : Api for mark as post as save
     * @param : event_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 23 March 2022
     */
    public function saveEvent(SaveEventRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $savedEvent = new SavedEvent();
            $savedEvent['event_id'] = $request->event_id;
            $savedEvent['user_id'] = $request->user_id;
            $savedEvent->save();
            $response['save_event_id'] = $savedEvent->id;
            $eventUserId = getEventUserId($request->event_id);
            // Notification Send
            // sendNotificationWithData($request->user_id,$eventUserId,7,['event_id'=>$request->event_id,'notification_type'=>7]);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(654,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : remove-save-event
     * @summary : Api for mark as event as remove from saved
     * @param : event_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 23 March 2022
     */
    public function unsaveEvent(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->event_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $savedEvent = SavedEvent::where(['event_id'=>$request->event_id,'user_id'=>$request->user_id])->first();
            if(empty($savedEvent)){
                return getResponse(653,400);
            }
            $savedEvent->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(655,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-saved-events
     * @summary : Get All Saved Events By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 23 March 2021
     */
    public function getSavedEvents(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $search_keyword = '';
        // echo $user->id;exit;
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $postSortBy = 'id desc';    
            $userId = $request->user_id;
            // echo $userId;exit;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $events = SavedEvent::with([
                'getEvent'=>function($query) use($user,$blockUserIds){
                    $query->select(['events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.created_at','events.admin_status','events.image']);
                    $query->whereRaw('events.deleted_at IS NULL');
                    $query->whereNotIn('events.user_id', [$blockUserIds]);
                },
                'getEvent.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    $query->whereRaw('users.deleted_at IS NULL');
                    
                }
            ])
            ->select('saved_events.id','saved_events.user_id','saved_events.event_id','saved_events.created_at')
            ->where(function ($query) use ($search_keyword) {
                if($search_keyword !=''){
                    $query->with('getEvent')->whereHas('getEvent', function ($query) use ($search_keyword) {
                        $query->whereRaw('LOWER(events.name) like "%' . strtolower($search_keyword) . '%"');
                    });
                }
            })
            ->where(function($query) use($userId){
                $query->where('saved_events.user_id', $userId);
            })
            ->whereHas('getEvent.getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($postSortBy)
            ->paginate(10);
            // ->toArray();
            $NewEvents = $events->getCollection()->transform(function ($value) {
                // Your code here
                if(isset($value['getEvent']))
                    return $value['getEvent'];
            });
            $NewEvents = $NewEvents->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);
            $response['events'] = $NewEvents->toArray();
            $response['paging'] = [
                'total_item'=>$events->total(),
                'item_per_page'=>$events->perPage(),
                'current_page'=>$events->currentPage(),
                'last_page'=>$events->lastPage(),
                'prev'=>$events->previousPageUrl(),
                'next'=>$events->nextPageUrl(),
                'first'=>$events->url(1),
                'last'=>$events->url($events->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : get-avaialable-event
     * @summary : Api for mark as event as remove from saved
     * @param : event_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 23 March 2022
     */
    public function getAvailableEvent(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            $data = Array();
            $timeoffset = $request->header('timeoffset');
            $data = getAvailableEvent($timeoffset,$user->id);
            // prd($data);
            if(empty($data)){
                return getResponse(653,200);
            }
            $data['user_id'] = $user->id;
            if(!empty($data)){
                $name = str_replace(' ','_',$data['name']);
                $data['token'] = getAgoraToken($name,$data['id'],$data['user_id'],$data['isOwner'],3600);
                $data['recToken'] = getAgoraToken($name,$data['id'],1,0,3600);

            }            
            $userFolderName = config('constants.user_image.upload_path');
            $data['path']['user_media'] = getS3Url($userFolderName);
            $response = $data;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }

    public function sendNotificationEvent(Request $request){
        $setting = getGlobalSetting();
        if($setting['is_agora_active'] == 0){
            return 1;
        }
        $followerUserIds = Array();
        $savedUserIds = Array();
        //  
        /***** Send Notification to admin of Event before 10 min */
        $ownerEvent = Event::with([
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.device_id','users.device_type']);
            }
            ])->select(['*'])
            ->whereRaw('DATE(start_time) = "'.gmdate('Y-m-d').'" AND start_time = (UTC_TIMESTAMP() + INTERVAL 10 MINUTE) AND admin_status = 1 AND status = 1 AND deleted_at IS NULL')
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw('id desc')
            ->first();
        // var_dump($ownerEvent);
        // prd($ownerEvent);
        if($ownerEvent != NULL){
            $data = $ownerEvent->toArray();
            $userData = $data['get_user'];
            $fcm_token = $userData['device_id'];
            $message = 'Your event '.$data['name'].' will start after 10 min.';
            // echo $message;exit;
            $returnData['payload'] = ['notification_type'=>12,'event_id'=>$data['id'],'user_id'=>$data['user_id']];
            $extra = sendFcmPushNotification($fcm_token, $message, $returnData,$userData['device_type']);
        }
        /***** Send Notification to admin of Event before 10 min */
        /***** Send Notification to all User of follows admin user */
        // AND start_time = (UTC_TIMESTAMP())
        $userEvent = Event::with([
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.device_id','users.device_type']);
            }
            ])->select(['*'])
            ->whereRaw('DATE(start_time) = "'.gmdate('Y-m-d').'" AND start_time = (UTC_TIMESTAMP()) AND admin_status = 1 AND status = 1 AND deleted_at IS NULL')
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw('id desc')
            ->first();
        // prd($userEvent);   
        if($userEvent != NULL){
            $data = $userEvent->toArray();
            $eventId = $data['id'];
            $userData = $data['get_user'];
            $userId = $data['user_id'];
            $full_name = trim($userData['first_name']).''.trim($userData['last_name']);
            $followerUsers = UserFollow::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.device_id','users.device_type']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->where(function($query) use($userId){
                $query->where('users_follows.follower_id', $userId);
                $query->whereRaw('users_follows.status = 1');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->get()
            ->toArray();
            // prd($followerUsers);
            /******** Send notification to brodcaster */
            $message = 'Your event '.$data['name'].' is started.';
            $returnData['payload'] = ['notification_type'=>13,'event_id'=>$data['id'],'user_id'=>$data['user_id']];
            $fcm_token = $userData['device_id'];
            $extra = sendFcmPushNotification($fcm_token, $message, $returnData,$userData['device_type']);
            // prd($extra);
            /******** Send notification to brodcaster */
            if(!empty($followerUsers)){
                // prd($followerUsers);
                $message = $full_name.' is live now for event '.$data['name'].'.';
                $returnData['payload'] = ['notification_type'=>14,'event_id'=>$data['id'],'user_id'=>$data['user_id']];
                foreach ($followerUsers as $key => $value) {
                    // if($value['get_user']['id']!=$data['user_id'])
                    {
                        $fcm_token = $value['get_user']['device_id'];
                        $extra = sendFcmPushNotification($fcm_token, $message, $returnData,$value['get_user']['device_type']);
                        $followerUserIds[] = $value['get_user']['id'];
                    }
                }
            }
            /******  Send Notification to Saved event  */
            $savedEvents = SavedEvent::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.device_id','users.device_type']);
                    $query->whereRaw('users.deleted_at IS NULL');
                    
                }
            ])
            ->select('saved_events.id','saved_events.user_id','saved_events.event_id','saved_events.created_at')
            ->where(function($query) use($eventId){
                $query->where('saved_events.event_id', $eventId);
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->get()
            ->toArray();
            // prd($followerUserIds);
            // prd($savedEvents);
            if(!empty($savedEvents)){
                $returnData['payload'] = ['notification_type'=>15,'event_id'=>$data['id'],'user_id'=>$data['user_id']];
                $message = $full_name.' is live now for event '.$data['name'].'.';
                foreach ($savedEvents as $save_key => $save_value) {
                    if(isset($save_value['get_user']) && !empty($save_value['get_user'])){
                        // prd($save_value);
                        if(!in_array($save_value['get_user']['id'],$followerUserIds)){
                            $fcm_token = $save_value['get_user']['device_id'];
                            $extra = sendFcmPushNotification($fcm_token, $message, $returnData,$save_value['get_user']['device_type']);
                            $savedUserIds[] = $save_value['get_user']['id'];

                        }
                    }
                } 
            }
            /******  Send Notification to Saved event  */
        }
        // prd($savedUserIds);
        /***** Send Notification to all User of follows admin user */


        /****** Change status of User story after 24 hrs    */
        $usersStories = UserStory::with([
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.device_id','users.device_type']);
            }
            ])->select(['*'])
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->whereRaw('is_visible = 1 AND deleted_at IS NULL')
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw('id desc')
            ->get();
        $usersStories->each(function ($item){
            $item->is_visible = 2;
            $item->save();
        });
        /****** Change status of User story after 24 hrs    */

        /* $data = ['ownerEvent'=>$ownerEvent,'userEvent'=>$userEvent,'gmdate'=> gmdate('Y-m-d H:i:s')];
        $EmailBodyArr = [
            '{name}' => 'Ashok',
            '{data}' => json_encode($data)
        ];
        $message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br><br>{data}.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
        $templateData = ['subject' => 'ISIT Cron Job run','title'=>'ISIT Cron Job run'];
        $data = sendEmail($message,$templateData,$EmailBodyArr,'Ashok','ashok.dadhich@blitzinfosystem.com');
        UserWelcomeEmailJob::dispatch($data); */
    }
    /**
     * @summary : Api for create user stroty
     * @param : media_type, media_file, thumb image, user_id
     * @type : post
     * @return : json
     * @author Ashok Dadhich
     * @created_at 12 September 2021
     */
    public function createStory(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(!($request->hasFile('file')) || empty($request->file)){ throw new \Exception(__('api-messages.6000')); }
            $story = new UserStory();
            $user = Auth::user();
            $story->user_id = $user->id;
            $story->is_visible = 1;
            $destinationPath = config('constants.story_media.upload_path');
            if($request->hasFile('file')){
                $file = $request->file('file');
                if($request['media_type'] == 2){
                    // prd($file[0]->getMimeType());
                    $extension = $file[0]->extension();    
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file[0]));
                    $story->media_name = $fileName;
                    // echo $story->file_name;exit;
                    // if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif')
                    {
                        $extension = $file[1]->extension();    
                        $fileName = 'thumb_'.rand().time().'.'.$extension;
                        $pathToUpload = $destinationPath.'/'.$fileName;
                        Storage::disk('s3')->put($pathToUpload, file_get_contents($file[1]));
                        // $file[1]->move($destinationPath,$fileName);
                        $story->thumb_image_name = $fileName;
                    }
                }else{
                    
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    $story->media_name = $fileName;

                }
            }  
            $story->media_type = $request['media_type'];
            $story->width = ($request->filled('width') ? $request['width'] : 0);
            $story->height = ($request->filled('height') ? $request['height'] : 0);
            $story->save();
            $StoryId = $story->id;            
            $response['story_id'] = $StoryId;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse(665,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : delete-story
     * @summary : Api for delete the story
     * @param : story_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 12 September 2022
     */
    public function deleteStory(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->story_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $story = UserStory::findOrFail($request->story_id);
            if(empty($story)){
                return getResponse(112,400);
            }
            $destinationPath = config('constants.story_media.upload_path');
            if($story->media_type == 2 && isset($story->thumb_image_name) && $story->thumb_image_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$story->thumb_image_name)){
                Storage::disk('s3')->delete($destinationPath.'/'.$story->thumb_image_name);
            }
            if(isset($story->media_name) && $story->media_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$story->media_name)){
                Storage::disk('s3')->delete($destinationPath.'/'.$story->media_name);
            }
            $story->deleted_at = date('Y-m-d H:i:s');
            $story->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(666,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : user-story-list-v2
     * @summary : Api for user story list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 12 September 2022
     */
    public function userStoryListV2(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $storyFolderName = config('constants.story_media.upload_path');
        $response['path']['story_media'] = getS3Url($storyFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        $search_keyword = '';
        $NewUsers = Array();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $setting = getGlobalSetting();
            $userSortBy = 'users_stories.id asc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getFollower'=>function($query) use($user){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.username','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
                'getFollowerStories'=>function($query) use($user,$userSortBy){
                    $query->select(['users_stories.id','users_stories.user_id','users_stories.media_name','users_stories.media_type','users_stories.thumb_image_name','users_stories.width','users_stories.height','users_stories.created_at']);
                    $query->whereRaw('users_stories.deleted_at IS NULL AND users_stories.is_visible = 1');
                    $query->orderByRaw($userSortBy);
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getFollower')
            ->whereHas('getFollowerStories')
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
                $query->where('users_follows.status', 1);
            })
            ->get();
            $userOwnStory = getUserStories($request->user_id);
            if(!empty($userOwnStory)){
                $NewUsers[] = $userOwnStory;
            }
            if($users !=NULL){
                $userData = $users->toArray();
                // prd($userData);
                foreach($userData as $key => $value){
                    $arr = Array();
                    // prd($value['get_follower']);
                    if(isset($value['get_follower_stories']) && !empty($value['get_follower_stories'])){
                        if(isset($value['get_follower']) && !empty($value['get_follower'])){
                            $arr['User']['id'] =  $value['get_follower']['id'];
                            $arr['User']['first_name'] =  $value['get_follower']['first_name'];
                            $arr['User']['last_name'] =  $value['get_follower']['last_name'];
                            $arr['User']['profile_picture'] =  $value['get_follower']['profile_picture'];
                            $arr['User']['is_public_visibility'] =  $value['get_follower']['is_public_visibility'];
                            $arr['User']['twilio_s_id'] =  $value['get_follower']['twilio_s_id'];
                            $arr['User']['username'] =  $value['get_follower']['username'];
                            $lastKey = array_key_last ($value['get_follower_stories']);
                            $arr['lastCreatedAt'] = $value['get_follower_stories'][$lastKey]['created_at'];
                            $arr['random'] = rand(100,1000);
                            $arr['lastSeenIndex'] = 0;
                        }
                        // echo "If";exit;
                        $arr['Stories'] = $value['get_follower_stories'];
                        if(!empty($arr['Stories'])){
                            $stoArr = $arr['Stories'];
                            $i = 0;
                            $arrRange=range(0, count($arr['Stories']), 1);
                            shuffle($arrRange); // randomize the order
                            $_2indexArr = array_slice($arrRange, 0, 2);
                            $finalResult = Array();
                            $isViewFound = 0;
                            foreach($stoArr as $key => $value){
                                if($setting['is_ads_active'] == 1){
                                    if(in_array($key, $_2indexArr)){
                                        // echo "123";exit;
                                        $finalResult[$i] = getAdsBlankObj(1);
                                        $i++;
                                        // prd($finalResult);
                                    }
                                }
                                if($isViewFound == 0 && $value['is_viewed'] == 0){
                                    $arr['lastSeenIndex'] = $i;
                                    $isViewFound = 1;
                                }
                                $finalResult[$i] = $value;
                                $i++;
                            }
                        }
                        $arr['Stories'] = $finalResult;
                        $NewUsers[] = $arr;
                    }                
                }
            }
            // prd($NewUsers);
            $NewUser1 = sort_array_of_array($NewUsers,'lastCreatedAt');
            // prd($NewUsers);
            $response['data'] = $NewUsers;
            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : like-user-story
     * @summary : Api for like a user stories
     * @param : story_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 24 Nov 2022
     */
    public function likeUserStory(LikeStoryRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isDeleted = isStoryDeleted($request->story_id);
            // echo $isDeleted;exit;
            if($isDeleted == 1){
                return getResponse(671,400);
            }
            $storyUserId = getStoryUserId($request->story_id);
            $isBlocked = getBlockByUser($user->id,$storyUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = new UserStoryLike();
            $savedPost['story_id'] = $request->story_id;
            $savedPost['user_id'] = $request->user_id;
            $savedPost->save();
            $response['story_like_id'] = $savedPost->id;
            // Notification Send
            if($storyUserId != $request->user_id){
                // sendNotificationWithData($request->user_id,$storyUserId,20,['story_id'=>$request->story_id,'notification_type'=>20]);
            }
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(673,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unlike-user-story
     * @summary : Api for remove from like user stories
     * @param : story_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 24 Nov 2022
     */
    public function unlikeStory(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->story_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isDeleted = isStoryDeleted($request->story_id);
            if($isDeleted == 1){
                return getResponse(671,400);
            }
            $storyUserId = getStoryUserId($request->story_id);
            $isBlocked = getBlockByUser($user->id,$storyUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $likedStory = UserStoryLike::where(['story_id'=>$request->story_id,'user_id'=>$request->user_id])->first();
            if(empty($likedStory)){
                return getResponse(672,400);
            }
            $likedStory->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(674,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-liked-story-user-list
     * @summary : Get All user stories like listing
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 24 Nov 2022
     */
    public function getLikedStoryUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->story_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isStoryDeleted($request->story_id);
            if($isDeleted == 1){
                return getResponse(671,400);
            }

            $likeSortBy = 'id desc';
            $storyId = $request->story_id;
            $users = UserStoryLike::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('users_stories_likes.id','users_stories_likes.user_id','users_stories_likes.story_id','users_stories_likes.created_at')
            ->where(function($query) use($storyId){
                $query->where('users_stories_likes.story_id', $storyId);
            })
            ->orderByRaw($likeSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr = $value['getUser'];
                    $arr['createdAt'] = $value['created_at'];
                }
                return $arr;
            });
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);
            $response['users'] = $NewUsers->toArray();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : view-user-story
     * @summary : Api for view a user stories
     * @param : story_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 05 Dec 2022
     */
    public function viewUserStory(ViewStoryRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isDeleted = isStoryDeleted($request->story_id);
            // echo $isDeleted;exit;
            if($isDeleted == 1){
                return getResponse(671,400);
            }
            $storyUserId = getStoryUserId($request->story_id);
            $isBlocked = getBlockByUser($user->id,$storyUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = new UserStoryView();
            $savedPost['story_id'] = $request->story_id;
            $savedPost['user_id'] = $request->user_id;
            $savedPost->save();
            $response['story_view_id'] = $savedPost->id;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(675,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-viewed-story-user-list
     * @summary : Get All user stories like listing
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 24 Nov 2022
     */
    public function getViewedStoryUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->story_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isStoryDeleted($request->story_id);
            if($isDeleted == 1){
                return getResponse(671,400);
            }

            $likeSortBy = 'id desc';
            $storyId = $request->story_id;
            $users = UserStoryView::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('users_stories_views.id','users_stories_views.user_id','users_stories_views.story_id','users_stories_views.created_at')
            ->where(function($query) use($storyId){
                $query->where('users_stories_views.story_id', $storyId);
            })
            ->orderByRaw($likeSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr = $value['getUser'];
                    $arr['createdAt'] = $value['created_at'];
                }
                return $arr;
            });
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);
            $response['users'] = $NewUsers->toArray();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : user-story-list
     * @summary : Api for user story list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 12 September 2022
     */
    public function userStoryList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $storyFolderName = config('constants.story_media.upload_path');
        $response['path']['story_media'] = getS3Url($storyFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        $search_keyword = '';
        $NewUsers = Array();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'users_stories.id asc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getFollower'=>function($query) use($user){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.username']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
                'getFollowerStories'=>function($query) use($user,$userSortBy){
                    $query->select(['users_stories.id','users_stories.user_id','users_stories.media_name','users_stories.media_type','users_stories.thumb_image_name','users_stories.width','users_stories.height','users_stories.created_at']);
                    $query->whereRaw('users_stories.deleted_at IS NULL AND users_stories.is_visible = 1');
                    $query->orderByRaw($userSortBy);
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getFollower')
            ->whereHas('getFollowerStories')
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
                $query->where('users_follows.status', 1);
            })
            ->get();
            $userOwnStory = getUserStories($request->user_id);
            if(!empty($userOwnStory)){
                $NewUsers[] = $userOwnStory;
            }
            if($users !=NULL){
                $userData = $users->toArray();
                // prd($userData);
                foreach($userData as $key => $value){
                    $arr = Array();
                    // prd($value['get_follower']);
                    if(isset($value['get_follower_stories']) && !empty($value['get_follower_stories'])){
                        if(isset($value['get_follower']) && !empty($value['get_follower'])){
                            $arr['User']['id'] =  $value['get_follower']['id'];
                            $arr['User']['first_name'] =  $value['get_follower']['first_name'];
                            $arr['User']['last_name'] =  $value['get_follower']['last_name'];
                            $arr['User']['profile_picture'] =  $value['get_follower']['profile_picture'];
                            $arr['User']['is_public_visibility'] =  $value['get_follower']['is_public_visibility'];
                            $arr['User']['twilio_s_id'] =  $value['get_follower']['twilio_s_id'];
                            $arr['User']['username'] =  $value['get_follower']['username'];
                            $lastKey = array_key_last ($value['get_follower_stories']);
                            $arr['lastCreatedAt'] = $value['get_follower_stories'][$lastKey]['created_at'];
                            $arr['random'] = rand(100,1000);
                            
                        }
                        // echo "If";exit;
                        $arr['Stories'] = $value['get_follower_stories'];
                        $NewUsers[] = $arr;
                    }                
                }
            }
            // prd($NewUsers);
            $NewUser1 = sort_array_of_array($NewUsers,'lastCreatedAt');
            // prd($NewUsers);
            $response['data'] = $NewUsers;
            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
}
