<?php

namespace App\Repositories;

use App\Models\Car;
use App\Models\Type;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CarRepository
{

// == GET

    // ----- get car by id
    function getCarById($car_id)
    {
        return Car::where('id', $car_id)->with(['brand', 'type'])->first();
    }

    // ----- get all cars
    function getAllCars($request)
    {
        $cars = Car::query();

        if ($request->name) {
            $cars = $cars->where('name', 'ILIKE', '%' . $request->name . '%');
        }

        if(isset($request->sort))
        {
            switch($request->sort){
                case 1 : $cars = $cars->orderBy('id', "DESC");
                        break;
                case 2 : $cars = $cars->orderBy('id', "ASC");
                        break;
                case 3 : $cars = $cars->orderBy('name',"ASC");
                        break;
                case 4 : $cars = $cars->orderBy('name',"DESC");
                        break;
            }
        }

        if(isset($request->brand_ids) && count($request->brand_ids))
        {
            $cars = $cars->whereIn('brand_id', $request->brand_ids);
        }

        if(isset($request->type_ids) && count($request->type_ids))
        {
            $cars = $cars->whereIn('type_id', $request->type_ids);
        }

        if(isset($request->minimum_price))
        {
            $cars = $cars->where('price', '>=', $request->minimum_price);
        }

        if(isset($request->max_price))
        {
            $cars = $cars->where('price', '<=', $request->max_price);
        }

        return isset($request->per_page) ? $cars->paginate($request->per_page) : $cars->get();
    }

    // ----- get all brands
    function getAllBrands($request)
    {
        $brands = Brand::query();

        if ($request->name) {
            $brands = $brands->where('name', 'ILIKE', '%' . $request->name . '%');
        }
        return isset($request->per_page) ? $brands->paginate($request->per_page) : $brands->get();
    }

    // ----- get all types
    function getAllTypes($request)
    {
        $types = Type::query();

        if ($request->name) {
            $types = $types->where('name', 'ILIKE', '%' . $request->name . '%');
        }
        return isset($request->per_page) ? $types->paginate($request->per_page) : $types->get();
    }

    // ----- get cars by ids
    function getCarsByIds($car_ids)
    {
        return Car::whereIn('id', $car_ids)->get();
    }

//

// == EDIT

    // ----- create car
    function createCar($request)
    {
        $car = Car::where([
            "name" => $request->name,
            "automatic_transmission" => $request->automatic_transmission,
            "color" => $request->color,
            "fuel_consumption" => $request->fuel_consumption,
            "mileage" => $request->mileage,
            "price" => $request->price,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id
        ])->first();

        if(isset($car))
        {
            $car->quantity = $car->quantity + $request->quantity;
            $car->update();
            return $car;
        }

        return Car::create(array_merge([
            "name" => $request->name,
            "automatic_transmission" => $request->automatic_transmission,
            "color" => $request->color,
            "fuel_consumption" => $request->fuel_consumption,
            "mileage" => $request->mileage,
            "price" => $request->price,
            "quantity" => $request->quantity,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id
        ]));
    }

    // ----- update car
    function updateCar($request)
    {
        return Car::where('id', $request->id)->update([
            "name" => $request->name,
            "automatic_transmission" => $request->automatic_transmission,
            "color" => $request->color,
            "fuel_consumption" => $request->fuel_consumption,
            "mileage" => $request->mileage,
            "price" => $request->price,
            "quantity" => $request->quantity,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id
        ]);
    }

    // ----- decrement car quantity
    function decrementCarQuantity($car_details)
    {
        $car = Car::where('id', $car_details['car_id'])->first();
        if($car->quantity < $car_details['quantity'])
                return false;

        $car->quantity = $car->quantity - $car_details['quantity'];
        $car->update();
        return $car;
    }
//

// == DELETE

    // ---- delete car
    function deleteCar($id)
    {
        return Car::where('id', $id)->delete();
    }

//

}
