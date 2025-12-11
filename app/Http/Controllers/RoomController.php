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
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereDoesntHave('reservations', function ($q) use ($startDate, $endDate) {
                $q->where('status', '!=', 'canceled')
                  ->where(function ($subQ) use ($startDate, $endDate) {
                        $subQ->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($dateQ) use ($startDate, $endDate) {
                               $dateQ->where('start_date', '<=', $startDate)
                                     ->where('end_date', '>=', $endDate);
                            });
                    });
            });
            $query->where('status', 'available');
        }

        $rooms = $query->get();
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
        //rezervasion booked rooms cannot be deleted
        if($room->status === 'booked'){
            return ResponseBuilder::error(
                [],
                'Cannot delete a booked room.', 400
            );
        }
        $room->delete();
        return ResponseBuilder::success(null, 'Room deleted successfully.');
    }
}
