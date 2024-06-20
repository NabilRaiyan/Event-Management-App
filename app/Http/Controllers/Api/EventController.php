<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;

use \App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $query = Event::query();
        $relations = ['user', 'attendees', 'attendees.user'];

        foreach($relations as $relation){
            $query->when(
                $this->shouldIncludeRelation($relation),
                fn($q)=>$q->with($relation)
            );
        }
        // adding api resource
        return EventResource::collection($query->latest()->paginate());
    }


    // helper methos to get response using queries
    protected function shouldIncludeRelation(string $relation): bool{
        $include = request()->query('include');

        if(!$include){
            return false;
        }
        $relations = array_map('trim',explode(',', $include));

        return in_array($relation, $relations);

    }

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

        return new EventResource($event);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($event);
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

        return new EventResource($event);
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
