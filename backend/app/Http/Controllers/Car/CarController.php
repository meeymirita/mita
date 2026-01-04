<?php

namespace App\Http\Controllers\Car;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    public function getAvailableCars(Request $request)
    {
        $user = Auth::user();

        $availableCars = Car::whereNotIn('id', function($query) use ($request) {
            $query->select('car_id')
                ->from('trips')
                ->where('start_time', '<', $request->end_time)
                ->where('end_time', '>', $request->start_time);
            })
            ->when($request->model, function($query, $model) {
                return $query->where('model', 'like', "%{$model}%");
            })
            ->when($request->category, function($query, $category) {
                return $query->where('comfort_category', $category);
            })
            ->get();

        return response()->json($availableCars);
    }
}
