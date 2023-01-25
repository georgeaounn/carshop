<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Validations\CarValidation;
use Illuminate\Support\Facades\DB;
use App\Repositories\CarRepository;

class CarController extends Controller
{
// == DECLARATION
    private $validateRequests, $carRepository;
    public function __construct(CarValidation $validateRequests, CarRepository $carRepository)
    {
        $this->middleware('auth:api', ['except' => []]);


        $this->middleware('permission:create-car', ['only' => ['createCar']]);
        $this->middleware('permission:update-car', ['only' => ['updateCar']]);
        $this->middleware('permission:delete-car', ['only' => ['deleteCar']]);

        $this->validateRequests = $validateRequests;
        $this->carRepository = $carRepository;
    }
//

// == GET

    // ----- get user by id
    /**
     * @OA\Get(
     * path="/car/get-car-by-id/{id}",
     * operationId="getCarById",
     * tags={"Cars"},
     * summary="Get a specific car's information",
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
    function getCarById($id)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateCarId($id);
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());


            $car = $this->carRepository->getCarById($id);
            return $car ? $this->handleReturn(true, $car, null) :  $this->handleReturn(false, null, "Invalid Id");
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get all cars
    /**
     * @OA\Get(
     * path="/car/get-all-cars",
     * operationId="getAllCars",
     * tags={"Cars"},
     * summary="Get all cars information",
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
     *     @OA\Parameter(
     *         name="minimum_price",
     *         in="query",
     *         description="filter by minimum price",
     *         required=false,
     *           @OA\Schema(
     *                 type="integer"
     *              )
     *      ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="filter by max price",
     *         required=false,
     *           @OA\Schema(
     *                 type="integer"
     *              )
     *      ),
     *     @OA\Parameter(
     *          name="brand_ids[]",
     *          in="query",
     *          description="filter by brand ids",
     *          @OA\Schema(
     *            type="array",
     *            @OA\Items(type="integer")
     *          )
     *      ),
     *       @OA\Parameter(
     *          name="type_ids[]",
     *          in="query",
     *          description="filter by type ids",
     *          @OA\Schema(
     *            type="array",
     *            @OA\Items(type="integer")
     *          )
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
    function getAllCars(Request $request)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateGetAllCars();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            $cars = $this->carRepository->getAllCars($request);
            return count($cars) ? $this->handleReturn(true, $cars, null) : $this->handleReturn(true, null, "No cars found");
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get all brands
    /**
     * @OA\Get(
     * path="/car/get-all-brands",
     * operationId="getAllBrands",
     * tags={"Cars"},
     * summary="Get all brands",
     * security={{"bearerToken":{}}},
     *      @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="name",
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
    function getAllBrands(Request $request)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateGetAllBrands();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            $brands = $this->carRepository->getAllBrands($request);
            return $this->handleReturn(true, $brands, null);
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get all types
    /**
     * @OA\Get(
     * path="/car/get-all-types",
     * operationId="getAllTypes",
     * tags={"Cars"},
     * summary="Get all types",
     * security={{"bearerToken":{}}},
     *      @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="name",
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
    function getAllTypes(Request $request)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateGetAllBrands();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            $types = $this->carRepository->getAllTypes($request);
            return $this->handleReturn(true, $types, null);
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

//

// == EDIT

    // ----- create car
    /**
     * @OA\Post(
     * path="/car/create-car",
     * operationId="createCar",
     * tags={"Cars"},
     * summary="Create car",
     * security={{"bearerToken":{}}},
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to create a car",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="name", description="name"),
     *               @OA\Property(property="automatic_transmission",type="boolean",description="transmission"),
     *               @OA\Property(property="color",type="string", description="color"),
     *               @OA\Property(property="fuel_consumption",type="integer",description="fuel_consumption"),
     *               @OA\Property(property="mileage",type="integer",description="mileage"),
     *               @OA\Property(property="price",type="integer",description="price"),
     *               @OA\Property(property="type_id",type="integer",description="type_id"),
     *               @OA\Property(property="brand_id",type="integer",description="brand_id"),
     *               @OA\Property(property="quantity",type="integer",description="quantity"),
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
    function createCar(Request $request)
    {
        try{
            //-- validation
            $validation =  $this->validateRequests->validateCreateCar();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();
            $car = $this->carRepository->createCar($request);
            DB::commit();
            return $this->handleReturn(true, $car, "Created successfully");
        }
        catch(Exception $ex){
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- update car
    /**
     * @OA\Put(
     * path="/car/update-car/{id}",
     * operationId="updateCar",
     * tags={"Cars"},
     * summary="Edit car's information",
     *      @OA\Parameter(
     *           name="id",
     *           description="car_id",
     *           required=true,
     *           in="path",
     *           @OA\Schema(
     *                 type="integer"
     *              )
     *      ),
     * security={{"bearerToken":{}}},
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to edit a car's information",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="name", description="name"),
     *               @OA\Property(property="automatic_transmission",type="boolean",description="transmission"),
     *               @OA\Property(property="color",type="string", description="color"),
     *               @OA\Property(property="fuel_consumption",type="integer",description="fuel_consumption"),
     *               @OA\Property(property="mileage",type="integer",description="mileage"),
     *               @OA\Property(property="price",type="integer",description="price"),
     *               @OA\Property(property="type_id",type="integer",description="type_id"),
     *               @OA\Property(property="brand_id",type="integer",description="brand_id"),
     *               @OA\Property(property="quantity",type="integer",description="quantity"),
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
    function updateCar(Request $request, $id)
    {
        try {
            //-- validation
            $validation =  $this->validateRequests->validateUpdateCar($id);
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();

            $this->carRepository->updateCar($request);

            DB::commit();
            return $this->handleReturn(true, null, "Updated successfully");

        } catch (Exception $ex) {
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

//

// == DELETE

    // ----- delete car
    /**
     * @OA\Delete(
     * path="/car/delete-car/{id}",
     * operationId="deleteCar",
     * tags={"Cars"},
     * summary="Delete car",
     *      @OA\Parameter(
     *           name="id",
     *           description="car_id",
     *           required=true,
     *           in="path",
     *           @OA\Schema(
     *                 type="integer"
     *              )
     *      ),
     * security={{"bearerToken":{}}},
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
    function deleteCar($id)
    {
        try {
            //-- validation
            $validation =  $this->validateRequests->validateCarId($id);
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();

            $this->carRepository->deleteCar($id);

            DB::commit();
            return $this->handleReturn(true, null, "Deleted successfully");

        } catch (Exception $ex) {
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }
//

}
