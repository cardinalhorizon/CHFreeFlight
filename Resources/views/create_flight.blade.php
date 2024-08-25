@extends('chfreeflight::layouts.frontend')

@section('title', 'CHFreeFlight')

@section('content')
  @if(Session::has('error'))
    <div class="alert alert-danger">
      {{ Session::get('error') }}
    </div>
  @endif
  <div class="card">
    <div class="card-body">
      <div class="alert alert-info">If a flight fumber or code are not provided, a flight number in the 9000 range, and a code of "FFT" will automatically be generated.</div>
      <form action="{{route('chfreeflight.store')}}" method="POST">
        @csrf
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
                      {{ Form::text('flight_number', request()->get('flight_number'), [
                          'placeholder' => "Flight Number (optional)",
                          'class' => 'form-control',
                      ]) }}
                      &nbsp;
                      {{ Form::text('route_code', request()->get('route_code'), [
                          'placeholder' => __('pireps.codeoptional'),
                          'class' => 'form-control',
                      ]) }}
                      &nbsp;
                      {{ Form::text('route_leg', null, [
                          'placeholder' => __('pireps.legoptional'),
                          'class' => 'form-control',
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
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      $("select.airport_search").select2({
        ajax: {
          url: '{{ Config::get("app.url") }}/api/airports/search',
          data: function (params) {
            const hubs_only = $(this).hasClass('hubs_only') ? 1 : 0;
            return {
              search: params.term,
              hubs: hubs_only,
              page: params.page || 1,
              orderBy: 'id',
              sortedBy: 'asc'
            }
          },
          processResults: function (data, params) {
            if (!data.data) { return [] }
            const results = data.data.map(apt => {
              return {
                id: apt.id,
                text: apt.description,
              }
            })

            const pagination = {
              more: data.meta.next_page !== null,
            }

            return {
              results,
              pagination,
            };
          },
          cache: true,
          dataType: 'json',
          delay: 250,
          minimumInputLength: 2,
        },
        width: 'resolve',
        placeholder: 'Type to search',
      });
    });
  </script>
@endsection
