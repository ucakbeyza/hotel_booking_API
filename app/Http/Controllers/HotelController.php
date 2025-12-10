<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Http\Resources\HotelResource;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateHotelRequest;
use App\Http\Requests\DeleteHotelRequest;
use App\Http\Requests\UpdateHotelRequest;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::all();
        return ResponseBuilder::success(HotelResource::collection($hotels));
    }
    public function show($id)
    {
        $hotel = Hotel::findOrFail($id);
        return ResponseBuilder::success(new HotelResource($hotel));
    }
    public function create(CreateHotelRequest $request)
    {
        $hotel = Hotel::create([
            'name'=>$request->name,
            'location'=>$request->location,
            'rating'=>$request->rating,
        ]);
        return ResponseBuilder::success(new HotelResource($hotel));
    }
    public function update(UpdateHotelRequest $request)
    {
        $hotel = Hotel::where('id', $request->id)->firstOrFail();

        $hotel->update([
            'name' => $request->name,
            'location' => $request->location,
            'rating' => $request->rating,
        ]);
        return ResponseBuilder::success(new HotelResource($hotel));
    }
    public function delete(DeleteHotelRequest $request)
    {
        $hotel = Hotel::findOrFail($request->id);
        if ($hotel->rooms()->count() > 0) {
            return ResponseBuilder::error(
                [],
                'Cannot delete hotel with existing rooms.', 400
            );
        }
        $hotel->delete();
        return ResponseBuilder::success(null, 'Hotel deleted successfully.');
    }
}
