<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Validations\UserValidation;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

// == DECLARATION
    private $validateRequests, $userRepository;
    public function __construct(UserValidation $validateRequests, UserRepository $userRepository)
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);


        $this->middleware('permission:get-user-by-id', ['only' => ['getUserById']]);
        $this->middleware('permission:get-all-users', ['only' => ['getAllUsers']]);

        $this->validateRequests = $validateRequests;
        $this->userRepository = $userRepository;
    }
//

// == GET

    // ----- get self user
    /**
     * @OA\Get(
     * path="/user/get-self-user",
     * operationId="getSelfUser",
     * tags={"Users"},
     * summary="Get a self user's personal information",
     * security={{"bearerToken":{}}},
     *
     *      @OA\Response(
     *          response="200",
     *          description="Successful Operation",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *        ),
     *
     *
     *
     *
     *       @OA\Response(
     *          response="422",
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *       ),
     *
     * )
     */
    function getSelfUser()
    {
        try {
            $user = $this->userRepository->getUserById(Auth::user()->id);
            return $this->handleReturn(true, $user, null);
        }
        catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get user by id
    /**
    * @OA\Get(
    * path="/admin/get-user-by-id/{id}",
    * operationId="getUserById",
    * tags={"Users"},
    * summary="Get a specific user's self personal information",
    * security={{"bearerToken":{}}},
    *        @OA\Parameter(
    *           name="id",
    *           description="id",
    *           required=true,
    *           in="path",
    *           @OA\Schema(
    *                 type="integer"
    *              )
    *      ),
    *      @OA\Response(
    *          response="200",
    *          description="Successful Operation",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data", type="object", description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *        ),
    *
    *
    *
    *
    *       @OA\Response(
    *          response="422",
    *          description="Unprocessable Entity",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *
    *       ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad Request",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *       ),
    *
    * )
    */
    function getUserById($id)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateUserId($id);
            if ($validation->fails())
            return $this->handleReturn(false, null, $validation->errors()->first());


            $user = $this->userRepository->getUserById($id);
            return $user ? $this->handleReturn(true, $user, null) :  $this->handleReturn(false, null, "invalid id");
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get all users
    /**
    * @OA\Get(
    * path="/admin/get-all-users",
    * operationId="getAllUsers",
    * tags={"Users"},
    * summary="Get all users personal information",
    * security={{"bearerToken":{}}},
    *      @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="name",
    *         required=false,
    *      ),
    *     @OA\Parameter(
    *         name="sort",
    *         in="query",
    *         description="sorting by newest to oldest => 1, oldest to newest =>2, A to Z => 3, Z to A =>4",
    *         required=false,
    *      ),
    *      @OA\Parameter(
    *           name="per_page",
    *           description="number of data per page",
    *           required=false,
    *           in="query",
    *           @OA\Schema(
    *                 type="integer"
    *              )
    *      ),
    *      @OA\Response(
    *          response="200",
    *          description="Successful Operation",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data", type="object", description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *        ),
    *       @OA\Response(
    *          response="422",
    *          description="Unprocessable Entity",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *
    *       ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad Request",
    *          @OA\JsonContent(
    *          type="object",
    *          @OA\Property(property="success", type="boolean", description="status" ),
    *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
    *          @OA\Property(property="message", type="string", description="message" ),
    *          ),
    *       ),
    *
    * )
    */
    function getAllUsers(Request $request)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateGetAllUsers();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            $users = $this->userRepository->getAllUsers($request);
            return $this->handleReturn(true, $users, null);
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- logout
    /**
     * @OA\Get(
     *      path="/auth/logout",
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="logout user",
     *      security={{"bearerToken":{}}},
     *      @OA\Response(
     *          response="200",
     *          description="Logout Successfully",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", default="User successfully signed out" ),
     *          ),
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    function logout()
    {
        auth()->logout();
        return $this->handleReturn(true, null, 'User successfully signed out');
    }



//

// == EDIT

    // ----- login
    /**
     * @OA\Post(
     * path="/auth/login",
     * operationId="Login",
     * tags={"Authentication"},
     * summary="Login to the system",
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed login to the system",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="email",description="email"),
     *               @OA\Property(property="password",description="password"),
     *
     *
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful Operation",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *        ),
     *
     *
     *
     *
     *       @OA\Response(
     *          response="422",
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data",type="array",  @OA\Items( type="object"  ),description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *       ),
     *
     * )
     */

    function login(Request $request)
    {
        try {
            $validator = $this->validateRequests->loginValidation();
            if ($validator->fails())
                    return $this->handleReturn(false, null, $validator->errors()->first());

            $inputs = [
                "email" => strtolower(request()->email),
                "password" => request()->password
            ];
            $token = Auth::attempt($inputs);

            if (!$token) {
                return $this->handleReturn(false, null, "Invalid email or password");
            }
            return $this->handleReturn(true, $this->createNewToken($token), "Logged in successfully");
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- create token
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL(),
            'user' => User::where('id', Auth::user()->id)->with('role')->get()
        ]);
    }

    // ----- registration
    /**
     * @OA\Post(
     * path="/auth/registration",
     * operationId="registration",
     * tags={"Authentication"},
     * summary="User Registration",
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to registrate",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="name", description="full_name"),
     *               @OA\Property(property="email",description="email"),
     *               @OA\Property(property="password",type="string", description="password"),
     *               @OA\Property(property="phone_number",description="phone_number"),
     *               @OA\Property(property="date_of_birth",type="string",description="date_of_birth")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful Operation",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *        ),
     * )
     */
    function registration(Request $request)
    {
        try{
            //-- validation
            $validation =  $this->validateRequests->validateRegistration();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();
            $user = $this->userRepository->createUser($request);
            DB::commit();
            return $this->handleReturn(true, $user, "Created successfully");
        }catch(Exception $ex){
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- change password
    /**
     * @OA\Put(
     * path="/auth/change-password",
     * operationId="changePassword",
     * tags={"Authentication"},
     * summary="Update account password",
     * security={{"bearerToken":{}}},
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to update account password",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="current_password",description="current_password"),
     *               @OA\Property(property="password",description="password")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful Operation",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *        ),
     * )
     */
    function changePassword(Request $request)
    {
        try{
            //-- validation
            $validation =  $this->validateRequests->validateChangePassword();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());


            //-- check current password if matches entered current password
            $check_current_password = Hash::check($request->current_password, Auth::user()->password);
            if (!$check_current_password) {
                return $this->handleReturn(false, null, "Passwords doesn't match");
            }

            //-- change password
            $this->userRepository->changePassword($request);

            return $this->handleReturn(true, null, "Updated successfully");
        }
        catch(Exception $ex){
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- update self user
    /**
     * @OA\Put(
     * path="/user/update-self-user",
     * operationId="updateSelfUser",
     * tags={"Users"},
     * summary="Edit user's personal information",
     * security={{"bearerToken":{}}},
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to edit user's personal information",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="name", description="name"),
     *               @OA\Property(property="email",type="string", description="country_id"),
     *               @OA\Property(property="phone_number",description="phone_number"),
     *               @OA\Property(property="date_of_birth",type="string",description="date"),
     *
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful Operation",
     *          @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="success", type="boolean", description="status" ),
     *          @OA\Property(property="data", type="object", description="data" ),
     *          @OA\Property(property="message", type="string", description="message" ),
     *          ),
     *        ),
     * )
     */
    function updateSelfUser(Request $request)
    {
        try {
            //-- validation
            $validation =  $this->validateRequests->validateUpdateSelfUser();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();

            //-- update user
            $request->id = Auth::user()->id;
            $this->userRepository->updateUser($request);

            DB::commit();
            return $this->handleReturn(true, null, "Updated successfully");

        } catch (Exception $ex) {
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

//

}
