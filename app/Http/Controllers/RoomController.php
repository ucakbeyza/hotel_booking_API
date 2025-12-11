<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Resources\RoomResource;
use App\Helpers\ResponseBuilder;    
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Requests\DeleteRoomRequest;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return ResponseBuilder::success(RoomResource::collection($rooms));
    }
    public function show($id)
    {
        $room = Room::findOrFail($id);
        return ResponseBuilder::success(new RoomResource($room));
    }
    public function create(CreateRoomRequest $request)
    {
        $room = Room::create([
            'hotel_id'=>$request->hotel_id,
            'room_number'=>$request->room_number,
            'type'=>$request->type,
            'price'=>$request->price,
            'status'=>$request->status,
        ]);
        return ResponseBuilder::success(new RoomResource($room));
    }
    public function update(UpdateRoomRequest $request)
    {
        $room = Room::where('id', $request->id)
            ->firstOrFail();
        $room->update([
            'hotel_id' => $request->hotel_id,
            'room_number' => $request->room_number,
            'type' => $request->type,
            'price' => $request->price,
            'status' => $request->status,
        ]);
        return ResponseBuilder::success(new RoomResource($room));
    }
    public function delete(DeleteRoomRequest $request)
    {
        $room = Room::findOrFail($request->id);
        if($room->bookings()->exists()) {
            return ResponseBuilder::error(
                [],
                'Cannot delete room with existing bookings.', 400
            );
        }
        $room->delete();
        return ResponseBuilder::success(null, 'Room deleted successfully.');
    }
}
