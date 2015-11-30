<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 29.04.2015
 * Time: 14:29
 */

class Nl2go_ResponseHelper {

    /**
     * err-number, that should be pulled, whenever credentials are missing
     */
    const ERRNO_PLUGIN_CREDENTIALS_MISSING = 'int-1-404';
    /**
     *err-number, that should be pulled, whenever credentials are wrong
     */
    const ERRNO_PLUGIN_CREDENTIALS_WRONG = 'int-1-403';
    /**
     * err-number for all other (intern) errors. More Details to the failure should be added to error-message
     */
    const ERRNO_PLUGIN_OTHER = 'int-1-600';



    static function generateErrorResponse($message, $errorCode, $context =null ){
        $res =  array(
            'success' => false,
            'message' =>$message,
        'errorcode' => $errorCode
        );
        if($context != null){
            $res['context'] = $context;
        }
        return json_encode($res);
    }

    static function generateSuccessResponse($data= array()){
        $res =  array('success' =>true, 'message' => 'OK');
       return json_encode(array_merge($res, $data));
    }

}