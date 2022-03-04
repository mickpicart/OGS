<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Mockery\Matcher\Any;

class AuthController extends Controller
{
    /**
    * Create a new AuthController instance.
    *
    * @return void
    */

    public function __construct()
    {
        // Exclude three methods : no need to be logged in to login / reset forgotten password
        $this->middleware('auth:api', ['except' => ['login','sendPasswordResetLink', 'callResetPassword']]);
    }


    /**
    * @OA\SecurityScheme(
    *   securityScheme="access_token",
    *   type="http",
    *   scheme="bearer"
    *   )
    */


    /**
    * Get the token array structure.
    *
    * @param string $token
    * @return \Illuminate\Http\JsonResponse
    */

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }


    /**
    * Get a JWT via given credentials.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Post(
    *      path="/auth/login",
    *      operationId="login",
    *      tags={"JWT"},
    *
    *      summary="Create a JWT via given credentials",
    *      description="Returns the JWT",
    *      @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          description="The user email for login",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Parameter(
    *          name="password",
    *          required=true,
    *          in="query",
    *          description="The password for login in clear text",
    *              @OA\Schema(
    *              type="string",
    *              )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="User successfully connected and token created",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *          ),
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request"
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */

    public function login(Request $request)
    {
        // Validate email and password format (minimum 12 characters)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:12',
        ]);

        // If validation fails return json error and 400
        // If validation OK, try to login returning a new token or a json error and 401
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } elseif (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }


    /**
    * Register a User.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/auth/register",
    *      security={{"access_token": {}}},
    *      operationId="register",
    *      tags={"Users"},
    *
    *      summary="Register a new user",
    *      @OA\Parameter(
    *          name="name",
    *          required=true,
    *          in="query",
    *          description="The user firstname and lastname",
    *          @OA\Schema(
    *              type="string"
    *          )
    *      ),
    *      @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          description="The user email that will be used for login",
    *          @OA\Schema(
    *              type="string"
    *          )
    *      ),
    *      @OA\Parameter(
    *          name="password",
    *          required=true,
    *          in="query",
    *          description="The password that will be used for login",
    *          @OA\Schema(
    *              type="string",
    *          )
    *      ),
    *      @OA\Parameter(
    *          name="password_confirmation",
    *          required=true,
    *          in="query",
    *          description="To confirm the password that will be used for login",
    *          @OA\Schema(
    *              type="string",
    *          )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="User successfully registered",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */

    public function register(Request $request)
    {
        // Validate name, email and password format
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:12',
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // If validation OK, create new user in DataBase encrypting the password
        // and return json with user details and 201
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    /**
    * Log the user out (Invalidate the token).
    *
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Post(
    *      path="/logout",
    *      security={{"access_token": {}}},
    *      operationId="logout",
    *      tags={"JWT"},
    *
    *      summary="Sign out user",
    *      description="Revoke JWT",
    *      @OA\Response(
    *          response=200,
    *          description="User successfully signed out",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      )
    *     )
    */

    public function logout()
    {
        // Log user out
        auth()->logout();
        // Invalidate token
        auth()->invalidate(true);

        // Return json message and 200
        return response()->json(['message' => 'User successfully signed out'], 200);
    }


    /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Get(
    *      path="/auth/user-profile",
    *      security={{"access_token": {}}},
    *      operationId="userProfile",
    *      tags={"Users"},
    *
    *      summary="Get the authenticated user",
    *      description="Returns the authenticated user",
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request"
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    *     )
    */

    public function userProfile()
    {
        return response()->json(auth()->user());
    }


    /**
    *   Send password reset link.
    *
    *   @param \Illuminate\Http\Request $request
    *   @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/auth/password/reset-link",
    *      operationId="resetLink",
    *      tags={"Password"},
    *
    *      summary="Send an email containing the password reset link with a 10 min token",
    *      description="Returns password reset email sent status",
    *      @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          description="The user email where reset link will be sent",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Successful operation",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *          ),
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */

    public function sendPasswordResetLink(Request $request)
    {
        // Validate email format
        $validator = Validator::make(['email' => $request->input('email')], [
            'email' => 'required|email|exists:users'
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json(['message' => 'Email could not be sent to this email address.'], 400);
        } else {
            // sendresetLink method has been adapted to have exactly the same token
            // in both url in reset mail link and in DataBase
            Password::sendResetLink(
                $request->only('email'),
                fn ($user, $token) =>
                (DB::table('password_resets')
                    ->updateOrInsert(
                        ['email' => $user->email],
                        ['token' => $token]
                    ))
                    ? $user->sendPasswordResetNotification($token)
                    : null
            );
            return response()->json(['message' => 'Password reset email sent.'], 201);
        }
    }

    /**
    * Handle reset password
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Patch(
    *      path="/auth/password/reset",
    *      operationId="resetPassword",
    *      tags={"Password"},
    *
    *      summary="Modify user password in DB",
    *      description="Returns password reset confirmation",
    *      @OA\Parameter(
    *          name="token",
    *          required=true,
    *          in="query",
    *          description="The token catched from the url in the email",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          description="The user email for which the password has to be changed",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Parameter(
    *          name="password",
    *          required=true,
    *          in="query",
    *          description="New password",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Parameter(
    *          name="password_confirmation",
    *          required=true,
    *          in="query",
    *          description="New password confirmation",
    *              @OA\Schema(
    *              type="string"
    *              )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Successful operation",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *          ),
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */

    public function callResetPassword(Request $request)
    {
        // Validate email, password and password_confirmation format
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:12',
            'password_confirmation' => 'required'
        ]);

        // If validation fails return json error and 422
        if ($validator->fails()) {
            return response()->json(['message' => 'Password could not be reset.'], 422);
        } else {
            // With user email and token from reset password link, we will check if
            // associated line exists in password_resets table
            $updatePassword = DB::table('password_resets')
                            ->where([
                              'email' => $request->email,
                              'token' => $request->token
                            ])
                            ->first();

            // If such line does not exist return json error and 400
            if (!$updatePassword) {
                return response()->json(['message' => 'Failed, invalid Token.'], 400);
            }

            // If such line exists update User table with hashed new password
            User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);
            // Delete line containing user email and token in password-resets table now that
            // the user password has been updated
            DB::table('password_resets')
                ->where(['email'=> $request->email])
                ->delete();

            return response()->json(['message' => 'Password reset successfully.'], 201);
        }
    }
}
