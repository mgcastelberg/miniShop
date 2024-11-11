<?php
namespace App\Traits;
use Illuminate\Http\Response;

 trait ApiResponser
 {
    /**
     * Build a succes response
     * @param string | array | $data
     * @param int $code
     * @return Illuminate\Http\JsonResponse
     */
     public function successLD3Response($data, $code = Response::HTTP_OK){
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'data'=> $data
        ], $code);
     }

     public function successApiResponse($data, $code = Response::HTTP_OK){
        return response()->json([
            'data'=> $data
        ], $code);
     }

     /**
     * Build a error response
     * @param string $message
     * @param int $code
     * @return Illuminate\Http\JsonResponse
     */
     public function errorResponse($message, $code){
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ], $code);
     }

     /**
     * Build a error response
     * @param string $message
     * @param int $code
     * @return Illuminate\Http\JsonResponse
     */
     public function errorApiResponse($title, $code, $meta = null){

        $errors = [];

        $errors[] = [
            'code' => $code,
            'title' => $title,
            'source' => ['pointer' => "/data/attributes/models"],
            'detail' => $meta,
        ];

        $response = [
            'errors' => $errors,
        ];

        return response()->json($response, $code);
     }

     /**
     * Build a error response
     * @param string $message
     * @param int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function errorFormApiResponse($formErrors, $code){

        $errors = [];

        foreach ($formErrors->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'code' => $code,
                    'attribute' => $field,
                    'detail' => $message,
                ];
            }
        }

        $response = [
            'errors' => $errors,
        ];

        return response()->json($response, $code);
     }
 }
