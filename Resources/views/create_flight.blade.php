@extends('chfreeflight::layouts.frontend')

@section('title', 'CHFreeFlight')

@section('content')
  @if(Session::has('error'))
  <div class="alert alert-danger">
    {{ Session::get('error') }}
  </div>
  @endif
  <form action="{{route('chfreeflight.store')}}" method="POST">
    <div class="row">
      <div class="col-12">
        <div class="form-container">
          <h6><i class="fas fa-info-circle"></i>
            &nbsp;@lang('pireps.flightinformations')
          </h6>
          <div class="form-container-body">
            <div class="row">
              <div class="col-sm-4">
                {{ Form::label('airline_id', __('common.airline')) }}
                @if(!empty($pirep) && $pirep->read_only)
                  <p>{{ $pirep->airline->name }}</p>
                  {{ Form::hidden('airline_id') }}
                @else
                  <div class="input-group input-group form-group">
                    {{ Form::select('airline_id', $airline_list, null, [
                        'class' => 'custom-select select2',
                        'style' => 'width: 100%',
                        'readonly' => (!empty($pirep) && $pirep->read_only),
                    ]) }}
                  </div>
                  <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                @endif
              </div>
              <div class="col-sm-4">
                {{ Form::label('flight_number', __('pireps.flightident')) }}
                <div class="input-group input-group-sm mb3">
                  {{ Form::text('flight_number', null, [
                      'placeholder' => __('flights.flightnumber'),
                      'class' => 'form-control',
                      'readonly' => (!empty($pirep) && $pirep->read_only),
                  ]) }}
                  &nbsp;
                  {{ Form::text('route_code', null, [
                      'placeholder' => __('pireps.codeoptional'),
                      'class' => 'form-control',
                      'readonly' => (!empty($pirep) && $pirep->read_only),
                  ]) }}
                  &nbsp;
                  {{ Form::text('route_leg', null, [
                      'placeholder' => __('pireps.legoptional'),
                      'class' => 'form-control',
                      'readonly' => (!empty($pirep) && $pirep->read_only),
                  ]) }}
                </div>
                <p class="text-danger">{{ $errors->first('flight_number') }}</p>
                <p class="text-danger">{{ $errors->first('route_code') }}</p>
                <p class="text-danger">{{ $errors->first('route_leg') }}</p>
              </div>
              <div class="col-lg-4">
                {{ Form::label('flight_type', __('flights.flighttype')) }}
                <div class="form-group">
                  {{ Form::select('flight_type',
                      \App\Models\Enums\FlightType::select(), null, [
                          'class' => 'custom-select select2',
                          'style' => 'width: 100%'
                      ])
                  }}
                </div>
                <p class="text-danger">{{ $errors->first('flight_type') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="form-container">
      <h6><i class="fas fa-globe"></i>
        &nbsp;@lang('pireps.deparrinformations')
      </h6>
      <div class="form-container-body">
        <div class="row">
          <div class="col-6">
            {{ Form::label('dpt_airport_id', __('airports.departure')) }}

            <div class="form-group">
              {{ Form::select('dpt_airport_id', [], null, [
                      'class' => 'custom-select airport_search',
                      'style' => 'width: 100%',
                      'readonly' => (!empty($pirep) && $pirep->read_only),
              ]) }}
            </div>
            <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
          </div>

          <div class="col-6">
            {{ Form::label('arr_airport_id', __('airports.arrival')) }}

            <div class="input-group input-group-sm form-group">
              {{ Form::select('arr_airport_id', [], null, [
                      'class' => 'custom-select airport_search',
                      'style' => 'width: 100%',
                      'readonly' => (!empty($pirep) && $pirep->read_only),
              ]) }}
            </div>
            <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
          </div>
        </div>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Create Flight</button>
  </form>
@endsection

@section('scripts')
  @include('scripts.airport_search')
@endsection
