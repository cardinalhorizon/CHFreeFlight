<?php

namespace Modules\CHFreeFlight\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\FlightType;
use App\Models\Enums\PirepFieldSource;
use App\Models\Flight;
use App\Repositories\AirlineRepository;
use App\Services\BidService;
use App\Services\FlightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\CHFreeFlight\Providers\CHFreeFlightProvider;

/**
 * Class $CLASS$
 * @package
 */
class IndexController extends Controller
{
    public function __construct(
        public AirlineRepository $airlineRepository,
        public FlightService $flightService,
        public BidService $bidService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        return view('chfreeflight::create_flight', [
            'airline_list' => $this->airlineRepository->selectBoxList(true)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $fields = $request->all();

        // If Free Flight Details Missing, Override
        if ($fields['flight_number'] == "") {
            $fields['flight_number'] = random_int(9001, 9999);
        }

        if ($fields['route_code'] == "") {
            $fields['route_code'] = "FFT";
        }

        if ($fields['flight_type'] == FlightType::SCHED_PAX) {
            $fields['flight_type'] = FlightType::ADDTL_PAX;
        }
        // Add the owner of the flight
        $fields['owner_type'] = CHFreeFlightProvider::class;
        $fields['user_id'] = Auth::user()->id;
        $fields['visible'] = 0;
        $fields['active'] = 1;
        $fields['minutes'] = 0;
        $fields['hours'] = 0;
        // Create the flight
        try {
            $flight = $this->flightService->createFlight($fields);
        } catch (\Exception $exception) {
            //dd($exception);
            Session::flash('error', $exception->getMessage());
            return to_route('chfreeflight.create');
        }
        $flight->visible = 0;
        $flight->save();
        // Add the Bid to the User Account
        $this->bidService->addBid($flight, Auth::user());

        return to_route('frontend.flights.bids');
    }
}
