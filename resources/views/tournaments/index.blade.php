@extends('layout')

@section('title', 'Tournaments')

@section('content')

        <!-- Alerts -->
        @if( session()->get('success') )
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session()->get('success') }}
            </div><br/>
        @endif

        <!-- If NO tournaments -->
        @if( count($tournaments) == 0 )
            <div class="card text-center">
              <div class="card-body">
                <h1 class="card-title"> No tournaments to display </h1>
                <p class="card-text"> Maybe you would like to create one? :) </p>
                <a href="../tournaments/create" class="btn btn-primary" role="button">Create Tournament</a>
              </div>
            </div>

        <!-- If tournaments -->
        @else

        <!-- Filters -->
        <div class="row text-center" style="margin-bottom: 20px">

            <!-- Categories filter -->
            <div class="col-sm">
                <select class="form-control" id="categories" style="margin: 5px 0">
                    <option value=""> All categories </option>
                    @foreach( $categories as $category )
                        <option value="{{ $category['category'] }}"> {{ $category['category'] }} </option>
                    @endforeach
                </select>
            </div>

            <!-- Dates filter -->
            <div class="col-sm-5">
                <input class="form-control" type="text" name="datefilter"
                      value="" placeholder="All dates" style="margin: 5px 0"/>
            </div>

            <!-- Country filter -->
            <div class="col-sm">
                <select class="form-control" id="countries" style="margin: 5px 0">
                    <option value=""> All countries </option>
                    @foreach( $countries as $country )
                        <option value="{{ $country['country'] }}"> {{ $country['country'] }} </option>
                    @endforeach
                </select>
            </div>

            <!-- Cities filter -->
            <div class="col-sm">
                <select class="form-control" id="cities" style="margin: 5px 0">
                    <option value=""> All cities </option>
                    @foreach( $cities as $city )
                        <option value="{{ $city['city'] }}"> {{ $city['city'] }} </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Tournaments table -->
        <table class="table dt-responsive nowrap table-hover" id="tourn" style="width: 100%">

            <!-- Table header -->
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Begin date</th>
                    <th scope="col">End date</th>
                    <th scope="col">Country</th>
                    <th scope="col">City</th>
                </tr>
            </thead>

            <!-- Table content -->
            <tbody>
                @foreach( $tournaments as $tournament )
                    <tr>
                        <!-- Name -->
                        <td>
                            <a href="{{ $tournament->website }}"
                               target="_blank" style="color: black">
                               {{ $tournament->name }}
                            </a>
                            <!-- Badge Started  -->
                            @if( \Carbon\Carbon::parse($tournament->begin)->lt(now()) )
                              <span class="badge badge-dark"> Started </span>
                            @endif
                        </td>
                        <!-- Category -->
                        <td> {{ $tournament->category }} </td>
                        <!-- Begin date -->
                        <td> {{ date('d-M-Y', strtotime($tournament->begin)) }} </td>
                        <!-- End date -->
                        <td> {{ date('d-M-Y', strtotime($tournament->end)) }} </td>
                        <!-- Country -->
                        <td> {{ $tournament->country }} </td>
                        <!-- City -->
                        <td> {{ $tournament->city }} </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        @endif

@endsection


<!-- JQuery 3.3.1 -->
<script src="{{ asset('js/jquery-3.3.1.js') }}"></script>

<!-- JS -->
<script>

    $(document).ready(function() {
        // datetable
         var table = $('#tourn').DataTable({
            "order": [ [ 2, "asc" ], [ 3, "asc" ] ],
            "scrollX": true,
            "pagingType": "full_numbers",
        });

        // categories filter
        $('#categories').on('change', function () {
            table.columns(1).search( this.value ).draw();
        } );

        // dates range filter
        $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('input[name="datefilter"]').data('daterangepicker').startDate._d;
            var max = $('input[name="datefilter"]').data('daterangepicker').endDate._d;
            var startDate = new Date(data[2]);
            var endDate = new Date(data[3]);

            if( min == null && max == null ) { return true; }
            if( $('input[name="datefilter"]').val() == '' ) { return true; }
            if( min == null && endDate <= max ) { return true; }
            if( max == null && startDate >= min ) { return true; }
            if( endDate <= max && startDate >= min ) { return true; }
            return false;
        }
        );

        var filter_minDay = new Date();
        $('input[name="datefilter"]').daterangepicker({
            autoUpdateInput: false,
            minDate: filter_minDay,
            opens: "center",
            locale: {
                cancelLabel: 'Clear'
            }
        });

        // apply button dates range filter
        $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val( 'From ' + picker.startDate.format('DD-MMM-Y') + ' to ' + picker.endDate.format('DD-MMM-Y') );
             table.draw();
        });

        // cancel button dates range filter
        $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val( '' );
            table.draw();
        });

        // countries filter
        $('#countries').on('change', function () {
            table.columns(4).search( this.value ).draw();
        } );

        // cities filter
        $('#cities').on('change', function () {
            table.columns(5).search( this.value ).draw();
        } );

    });

</script>
