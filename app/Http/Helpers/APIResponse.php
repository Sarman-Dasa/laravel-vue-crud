<?php
/**
 * Api response helper file 
 * This helper needs to register in composer.json file under the "autoload-dev > files" section
 * "files": [ "app/Http/Helpers/APIResponse.php" ] 
 */

/**
 * Return success response
 *
 * @param int    $status
 * @param string $message
 * @param array  $data
 */
if(!function_exists('ok')){
    function ok($message = null, $data = [], $status = 200,)
    {
        $response = [
            'status'    =>  $status,
            'message'   =>  $message ?? 'Process is successfully completed',
            'data'      =>  $data
        ];

        return response()->json($response,$status);
    }
}
/**
 * Return all type of error response with different status code
 *
 * @param string $message
 * @param array  $data
 * @param string $type = validation | unauthenticated | notfound | forbidden | internal server
 */
if(!function_exists('error')){
    function error($message = null, $data = [], $type = null)
    {
        $status = 500;

        switch ($type) {
            case 'validation':
                $status  = 422;
                $message =   $message ?? 'Validation Failed please check the request attributes and try again';
            break;

            case 'unauthenticated':
                $status  = 401;
                $message =  $message ?? 'User token has been expired';
            break;

            case 'notfound':
                $status  = 404;
                $message = $message ?? 'Sorry no results query for your request';
            break;

            case 'forbidden':
                $status  = 403;
                $message =  $message ??  'You don\'t have permission to access this content';
            break;

            default:
                $status = 500;
                $message ?? $message = 'Server error, please try again later';
            break;
        }

        $response = [
            'status'    =>  $status,
            'message'   =>  $message,
            'data'      =>  $data
        ];

        return response()->json($response,$status);
    }
}