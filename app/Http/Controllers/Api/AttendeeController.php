<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use Illuminate\Http\Request;
use \App\Models\Event;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        // return attendees
        $attendees = $event->attendees()->latest();

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => 1
        ]);

        return new AttendeeResource($attendee);
    }

    /**
     * Display the specified attendee.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($attendee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the attendee resource from storage.
     */
    public function destroy(string $event, Attendee $attendee)
    {
        $attendee->delete();
        return response()->json([
            "message" => "attendee deleted successfully",
        ]);
    }
}
