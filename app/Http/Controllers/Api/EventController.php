<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Traits\CanLoadRelationships;
use \App\Models\Event;

class EventController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ['user', 'attendees', 'attendees.user'];

    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $query = $this->loadRelationships(Event::query());

     
        // adding api resource
        return EventResource::collection($query->latest()->paginate());
    }


    // helper methos to get response using queries
   

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        // creating new event
        $event = Event::create([
            // validating data
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start' => 'required|date',
                'end' => 'required|date|after:start',
            ]),

            'user_id' => 1
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        // updating an event
        $event -> update(
                $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'description' => 'nullable|string',
                    'start' => 'required|date',
                    'end' => 'required|date|after:start',
                ])
        
        );

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json([
            'message' => "Event deleted successfully"
        ]);
    }
}
