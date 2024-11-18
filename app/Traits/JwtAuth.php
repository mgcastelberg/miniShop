<?php
namespace App\Traits;

use App\Models\User;
use DomainException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;
use App\Models\tenant\Api_user;
use Illuminate\Support\Facades\Hash;

trait JwtAuth{

    public $key;

    public function __construct(){
        $this->key = 'api-rest-laravel-cl4v3-s3cr3t4-1842523131112';
    }

    public function jwtEncode($payload=null)
    {
        if($payload){
            $payload['iat'] = time(); // Fecha de emisión (en segundos)
            $payload['exp'] = time() + 360; // Expiración (1 hora después de la emisión)
            return JWT::encode($payload, $this->key, 'HS256');
        }else{
            return array('status' => 'error', 'message' => 'Login a fallado');
        }
    }

    public function jwtDecode($jwt, $getIdentity = false){

        $auth = false;

        try {
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        } catch (UnexpectedValueException $e) {
            $auth = false; //throw $e;
        } catch (DomainException $e) {
            $auth = false;
        }

        if(isset($decoded) && is_object($decoded)){
            $auth = true;
        }else {
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }
}
