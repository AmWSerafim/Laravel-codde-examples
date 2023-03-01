@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
    asset('assets/libs/multiselect/multi-select.css'),
     ] ])
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">New Geo</h4>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('geos.update', $geo->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_name">Name*</label>
                        <div class="col-md-10">
                            <input name="name" type="text" id="geo_name" parsley-trigger="change" required="" class="form-control" value="{{$geo->name}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_slug">Slug*</label>
                        <div class="col-md-10">
                            <input name="slug" type="text" id="geo_slug" parsley-trigger="change" required="" class="form-control" value="{{$geo->slug}}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Accounts*</label>
                        <div class="col-md-10">
                            <select multiple="multiple" class="multi-select" id="accounts" name="accounts[]" data-plugin="multiselect" data-parsley-required data-parsley-mincheck="1">
                                @foreach ($accounts as  $account)
                                    <option value="{{$account->id}}" @if(in_array($account->id, $selected_accounts)) selected @endif >{{$account->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-md-2 col-form-label">Countries*</label>
                            <div class="col-md-10">
                                <select multiple="multiple" class="multi-select" id="countries" name="countries[]" data-plugin="multiselect" data-parsley-required data-parsley-mincheck="1">
                                    @foreach ($countries as  $country)
                                        <option value="{{$country->name}}" @if(in_array($country->name, $selectedCountries)) selected @endif >{{$country->value}}</option>
                                    @endforeach

                                </select>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-2 col-form-label">Outbrain Countries*</label>
                            <div class="col-md-10 ui-widget">
                                <div class="row" id="saved_results">
                                    @if(!empty($selected_outbrain_countries))
                                        @foreach ($selected_outbrain_countries as $key => $value)
                                        <p id="{{ $outbrain_country_ids[$key] }}">
                                            <input type="hidden" name="country_ids[]" value="{{ $outbrain_country_ids[$key] }}">
                                            <input type="hidden" name="country_codes[]" value="{{ $value }}">
                                            <b>{{ $countries_array_for_outbrain[$value] }}</b>
                                            <a href="#" class="btn btn-danger waves-effect waves-light mr-1 removeCountry" data-id="{{ $outbrain_country_ids[$key] }}"> - </a>
                                        </p>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="text" name="outbrain_input" id="outbrain_input_search">
                                <input type="button" value="Add" id="add_outbrain_geo" disabled="disabled">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right mb-0">
                        <button id="create" class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                            Save
                        </button>

                    </div>
                </form>
            </div>

        </div>
    </div>
    <script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $('[data-plugin="multiselect"]').multiSelect($(this).data());

        var selected_outbrain_geo_id = "";
        var selected_outbrain_geo_code = "";

        function saveSelection(){

            let data_append = "";
            let contry = $("#outbrain_input_search").val();

            data_append = '<p id="'+selected_outbrain_geo_id+'">';
            data_append += '<input type="hidden" name="country_ids[]" value="'+selected_outbrain_geo_id+'">';
            data_append += '<input type="hidden" name="country_codes[]" value="'+selected_outbrain_geo_code+'">';
            data_append += '<b>'+contry+'</b>';
            data_append += '<a href="#" class="btn btn-danger waves-effect waves-light mr-1 removeCountry" data-id="'+selected_outbrain_geo_id+'"> - </a>';
            data_append += '</p>';

            jQuery("#saved_results").append(data_append);
            $('#add_outbrain_geo').attr('disabled', 'disabled');
        }

        $("body").on("click", ".removeCountry", function (e) {
            let id = "#"+$(this).data('id');
            $(id).remove();
            //console.log(id);
        });

        $('#add_outbrain_geo').click(function(e){
            saveSelection();
            $("#outbrain_input_search").val("");
        });

        $("#outbrain_input_search").autocomplete({
            source: function (request, response) {
                //console.log(request);
                $.ajax({
                    url: "{{ route('geos.outbrainSearchAjax') }}",
                    //dataType: "jsonp",
                    method: "GET",
                    data: {
                        search_term: request.term
                    },
                    complete: function (data) {
                        //console.log(data);
                        //console.log($.parseJSON( data.responseText ))
                        let responce_result = $.parseJSON( data.responseText );
                        response(responce_result);
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(ui.item.name);
                console.log(ui.item.value);
                $("#outbrain_input_search").val(ui.item.name);
                let selected_value = ui.item.value.split("|");

                selected_outbrain_geo_id = selected_value[0];
                selected_outbrain_geo_code = selected_value[1];

                $('#add_outbrain_geo').removeAttr('disabled');
                return false;
            },
        });

    </script>


@endsection
