@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Import files</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-primary" href="{{ route('mapping') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                    </div>
                </div>
            </div>

            <div>
                <h2>Mapped fields</h2>
                <p><i>Files structure should be same as was used for create this mapping. If structure is different you will get error.</i></p>
                <div class="row">
                    <div class="col-xl-6">
                        <p>Transaction code field for Payment file: {{ $mapping->payment->transaction_code }}</p>
                    </div>

                    <div class="col-xl-6">
                        <p>Transaction code field for Transfer file: {{ $mapping->transfer->transaction_code }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <p>Amount field for Payment file: {{ $mapping->payment->amount }}</p>
                    </div>

                    <div class="col-xl-6">
                        <p>Amount field for Transfer file: {{ $mapping->transfer->amount }}</p>
                    </div>
                </div>
            </div>

            <div>
                <form method="POST" action="" id="upload-files-form" enctype="multipart/form-data">
                    <input type="hidden" name="mapping_id" value="{{ $mapping_id }}">
                    <input type="hidden" name="type" value="mapping_preview" />
                    @csrf
                    <div class="row">
                        <div class="col-xl-6">
                            <label for="payment-file">Payment details file</label>
                            <input type="file" name="payment-file" class="form-control" id="payment-file">
                        </div>

                        <div class="col-xl-6">
                            <label for="transfer-file">Transfers details file</label>
                            <input type="file" name="transfer-file" class="form-control" id="transfer-file">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Import and generate preview</button>
                    </div>

                </form>
            </div>

            <div id="result-table" style="display: none">
                <div class="table-responsive">
                    <table id="result_table" class="table table-hover mb-0">
                    </table>
                </div>
                <div class="row">
                    <form method="POST" action="{{ route('mapping.report') }}" id="mape-and-report-form">
                        @csrf
                        <input type="hidden" name="type" id="report-type" value="" />
                        <input type="hidden" name="mapping-name" id="report-mapping-name" value="" />
                        <input type="hidden" name="mapping-id" id="report-mapping-id" value="" />
                        <input type="hidden" name="payment-amount" id="report-payment-amount" value="" />
                        <input type="hidden" name="payment-file-link" id="report-payment-file-link" value="" />
                        <input type="hidden" name="payment-transaction-code" id="report-payment-transaction-code" value="" />
                        <input type="hidden" name="transfer-amount" id="report-transfer-amount" value="" />
                        <input type="hidden" name="transfer-file-link" id="report-transfer-file-link" value="" />
                        <input type="hidden" name="transfer-transaction-code" id="report-transfer-transaction-code" value="" />
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Generate Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        function populate_preview_table_header(selector, data) {
            let data_append = "";
            data_append = '<thead><tr>';
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

                cols = row_value.length;
                counter = 0;

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
            data_append += "</tbody>";
            jQuery(selector).append(data_append);
        }

        jQuery('#upload-files-form').submit(function(e) {
            e.preventDefault();
            jQuery('#mapping-form').hide();
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

                        populate_preview_table_header('#result_table', response.total_headers);
                        populate_preview_table_rows('#result_table', response.result);

                        jQuery('#report-type').val(response.request_data['type']);
                        jQuery('#report-mapping-name').val(response.request_data['mapping-name']);
                        jQuery('#report-mapping-id').val(response.request_data['mapping-id']);
                        jQuery('#report-payment-amount').val(response.request_data['payment-amount']);
                        jQuery('#report-payment-file-link').val(response.request_data['payment-file-link']);
                        jQuery('#report-payment-transaction-code').val(response.request_data['payment-transaction-code']);
                        jQuery('#report-transfer-amount').val(response.request_data['transfer-amount']);
                        jQuery('#report-transfer-file-link').val(response.request_data['transfer-file-link']);
                        jQuery('#report-transfer-transaction-code').val(response.request_data['transfer-transaction-code']);

                        jQuery('#result-table').show();
                        console.log(response);
                    }
                },
                error: function(response){
                    console.log(response);
                }
            });

        });
    </script>
@endsection
