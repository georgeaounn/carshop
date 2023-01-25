<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\CarRepository;
use App\Validations\TransactionValidation;
use App\Repositories\TransactionRepository;

class TransactionController extends Controller
{
// == DECLARATION
    private $validateRequests, $transactionRepository, $carRepository;
    public function __construct(TransactionValidation $validateRequests, TransactionRepository $transactionRepository, CarRepository $carRepository)
    {
        $this->middleware('auth:api', ['except' => []]);


        $this->middleware('permission:get-all-transactions', ['only' => ['getAllTransactions']]);
        $this->middleware('permission:export-transactions', ['only' => ['exportTransactions']]);
        $this->middleware('permission:create-transaction', ['only' => ['createTransaction']]);


        $this->validateRequests = $validateRequests;
        $this->transactionRepository = $transactionRepository;
        $this->carRepository = $carRepository;
    }
//


// == GET

    // ----- get transaction by id
    /**
     * @OA\Get(
     * path="/admin/get-transaction-by-id/{id}",
     * operationId="getTransactionById",
     * tags={"Transactions"},
     * summary="Get a specific transaction's information",
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
    function getTransactionById($id)
    {
        try {
            return $this->transactionRepository->exportTransactions();
            //-- validate
            $validation = $this->validateRequests->validateTransactionId($id);
            if ($validation->fails())
            return $this->handleReturn(false, null, $validation->errors()->first());


            $transaction = $this->transactionRepository->getTransactionById($id);
            return $transaction ? $this->handleReturn(true, $transaction, null) :  $this->handleReturn(false, null, "invalid id");
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- get all transactions
    /**
     * @OA\Get(
     * path="/admin/get-all-transactions",
     * operationId="getAllTransactions",
     * tags={"Transactions"},
     * summary="Get all transactions",
     * security={{"bearerToken":{}}},
     *      @OA\Parameter(
     *         name="minimum_date",
     *         in="query",
     *         description="minimum_date",
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="user_ids[]",
     *         in="query",
     *              @OA\Schema(
     *               type="array",
     *               @OA\items(type="integer"),
     *          ),
     *         description="user ids",
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
    function getAllTransactions(Request $request)
    {
        try {
            //-- validate
            $validation = $this->validateRequests->validateGetAllTransactions();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            $transactions = $this->transactionRepository->getAllTransactions($request);
            return $this->handleReturn(true, $transactions, null);
        } catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }

    // ----- export transactions
    /**
     * @OA\Get(
     * path="/admin/export-transactions",
     * operationId="exportTransactions",
     * tags={"Transactions"},
     * summary="Export transactions ",
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
    function exportTransactions()
    {
        try{
            return $this->transactionRepository->exportTransactions();
        }
        catch (Exception $ex) {
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }
//

// == EDIT

    // ----- create transaction
    /**
     * @OA\Post(
     * path="/user/create-transaction",
     * operationId="createTransaction",
     * tags={"Transactions"},
     * summary="Create transaction",
     * security={{"bearerToken":{}}},
     *     @OA\RequestBody(
     *           required=true,
     *           description="Body request needed to create a transaction",
     *            @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="cars",type="array",
     *              @OA\Items(@OA\Property(property="car_id",type="integer"),@OA\Property(property="quantity",type="integer"))),
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
    function createTransaction(Request $request)
    {
        try{
            //-- validation
            $validation =  $this->validateRequests->validateCreateTransaction();
            if ($validation->fails())
                return $this->handleReturn(false, null, $validation->errors()->first());

            DB::beginTransaction();
            $amount = 0;
            $cars_array = [];
            foreach($request->cars as $key => $car_details)
            {
                $car = $this->carRepository->decrementCarQuantity($car_details);
                if($car == false)
                        return $this->handleReturn(false, null, "Quantity error");
                $cars_array[$key]['car'] = $car;
                $cars_array[$key]['car_id'] = $car_details['car_id'];
                $cars_array[$key]['quantity'] = $car_details['quantity'];
                $amount = $amount + ($car_details['quantity'] * $car->price);
            }
            $request->cars_array = $cars_array;
            $request->amount = $amount;
            $transaction = $this->transactionRepository->createTransaction($request);
            DB::commit();
            return $this->handleReturn(true, $transaction, "Created successfully");
        }
        catch(Exception $ex){
            DB::rollBack();
            return $this->handleReturn(false, null, $ex->getMessage());
        }
    }
//
}
