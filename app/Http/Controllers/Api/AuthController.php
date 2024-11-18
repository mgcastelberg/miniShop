<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Traits\JwtAuth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use JwtAuth;
    use ApiResponser;

    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => ['required', 'max:60'],
                'password' => ['required', 'max:50']
            ]);

            if ($validator->fails()) {
                return $this->errorFormApiResponse($validator->errors(), 422);
            }

            $email = $request->input('email');
            $password = $request->input('password');

            $user = User::where('email', $email)->firstOrFail();

            if (password_verify($password, $user->password)) {
                $payload = [
                    'id'    => $user->id,
                    'email'  => $user->email,
                    'fullName'  => $user->name,
                    'isActive'  => true,
                    'roles'  => ['admin']
                ];
                $payload['token'] = $this->jwtEncode($payload);
                return response()->json($payload, 200);
            } else {
                return $this->errorResponse('invalid credentials', 401);
            }
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Error:'. $e->getMessage(), 500 );
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            // Obtener el token actual del encabezado de la solicitud
            $token = $request->bearerToken();

            // Decodificar el token para validar y extraer el payload
            $decoded = $this->jwtDecode($token, true);


            if (!$decoded) {
                return $this->errorResponse('Invalid token', 401);
            }

            // Opcionalmente, puedes verificar que el usuario aún exista en la base de datos
            $user = User::find($decoded->id);

            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            // Generar un nuevo token
            $newPayload = [
                'id'    => $user->id,
                'email'  => $user->email,
                'fullName'  => $user->name,
                'isActive'  => true,
                'roles'  => ['admin']
            ];
            $newPayload['token'] = $this->jwtEncode($newPayload);

            return response()->json($newPayload, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Error: '. $e->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'fullName' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:60'],
        ]);

        if ($validator->fails()) {
            return $this->errorFormApiResponse($validator->errors(), 422);
        }


        try {
            $user = new User;
            $user->email = $request->input('email');
            $user->name = $request->input('fullName');
            $user->password = $request->password ? Hash::make(trim($request->password)) : Hash::make('LocalD4sh');
            $user->save();

            $payload = [
                'id'    => $user->id,
                'email'  => $user->email,
                'fullName'  => $user->name,
                'isActive'  => true,
                'roles'  => ['admin']
            ];
            $payload['token'] = $this->jwtEncode($payload);
            return response()->json($payload, 200);

        } catch (Exception $e) {
            DB::rollback();
            return $this->errorApiResponse('Could not create client', 500, $e->getMessage());
        }
    }

}
