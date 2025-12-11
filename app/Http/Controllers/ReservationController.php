<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateReservationRequest;
use App\Models\Reservation;
use App\Helpers\ResponseBuilder;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ReservationResource;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Requests\DeleteReservationRequest;


class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('room')->get();
        return ResponseBuilder::success(ReservationResource::collection($reservations));
    }

    public function create(CreateReservationRequest $request)
    {
        if(Reservation::hasConflict(
            $request->room_id, 
            $request->start_date, 
            $request->end_date,
            null
        )){
            return ResponseBuilder::error(
                [],
                'The selected room is not available for the chosen dates.',
                409
            );
        }
        $room = Room::findOrFail($request->room_id);
        if($room->status !== 'available'){
            return ResponseBuilder::error(
                [],
                'Room is not available.',
                400
            );
        }
        DB::beginTransaction();
        try{
            $reservation = Reservation::create([
                'room_id' => $request->room_id,
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'pending', 
            ]);

            $room->update(['status' => 'booked']);

            DB::commit();

            return ResponseBuilder::success(
                new ReservationResource($reservation->load('room')),
                'Reservation created successfully.',
                201 
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseBuilder::error(
                [],
                'Failed to create reservation: ' . $e->getMessage(),
                500
            );
        }
    }
    public function show($id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);
        return ResponseBuilder::success(
            new ReservationResource($reservation)
        );
    }
    public function update(UpdateReservationRequest $request, $id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        if ($reservation->status === 'canceled') {
            return ResponseBuilder::error(
                [],
                'Cannot update a canceled reservation.',
                400
            );
        }

        DB::beginTransaction();
        try {
            $oldRoomId = $reservation->room_id;
            $oldStatus = $reservation->status;
            if ($request->has('start_date') || $request->has('end_date') || $request->has('room_id')) {
                $roomId = $request->room_id ?? $reservation->room_id;
                $startDate = $request->start_date ?? $reservation->start_date;
                $endDate = $request->end_date ?? $reservation->end_date;
                if (Reservation::hasConflict($roomId, $startDate, $endDate, $reservation->id)) {
                    return ResponseBuilder::error(
                        [],
                        'Room is not available for the selected dates.',
                        409
                    );
                }
            }

            $reservation->update($request->only([
                'room_id',
                'guest_name',
                'guest_email',
                'start_date',
                'end_date',
                'status'
            ]));

            if ($request->has('status')) {
                $newStatus = $request->status;

                if ($newStatus === 'canceled') {
                    $reservation->room->update(['status' => 'available']);
                }
                elseif ($oldStatus === 'canceled' && in_array($newStatus, ['pending', 'confirmed'])) {
                    $reservation->room->update(['status' => 'booked']);
                }
            }
            if ($request->has('room_id') && $oldRoomId != $request->room_id) {
                Room::find($oldRoomId)->update(['status' => 'available']);
                $reservation->room->update(['status' => 'booked']);
            }

            DB::commit();

            return ResponseBuilder::success(
                new ReservationResource($reservation->fresh('room')),
                'Reservation updated successfully.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseBuilder::error(
                [],
                'Failed to update reservation: ' . $e->getMessage(),
                500
            );
        }
    }
    public function delete($id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        DB::beginTransaction();
        try {
            $reservation->room->update(['status' => 'available']);

            $reservation->delete();

            DB::commit();

            return ResponseBuilder::success(
                null,
                'Reservation deleted successfully.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseBuilder::error(
                [],
                'Failed to delete reservation: ' . $e->getMessage(),
                500
            );
        }
    }
}