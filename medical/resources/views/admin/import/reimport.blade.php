@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-rounded" href="{{ route('import') }}" title="Back to scenarios"><i class="fas fa-backward "></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Import files</h2>
                    </div>
                </div>
            </div>

            <div>
                <h4>Mapped fields</h4>
                <p class="text-muted"><i>Files structure should be same as was used for create this mapping. If structure is different you will get error.</i></p>
                <div class="row">
                    <div class="col-xl-6">
                        <p>Transaction code field for Practice Management - Billing file: </p>
                        <p><b>{{ $mapping->payment->transaction_code }}</b></p>
                    </div>

                    <div class="col-xl-6">
                        <p>Transaction code field for Bank EFT file:</p>
                        <p><b>{{ $mapping->transfer->transaction_code }}</b></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <p>Amount field for Practice Management - Billing file: </p>
                        <p><b>{{ $mapping->payment->amount }}</b></p>
                    </div>

                    <div class="col-xl-6">
                        <p>Amount field for Bank EFT file: </p>
                        <p><b>{{ $mapping->transfer->amount }}</b></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <p>Displayed columns for Practice Management - Billing file:</p>
                        <ul>
                            @foreach($selected_headers_text['payment'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-xl-6">
                        <p>Displayed columns for Bank EFT file:</p>
                        <ul>
                            @foreach($selected_headers_text['transfer'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                     </div>
                </div>
            </div>

            <div>
                <div class="alert alert-danger" id="form_errors" style="display: none;"></div>
                <form method="POST" action="" id="upload-files-form" enctype="multipart/form-data">
                    <input type="hidden" name="mapping_id" value="{{ $mapping_id }}">
                    <input type="hidden" name="type" value="mapping_preview" />
                    @csrf
                    <div class="row">
                        <div class="col-xl-6">
                            <label for="payment-file">
                                Practice Management - Billing details file
                                @if($files_extensions['payment'] != "")
                                    ( .{{ $files_extensions['payment'] }} )
                                @endif
                            </label>
                            <input type="file" name="payment-file" class="dropify" id="payment-file">
                            <p>Headings row number: {{ $header_row->payment }}</p>
                        </div>

                        <div class="col-xl-6">
                            <label for="transfer-file">
                                Bank EFT details file
                                @if($files_extensions['transfer'] != "")
                                    ( .{{ $files_extensions['transfer'] }} )
                                @endif
                            </label>
                            <input type="file" name="transfer-file" class="dropify" id="transfer-file">
                            <p>Headings row number: {{ $header_row->transfer }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-rounded">Import and generate preview</button>
                    </div>

                </form>
            </div>

            <div id="result-table" style="display: none">
                <form method="POST" action="{{ route('mapping.export') }}" id="mape-and-report-form">
                    @csrf
                    <input type="hidden" name="type" id="report-type" value="" />
                    <input type="hidden" name="mapping-name" id="report-mapping-name" value="" />
                    <input type="hidden" name="mapping-id" id="report-mapping-id" value="" />

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
                            <button type="submit" class="btn btn-primary btn-rounded">Generate Report</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="result_table" class="table table-hover mb-0">
                        </table>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-rounded">Generate Report</button>
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

        jQuery('#upload-files-form').submit(function(e) {
            e.preventDefault();
            jQuery('#mapping-form').hide();
            jQuery("#form_errors").hide();
            jQuery("#form_errors").html("");
            let formData = new FormData(this);

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
                        console.log(response);
                        if(response.error){
                            jQuery("#form_errors").html(response.error);
                            jQuery("#form_errors").show();
                        } else {
                            populate_preview_table('#result_table', response.total_headers, response.result)
                            populate_space_between_tables('#result_table');
                            populate_preview_table('#result_table', response.total_headers, response.addition_result)
                            //                        populate_preview_table_header('#result_table', response.total_headers);
                            //                        populate_preview_table_rows('#result_table', response.result);

                            jQuery('#report-type').val(response.request_data['type']);
                            jQuery('#report-mapping-name').val(response.request_data['mapping-name']);
                            jQuery('#report-mapping-id').val(response.request_data['mapping-id']);

                            jQuery('#report-separate-column').val(response.request_data['separate-column']);
                            jQuery('#report-separate-values').val(response.request_data['separate-values']);

                            jQuery('#report-payment-amount').val(response.request_data['payment-amount']);
                            jQuery('#report-payment-file-link').val(response.request_data['payment-file-link']);
                            jQuery('#report-payment-file-headings').val(response.request_data['payment-file-headings']);
                            jQuery('#report-payment-keep-columns').val(response.request_data['payment-keep-headers']);
                            jQuery('#report-payment-transaction-code').val(response.request_data['payment-transaction-code']);

                            jQuery('#report-transfer-amount').val(response.request_data['transfer-amount']);
                            jQuery('#report-transfer-file-link').val(response.request_data['transfer-file-link']);
                            jQuery('#report-transfer-file-headings').val(response.request_data['transfer-file-headings']);
                            jQuery('#report-transfer-keep-columns').val(response.request_data['transfer-keep-headers']);
                            jQuery('#report-transfer-transaction-code').val(response.request_data['transfer-transaction-code']);

                            jQuery('#result-table').show();
                            console.log(response);
                        }
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });

        });
    </script>
@endsection
