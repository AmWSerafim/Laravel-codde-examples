@extends('layouts.app')

@section('content')
    <style>
        .custom_error_border{
            border: 2px solid #FF0000 !important;
        }
    </style>


    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Make import with new mapping</h2>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('mapping.create') }}" id="upload-files-form" enctype="multipart/form-data">
                @csrf
                <div class="row form-group">
                    <div class="col-xl-6">
                        <label for="payment-file">Practice Management - Billing</label>
                        <input type="file" name="payment-file" class="dropify" id="payment-file">
                    </div>
                    <div class="col-xl-6">
                        <label for="transfer-file">Bank EFT</label>
                        <input type="file" name="transfer-file" class="dropify" id="transfer-file">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xl-6 form-group">
                        <label for="payment-file-headings" class="control-label">Headings row number</label>
                        <input type="text" name="payment-file-headings" class="form-control touch-spin-custom" id="payment-file-headings" value="1" min="1">
                    </div>
                    <div class="col-xl-6 form-group">
                        <label for="transfer-file-headings" class="control-label">Headings row number</label>
                        <input type="text" name="transfer-file-headings" class="form-control touch-spin-custom" id="transfer-file-headings" value="1" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-rounded btn-primary">Upload</button>
                </div>

            </form>

            <div id="mapping-form" style="display: none">
                <div class="row" id="form_errors">

                </div>
                <form method="POST" action="{{ route('mapping.preview') }}" id="map-and-report-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment-file-link" id="payment-file-link" value="">
                    <input type="hidden" name="transfer-file-link" id="transfer-file-link" value="">
                    <input type="hidden" name="payment-file-headings" id="preview-payment-file-headings" value="">
                    <input type="hidden" name="transfer-file-headings" id="preview-transfer-file-headings" value="">
                    <input type="hidden" name="type" id="type" value="new_preview">
                    <div class="form-group row">
                        <label for="mapping-name" class="col-md-2 col-form-label">Mapping name*</label>
                        <div class="col-md-10">
                            <input type="text" name="mapping-name" id="mapping-name" value="" class="custom_validate form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-6">
                            <label for="payment-transaction-code" class="control-label">Transaction code field for Practice Management - Billing file*</label>
                            <select name="payment-transaction-code" id="payment-transaction-code" class="maping-fields custom_validate form-control select2">
                                <option value=""> -Please select- </option>
                            </select>
                        </div>

                        <div class="col-xl-6">
                            <label for="transfer-transaction-code" class="control-label">Transaction code field for Bank EFT file*</label>
                            <select name="transfer-transaction-code" id="transfer-transaction-code" class="maping-fields custom_validate form-control select2">
                                <option value=""> -Please select- </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-6">
                            <label for="payment-amount" class="control-label">Amount field for Practice Management - Billing file*</label>
                            <select name="payment-amount" id="payment-amount" class="maping-fields custom_validate form-control select2">
                                <option value=""> -Please select- </option>
                            </select>
                        </div>

                        <div class="col-xl-6">
                            <label for="transfer-amount" class="control-label">Amount field for Bank EFT file*</label>
                            <select name="transfer-amount" id="transfer-amount" class="maping-fields custom_validate form-control select2">
                                <option value=""> -Please select- </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-12">
                            <p>Select the fields you would like to include in the report</p>
                        </div>
                    </div>
                    <div class="form-group row" style="">
                        <div class="col-xl-6">
                            <div class="row" id="payment-headers-selector">
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="row" id="transfer-headers-selector">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-12">
                            <h4>Separate results</h4>
                            <p>Results will be separated by this section values</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="separate-column" class="col-md-2 col-form-label">Column name:</label>
                        <div class="col-md-10">
                            <select name="separate-column" id="separate-column" class="maping-fields form-control select2">
                                <option value=""> -Please select- </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="separate-values" class="col-md-2 col-form-label">Values for search (coma separated):</label>
                        <div class="col-md-10">
                            <input type="text" name="separate-values" id="separate-values" class="form-control" value="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <button type="submit" class="btn btn-rounded btn-primary">generate Preview</button>
                    </div>

                </form>
            </div>

            <div id="result-block" style="display: none">
                <form method="POST" action="{{ route('mapping.report') }}">
                    @csrf
                    <input type="hidden" name="type" id="report-type" value="" />
                    <input type="hidden" name="mapping-name" id="report-mapping-name" value="" />

                    <input type="hidden" name="separate-column" id="report-separate-column" value="" />
                    <input type="hidden" name="separate-values" id="report-separate-values" value="" />

                    <input type="hidden" name="payment-amount" id="report-payment-amount" value="" />
                    <input type="hidden" name="payment-file-link" id="report-payment-file-link" value="" />
                    <input type="hidden" name="payment-file-headings" id="report-payment-file-headings" value="" />
                    <input type="hidden" name="payment-keep-columns" id="report-payment-keep-columns" value="" />
                    <input type="hidden" name="payment-transaction-code" id="report-payment-transaction-code" value="" />

                    <input type="hidden" name="transfer-amount" id="report-transfer-amount" value="" />
                    <input type="hidden" name="transfer-file-link" id="report-transfer-file-link" value="" />
                    <input type="hidden" name="transfer-file-headings" id="report-transfer-file-headings" value="" />
                    <input type="hidden" name="transfer-keep-columns" id="report-transfer-keep-columns" value="" />
                    <input type="hidden" name="transfer-transaction-code" id="report-transfer-transaction-code" value="" />
                    <div class="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-rounded btn-primary">Save mapping and Generate Report</button>
                        </div>
                    </div>
                    <div class="row table-responsive" id="result_table">

                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-rounded btn-primary">Save mapping and Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        function populate(selector, data) {
            jQuery.each( data, function(key, value) {
                jQuery(selector).append('<option value="'+key+'">'+value+'</option>');
            });
        }

        function populate_headers_selectors(selector, data){

            let data_append = "";

            let field_key = selector.replace("#", "");

            jQuery.each( data, function(key, value) {
                data_append += '<div class="col-4 checkbox checkbox-primary">';
                data_append += '<input type="checkbox" value="'+value+'" name="'+field_key+'_headers[]" id="'+field_key+'_'+key+'"/>';
                data_append += '<label for="'+field_key+'_'+key+'">'+value+'</label>';
                data_append += '</div>';
            });
            jQuery(selector).append(data_append);
        }

        function populate_preview_table(selector, headers, data) {

            let data_append = "";
            let cols = 0;
            let counter = 0;

            data_append = '<div class="row"><table class="table table-hover mb-0"><thead><tr>';
            jQuery.each( headers, function(key, value) {
                data_append += '<th>'+value+'</th>';
            });
            data_append += '</tr></thead><tbody>';
            //data_append = '<tbody>';
            jQuery.each(data, function(row_key, row_value){

                cols = 0;
                counter = 0;

                jQuery.each( row_value, function() {
                    cols++;
                });

                data_append += '<tr>';
                jQuery.each( row_value, function(key, value) {
                    counter++;
                    if(counter == cols-1){
                        data_append += '<td style="background-color: #98E5E8;">'+value+'</td>';
                    } else if(counter == (cols-2)){
                        data_append += '<td style="background-color: #B6DCFD;">'+value+'</td>';
                    } else if(counter == cols){
                        if(value < 0){
                            data_append += '<td style="background-color: #FD7741;">'+value+'</td>';
                        } else if(value > 0){
                            data_append += '<td style="background-color: #FDF557;">'+value+'</td>';
                        } else {
                            data_append += '<td>'+value+'</td>';
                        }
                    } else {
                        data_append += '<td>'+value+'</td>';
                    }
                });
                data_append += "</tr>";
            });
            data_append += "</tbody></table></div>";

            jQuery(selector).append(data_append);
        }

        function populate_space_between_tables(selector){
            let data_append = '<div class="row" style="height: 80px"></div>';
            jQuery(selector).append(data_append);
        }

/*
        function populate_preview_table_header(selector, data) {
            let data_append = "";
            data_append = '<table><thead><tr>';
            jQuery.each( data, function(key, value) {
                data_append += '<th>'+value+'</th>';
            });
            data_append += '</tr></thead>';
            jQuery(selector).append(data_append);
        }

        function populate_preview_table_rows(selector, data) {
            let data_append = "";
            let cols = 0;
            let counter = 0;
            data_append = '<tbody>';
            jQuery.each(data, function(row_key, row_value){

                cols = 0;
                counter = 0;

                jQuery.each( row_value, function() {
                    cols++;
                });

                data_append += '<tr>';
                jQuery.each( row_value, function(key, value) {
                    counter++;
                    if(counter == cols-1){
                        data_append += '<td style="background-color: #98E5E8;">'+value+'</td>';
                    } else if(counter == (cols-2)){
                        data_append += '<td style="background-color: #B6DCFD;">'+value+'</td>';
                    } else if(counter == cols){
                        if(value < 0){
                            data_append += '<td style="background-color: #FD7741;">'+value+'</td>';
                        } else if(value > 0){
                            data_append += '<td style="background-color: #FDF557;">'+value+'</td>';
                        } else {
                            data_append += '<td>'+value+'</td>';
                        }
                    } else {
                        data_append += '<td>'+value+'</td>';
                    }
                });
                data_append += "</tr>";
            });
            data_append += "</tbody></table>";
            jQuery(selector).append(data_append);
        }
*/

        var separate_column = "";
        var payment_code = "";
        var payment_amount = "";
        var transfer_code = "";
        var transfer_amount = "";

        jQuery('.maping-fields').change(function(){
            let selectedId = jQuery(this).attr('id');
            let selectedValue = jQuery( "#"+selectedId+" option:selected" ).val();

            if(selectedId == "separate-column"){

                if(separate_column != ""){
                    jQuery('#payment-headers-selector_'+separate_column).prop('checked', false);
                    //jQuery('#payment-headers-selector_'+separate_column).removeAttr("disabled");
                }
                separate_column = selectedValue;

                jQuery('#payment-headers-selector_'+selectedValue).prop('checked', true);
                //jQuery('#payment-headers-selector_'+selectedValue).attr("disabled", true);
            }

            if(selectedId == "payment-transaction-code" || selectedId == "payment-amount"){
                if(selectedId == "payment-transaction-code"){
                    if(payment_code != ""){
                        jQuery('#payment-headers-selector_'+payment_code).prop('checked', false);
                        //jQuery('#payment-headers-selector_'+payment_code).removeAttr("disabled");
                    }
                    payment_code = selectedValue;
                }
                if(selectedId == "payment-amount"){
                    if(payment_amount != ""){
                        jQuery('#payment-headers-selector_'+payment_amount).prop('checked', false);
                        //jQuery('#payment-headers-selector_'+payment_amount).removeAttr("disabled");
                    }
                    payment_amount = selectedValue;
                }

                jQuery('#payment-headers-selector_'+selectedValue).prop('checked', true);
                //jQuery('#payment-headers-selector_'+selectedValue).attr("disabled", true);
            }

            if(selectedId == "transfer-transaction-code" || selectedId == "transfer-amount"){
                if(selectedId == "transfer-transaction-code"){
                    if(transfer_code != ""){
                        jQuery('#transfer-headers-selector_'+transfer_code).prop('checked', false);
                        //jQuery('#transfer-headers-selector_'+transfer_code).removeAttr("disabled");
                    }
                    transfer_code = selectedValue;
                }
                if(selectedId == "transfer-amount"){
                    if(transfer_amount != ""){
                        jQuery('#transfer-headers-selector_'+transfer_amount).prop('checked', false);
                        //jQuery('#transfer-headers-selector_'+transfer_amount).removeAttr("disabled");
                    }
                    transfer_amount = selectedValue;
                }
                jQuery('#transfer-headers-selector_'+selectedValue).prop('checked', true);
                //jQuery('#transfer-headers-selector_'+selectedValue).attr("disabled", true);
            }
            //console.log(selectedId+" "+selectedText+" "+selectedValue);
        });

        jQuery('#upload-files-form').submit(function(e) {
            e.preventDefault();
            jQuery('#mapping-form').hide();
            let formData = new FormData(this);

            jQuery( "#separate-column" ).find('option').not(':first').remove();

            jQuery( "#payment-transaction-code" ).find('option').not(':first').remove();
            jQuery( "#transfer-transaction-code" ).find('option').not(':first').remove();
            jQuery( "#payment-amount" ).find('option').not(':first').remove();
            jQuery( "#transfer-amount" ).find('option').not(':first').remove();

            jQuery("#payment-headers-selector").empty();
            jQuery("#transfer-headers-selector").empty();

            jQuery.ajax({
                type:"POST",
                url: "{{ route('mapping.create') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (response) => {
                    if (response) {
                        //this.reset();
                        populate('#separate-column', response.payment_headers);

                        populate('#payment-transaction-code', response.payment_headers);
                        populate('#transfer-transaction-code', response.transfer_headers);
                        populate('#payment-amount', response.payment_headers);
                        populate('#transfer-amount', response.transfer_headers);

                        populate_headers_selectors('#payment-headers-selector', response.payment_headers);
                        populate_headers_selectors('#transfer-headers-selector', response.transfer_headers);

                        jQuery('#payment-file-link').val(response.payment_file);
                        jQuery('#transfer-file-link').val(response.transfer_file);

                        jQuery('#preview-payment-file-headings').val(response.payment_file_header_row);
                        jQuery('#preview-transfer-file-headings').val(response.transfer_file_header_row);

                        jQuery('#mapping-form').show();
                        console.log(response);
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });

        });

        jQuery('#map-and-report-form').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            jQuery("#form_errors").empty();
            jQuery("#result-table").hide();
            jQuery( "#result_table" ).empty();

            jQuery.ajax({
                type:"POST",
                url: "{{ route('mapping.preview') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (response) => {
                    if (response) {

                        //console.log(response);

                        populate_preview_table('#result_table', response.total_headers, response.result)

                        //populate_preview_table_header('#result_table', response.total_headers);
                        //populate_preview_table_rows('#result_table', response.result);

                        populate_space_between_tables('#result_table');

                        populate_preview_table('#result_table', response.total_headers, response.addition_result)

                        //populate_preview_table_header('#result_table', response.total_headers);
                        //populate_preview_table_rows('#result_table', response.addition_result);

                        jQuery('#report-type').val(response.request_data['type']);
                        jQuery('#report-mapping-name').val(response.request_data['mapping-name']);

                        jQuery('#report-separate-column').val(response.request_data['separate-column']);
                        jQuery('#report-separate-values').val(response.request_data['separate-values']);

                        jQuery('#report-payment-amount').val(response.request_data['payment-amount']);
                        jQuery('#report-payment-file-link').val(response.request_data['payment-file-link']);
                        jQuery('#report-payment-file-headings').val(response.request_data['payment-file-headings']);
                        jQuery('#report-payment-keep-columns').val(response.request_data['payment-headers-selector_headers']);
                        jQuery('#report-payment-transaction-code').val(response.request_data['payment-transaction-code']);

                        jQuery('#report-transfer-amount').val(response.request_data['transfer-amount']);
                        jQuery('#report-transfer-file-link').val(response.request_data['transfer-file-link']);
                        jQuery('#report-transfer-file-headings').val(response.request_data['transfer-file-headings']);
                        jQuery('#report-transfer-keep-columns').val(response.request_data['transfer-headers-selector_headers']);
                        jQuery('#report-transfer-transaction-code').val(response.request_data['transfer-transaction-code']);


                        jQuery('#result-block').show();
                        //console.log(response);
                    }
                },
                error: function(response){
                    let error_message = response.responseJSON.message;
                    let errors = response.responseJSON.errors;

                    let data_append = '<ul>';
                    for (const [key, value] of Object.entries(errors)) {
                        let element = "#"+key;
                        jQuery(element).addClass('custom_error_border');
                        data_append += '<li style="color: #FF0000">'+value[0]+'</li>';
                        console.log(key, value[0]);
                    }
                    data_append += '</ul>';

                    jQuery("#form_errors").append(data_append);

                    //console.log(response.responseJSON.errors);
                    //console.log(response.responseJSON.message);
                }
            });

        });

        jQuery('.custom_validate').change(function(e){
            if(jQuery(this).val() != ""){
                jQuery(this).removeClass('custom_error_border');
            } else {
                jQuery(this).addClass('custom_error_border');
            }
        })
    </script>
    <script src="{{ asset('assets/libs/dropify/dropify.min.js')}}"></script>
@endsection
