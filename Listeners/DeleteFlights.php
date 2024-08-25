<?php

namespace Modules\CHFreeFlight\Listeners;

use App\Contracts\Listener;
use App\Models\Bid;
use App\Models\Enums\FlightType;
use App\Models\Enums\PirepState;
use App\Models\Flight;
use App\Models\Pirep;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\CHFreeFlight\Providers\CHFreeFlightProvider;

/**
 * Class DeleteFlights
 * @package Modules\CHFreeFlight\Listeners
 */
class DeleteFlights extends Listener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Find all the flights
        $flights = Flight::where('owner_type', CHFreeFlightProvider::class)->get();

        Log::debug("found ".$flights->count()." CHFreeFlight flights");
        // We're going to only delete flights that don't have a bid, or a pirep that's completed.

        foreach ($flights as $flight) {
            $flight->visible = false;
            $flight->save();
            Log::debug("Processing ".$flight->id);
            // if Pirep is in progress, then don't do anything.
            $pirep = Pirep::where(['flight_id' => $flight->id, 'user_id' => $flight->user_id])->first();

            if ($pirep) {
                if ($pirep->state == PirepState::IN_PROGRESS) {
                    continue;
                } elseif ($pirep->state == PirepState::PENDING ||
                    $pirep->state == PirepState::ACCEPTED ||
                    $pirep->state == PirepState::REJECTED)
                {
                    Log::debug("Deleted ".$flight->id);
                    $flight->delete();
                    continue;
                }
            }

            // Check if there's a bid.
            $bids = Bid::where('flight_id', $flight->id)->count();
            if ($bids == 0) {
                Log::debug("Deleted ".$flight->id);
                $flight->delete();

                continue;
            }

        }
    }
}
