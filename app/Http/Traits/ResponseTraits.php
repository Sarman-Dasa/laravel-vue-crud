<?php
namespace App\Http\Traits;
use Illuminate\Support\Str;

    trait ResponseTraits{
        
        public function sendValidationError($validation)
        {
            return response()->json(['status'=>true,'message'=>'Validation Error','error'=>$validation->errors()]);
        }

        public function sendSuccessResponse($message,$data="")
        {
            return response()->json(['status'=>true,'message'=>$message,'data'=>$data],200);
        }

        public function sendFailureResponse($message)
        {
            return response()->json(['status'=>false,'message'=>$message],401);
        }

        public function sendExecptionMessage($ex)
        {
            $boolean = Str::contains($ex->getMessage(), 'Duplicate entry');
           if($boolean)
           {
                return response()->json(['status'=>false,'message'=>"Data Duplicate Error"],500);
           }
           else
           {
                return response()->json(['status'=>false,'message'=>$ex->getMessage()],500);
           }
        }

        public function dataNotFound($message)
        {
            return response()->json(['status'=>false,'message'=>$message],404);
        }

        public function sendFilterListData($query, $searching_Fields)
        {  
            if(request()->search){
                $search = request()->search;
                $query = $query->where(function($query) use($search ,$searching_Fields){
                    foreach ($searching_Fields as $searching_Field) {
                         $query->orWhere($searching_Field,'like','%'.$search.'%');  
                    }
                });
            }

            if(request()->sort)
            {
                $sortOrder = request()->sortOrder == 'desc' ? 'desc': 'asc';
                $query->orderBy(request()->sort,$sortOrder);
            }

            $pagination = request()->perPageData ?? 10;
            $list = $query->paginate($pagination);
            return response()->json(['status'=>true,'message'=>'Data get successfully.','data'=>$list],200);
        }
    }
?>