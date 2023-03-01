@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
    asset('assets/libs/multiselect/multi-select.css'),
     ] ])

{{--<link href="assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css">--}}
{{--<link href="assets/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css">--}}

@section('content')

    <div class="row">
    <div class="col-xl-8">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Basic Form</h4>
            <form id="campaignForm" method="POST" action="campaign/generate" data-parsley-validate="" novalidate="">
                @csrf

                @role('admin')
                    <div class="form-group row">
                        <div class="col-md-1" style="text-align: center">
                            <div><label class="col-form-label" for="sandbox">Sandbox</label></div>
                            <input name="sandbox" class="sandbox_selector" type="checkbox" id="sandbox" checked="" value="1" data-plugin="switchery" data-color="#00b19d" >
                        </div>
                    </div>
                @endrole

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="simpleinput">Name*</label>
                    <div class="col-md-10">
                        <input name="name" type="text" id="simpleinput" parsley-trigger="change" required="" class="form-control" value="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Language*</label>
                    <div class="col-md-10">
                        <select id="language" name="language" class="form-control" required="">
                            <option value="">Select</option>
                            @foreach ($languages as $language)
                                <option value="{{$language->id}}">{{$language->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <h4>Ad Platforms</h4>
                <div class="form-group row">

                    <div class="col-md-1" style="text-align: center">
                        <div><label class="col-form-label" for="ad_platforms_taboola">Taboola</label></div>
                        <input name="ad_platforms_taboola" class="platform_selector" type="checkbox" id="ad_platforms_taboola" checked="checked" value="taboola" data-plugin="switchery" data-color="#00b19d" >
                    </div>
                    <div class="col-md-2" style="text-align: center">
                        <div><label class="col-form-label" for="ad_platforms_outbrain">Outbrain</label></div>
                        <input name="ad_platforms_outbrain" class="platform_selector" id="ad_platforms_outbrain" type="checkbox" checked="checked" value="outbrain" data-plugin="switchery" data-color="#00b19d">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Website*</label>
                    <div class="col-md-10">
                        <select id="website" name="website" required="" class="form-control accounts_action">
                            <option value="">Select</option>
                            @foreach ($websites as $website)
                                <option value="{{$website->id}}">{{$website->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row" id="select_loader" style="display:none">
                    <img src="{{ asset('assets/images/loading.gif')}}" alt="" height="22" class="logo-light mx-auto">
                </div>

                <div class="row">
                    <div id="accounts_selects" class="col-md-12">
                        @include('admin.campaign._accounts', ['display' => 'dry'])
                    </div>
                </div>
                <div class="form-group row" id="audience_loader" style="display:none">
                    <img src="{{ asset('assets/images/loading.gif')}}" alt="" height="22" class="logo-light mx-auto">
                </div>
                <div id="audience-list" class="form-group row">
                </div>
                <h4>Devices</h4>
                <div class="form-group row">
                    @foreach ($devices as $key=> $device)
                        <div class="col-md-2" style="text-align: center">
                            <div><label class="col-form-label" for="simpleinput">{{$device['label']}}</label></div>
                            <input name="devices[]" type="checkbox" checked="" value="{{$key}}" data-plugin="switchery" data-color="#00b19d" data-parsley-mincheck="1" required>
                        </div>
                    @endforeach


                </div>

                <h4>Geos</h4>
                <div id="geo-list" class="form-group row">
                    @include('admin.campaign.geoList', ['geoList' => $geoList, "selected"=>[]])

                </div>
                <h4>Ads</h4>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="ads_url">Url* (ex: http://www.soolide.com/en/16720)</label>
                    <div class="col-md-10">
                        <input type="text" id="ads_url" name="ads_url" class="form-control" value="">
                    </div>
                </div>
                <h5>Titles</h5>
                <div id="ads-titles">
                    <div class="ads_titles-list">
                        <div class="ads_title-template" style="display: none;">
                            <div class="ads_title row">
                                <div class="col-md-10">
                                    <textarea name="" class="form-control titles-text"></textarea>
                                    <p class="title-length-message"></p>
                                </div>
                                <div class="col-md-2">
                                    <a href="#" id="removeAds_title" class="btn btn-danger waves-effect waves-light mr-1">
                                        -
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="ads_title">
                            <textarea name="ads_titles[]" class="form-control titles-text"></textarea>
                            <p class="title-length-message"></p>
                        </div>

                    </div>
                    <a href="#" id="addAds_title" class="btn btn-primary waves-effect waves-light mr-1">
                        +
                    </a>
                </div>
                <h5>Files</h5>
                <div id="files">
                    <div class="images-list">
                        <div class="image-template" style="display: none;">
                            <div class="image row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="file" name="" class="image_file form-control images">
                                        </div>
                                        <div class="col-md-1">
                                            <b>or</b>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="url" name="" placeholder="Link to image" class="image_link form-control images_link">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <a href="#" id="removeImage" class="btn btn-danger waves-effect waves-light mr-1">
                                        -
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="image row">
                            <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="file" name="images[]" class="form-control images">
                                </div>
                                <div class="col-md-1">
                                    <b>or</b>
                                </div>
                                <div class="col-md-6">
                                    <input type="url" name="images_links[]" placeholder="Link to image" class="form-control" id="images">
                                </div>
                            </div>
                            </div>

                        </div>

                    </div>
                    <a href="#" id="addImage" class="btn btn-primary waves-effect waves-light mr-1">
                        +
                    </a>
                </div>
                <div class="form-group text-right mb-0">
                    <a href="#" id="generate" class="btn btn-primary waves-effect waves-light mr-1">
                        Generate
                    </a>
                    <button type="reset" class="btn btn-secondary waves-effect waves-light">
                        Cancel
                    </button>
                </div>

            </form>
        </div>
    </div><!-- end col -->
    <div class="col-xl-4">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Preview</h4>
            <div class="preview"></div>
            @if(0)
            <div class="ads">
                <img id="placement1" src="#" alt="your image" style="width:100px"/>
                <br/>
                <img id="placement2" src="#" alt="your image" style="width:100px"/>
                <br/>
                <img id="placement3" src="#" alt="your image" style="width:100px"/>
                <br/>
                <img id="placement4" src="#" alt="your image" style="width:100px"/>
            </div>
            @endif
            <a href="#" class="btn btn-primary waves-effect" id="refresh">Refresh</a>
        </div>
    </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="createModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Creating campaigns</h5>

                </div>
                <div class="modal-body">
                    <div class="progress progress-lg">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: none">

                    <a href="/campaign-creation" class="btn btn-primary">Create more campaign</a>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/libs/switchery/switchery.min.js"></script>
{{--    <script src="assets/libs/multiselect/jquery.multi-select.js"></script>--}}
{{--    <script src="assets/libs/parsleyjs/parsley.min.js"></script>--}}

    <script>

        function generateOptions(data){
            let result = [];
            result['taboola'] = '';
            result['outbrain'] = '';
            $.each(JSON.parse(data), function(i, item) {
                console.log(item);
                if(item.platform == "taboola"){
                    result['taboola'] += '<option value="'+item.api_account_id+'">'+item.account_name+'</option>';
                }
                if(item.platform == "outbrain"){
                    result['outbrain'] += '<option value="'+item.api_account_id+'">'+item.account_name+'</option>';
                }
            });
            return result;
        }

        function getAccountsSelect(){
            let taboola = 0;
            let outbrain = 0;

            let geos = [];
            //let language = "";
            $("input:checkbox[class=geos_addition]:checked").each(function(){
                //console.log($(this).val());
                geos.push($(this).val());
            });
            //console.log(geos);

            $('#accounts_selects').empty();

            if($('input[id="ad_platforms_taboola"]:checked').length > 0){
                taboola = 1;
            }
            if($('input[id="ad_platforms_outbrain"]:checked').length > 0){
                outbrain = 1;
            }

            website_id = $("#website").val();

            $.ajax({
                url: "{{ route('campaign_f.campaignAccounts') }}",
                method: "GET",
                data: {
                    taboola_checked: taboola,
                    outbrain_checked: outbrain,
                    geos: geos,
                    website_id: website_id
                },
                success: function (msg) {
                    $("#accounts_selects").append(msg);
                    setTimeout(function () {
                        $('#accounts_selects [data-plugin="multiselect"]').multiSelect($(this).data());
                    }, 300);
                 }
            });
        }

        $(document).ready(function(){


            $('[data-plugin="switchery"]').each(function(e,t){new Switchery($(this)[0],$(this).data())})
            $('[data-plugin="multiselect"]').multiSelect($(this).data());

            let selected_geos = [];
            let min = 0;
            let max = 0;
            $("body").on('keyup', "textarea.titles-text", function() {
                let temp_geos = [];
                $('input.geos_addition:checked').each(function(){
                    temp_geos.push($(this).val());
                });
                var geos_is_same = selected_geos.length == temp_geos.length && selected_geos.every(function(element, index) {
                    return element === temp_geos[index];
                });

                let outbrain_checked = false;
                if($('input[name="ad_platforms_outbrain"]:checked').length){
                    outbrain_checked = true;
                }
                //console.log(geos_is_same);
                //console.log(outbrain_checked);

                let text_length = $(this).val().length;
                let element = $(this);
                let min_text_length = text_length + min;
                let max_text_length = text_length + max;
                if(!geos_is_same && outbrain_checked){
                    console.log("in ajax")
                    $.ajax({
                        type: "GET",
                        url: '/campaign-creation/getPrefixesLength',
                        data: {"geos_slugs": temp_geos},
                        success: function (responce) {
                            $.each($.parseJSON(responce), function(key, value){
                                if(key == 0){
                                    min = value;
                                } else {
                                    max = value;
                                }
                            });

                            min_text_length = text_length + min;
                            max_text_length = text_length + max;
                            element.next(".title-length-message").html("Title length including different outbrains prefixes: min: "+min_text_length+" max: "+max_text_length);
                            if(max_text_length > 90){
                                element.next(".title-length-message").css('color', "#FF0000");
                            } else {
                                $(this).next(".title-length-message").css('color', "#000000");
                            }
                            selected_geos = temp_geos;
                        }
                    });
                    //console.log('in prefix refresh');
                } else {
                    if(!outbrain_checked){
                        min = 0;
                        max = 0;
                        min_text_length = text_length + min;
                        max_text_length = text_length + max;

                        $(this).next(".title-length-message").html("Title length: "+min_text_length+"/90");
                    } else {
                        $(this).next(".title-length-message").html("Title length including different outbrains prefixes: min: "+min_text_length+" max: "+max_text_length);
                    }

                    if(max_text_length > 90){
                        $(this).next(".title-length-message").css('color', "#FF0000");
                    } else {
                        $(this).next(".title-length-message").css('color', "#000000");
                    }
                }
            });

            $('.accounts_action').on('change', function(){
                console.log('lang_change');
                getAccountsSelect();
            });

            $("body").on('change', ".geos_addition", function(){
                console.log('geos_change');
                getAccountsSelect();
            });

            $('.platform_selector').on('change', function(){
                console.log('platform_change');
                getAccountsSelect();
            });

            $('#language').on('change', function() {
                $.ajax({
                    type: "GET",
                    url: '/campaign-creation/generateCountriesPart?language='+this.value,
                    success: function( msg ) {
                        $("#geo-list").html(msg);
                        $('#geo-list [data-plugin="switchery"]').each(function(e,t){new Switchery($(this)[0],$(this).data())})
                        //$(".geos_addition").trigger("change");
                        getAccountsSelect();
                    }
                });
            });

            function readURL(input, placement) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $(placement).attr('src', e.target.result);
                    }

                    return reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }

            function validate() {
                var instance = $('#campaignForm').parsley();
                return  instance.validate();

            }
            $("#refresh").click(function (e) {
                e.preventDefault();
                //console.log(validate());
                let form = $('#campaignForm')[0];
                let formData = new FormData(form);
                //console.log(form);
                //console.log(formData);
                if(validate()) {
                    $.ajax({
                        type: "POST",
                        url: '/campaign-creation/preview',
                        data: formData,//$("#campaignForm").serialize(),
                        processData: false,
                        contentType: false,
                        success: function( msg ) {
                            $(".preview").html(msg);
                            /*
                            let index = 0;
                            $("body .images").each(function() {
                                index++;
                                readURL(this, "#placement"+index);
                                //console.log(this);
                                //console.log(readURL(this));
                            });
                            */
                        }
                    });
                }
            });

            function arr_diff (a1, a2) {

                var a = [], diff = [];

                for (var i = 0; i < a1.length; i++) {
                    a[a1[i]] = true;
                }

                for (var i = 0; i < a2.length; i++) {
                    if (a[a2[i]]) {
                        delete a[a2[i]];
                    } else {
                        a[a2[i]] = true;
                    }
                }

                for (var k in a) {
                    diff.push(k);
                }

                return diff;
            }

            var accountsGlobal = [];
            $("body").on('change', "#taboola_accounts", function() {
                var accountsLocal = [];
                $(this).find("option:selected" ).each(function() {
                    accountsLocal.push( $( this ).val());
                });
                if(accountsLocal.length < accountsGlobal.length) {
                    var diff = arr_diff(accountsLocal, accountsGlobal);
                    if(diff.length > 0) {
                        var $toremove = $("#audience-list").find(`[data-account_id='${diff[0]}']`);
                        if($toremove) $toremove.remove();
                    }
                    // remove difference
                } else {
                    var diff = arr_diff(accountsLocal, accountsGlobal);
                    if(diff.length > 0) {
                        $("#audience_loader").show();
                        var latest_value = diff[0];
                        $.ajax({
                            type: "GET",
                            url: '/campaign-creation/getCustomAudience',
                            data: {"id": latest_value},
                            success: function (msg) {
                                $("#audience-list").append(msg);
                                setTimeout(function () {
                                    $('#audience-list [data-plugin="multiselect"]').multiSelect($(this).data());
                                }, 300)
                                $("#audience_loader").hide();
                            }
                        });
                    }

                    console.log(accountsLocal);
                }
                accountsGlobal = accountsLocal;

            });

            var accountsGlobalOutbrain = [];
            $("body").on('change', "#outbrain_accounts", function() {
                var accountsLocalOutbrain = [];
                $(this).find("option:selected" ).each(function() {
                    accountsLocalOutbrain.push( $( this ).val());
                });
                if(accountsLocalOutbrain.length < accountsGlobalOutbrain.length) {
                    var diff = arr_diff(accountsLocalOutbrain, accountsGlobalOutbrain);
                    if(diff.length > 0) {
                        var $toremove = $("#audience-list").find(`[data-account_id='${diff[0]}']`);
                        if($toremove) $toremove.remove();
                    }
                    // remove difference
                } else {
                    var diff = arr_diff(accountsLocalOutbrain, accountsGlobalOutbrain);
                    if(diff.length > 0) {
                        $("#audience_loader").show();
                        var latest_value = diff[0];
                        $.ajax({
                            type: "GET",
                            url: '/campaign-creation/getCustomAudienceOutbrain',
                            data: {"id": latest_value},
                            success: function (msg) {
                                $("#audience-list").append(msg);
                                setTimeout(function () {
                                    $('#audience-list [data-plugin="multiselect"]').multiSelect($(this).data());
                                }, 300)
                                $("#audience_loader").hide();
                            }
                        });
                    }

                    console.log(accountsLocalOutbrain);
                }
                accountsGlobalOutbrain = accountsLocalOutbrain;

            });

            $("#generate").click(function (e) {
                e.preventDefault();
                if(validate()) {
                    $('#createModal').modal('show');
                    var progress = 0;
                    var interval = setInterval(function () {
                        if (progress <= 90) {
                            $("#createModal .progress-bar").css("width", progress + "%")
                            progress++;
                        } else {
                            clearInterval(interval);
                        }
                    }, 200)
                    var form = $('#campaignForm')[0];
                    var formData = new FormData(form);

                    // var files = $('.image input')[0].files;
                    //
                    // if(files.length > 0 ){
                    //     formData.append('file[]',files[0]);
                    // }
                    //  files = $('.image input')[1].files;
                    //
                    // if(files.length > 0 ){
                    //     formData.append('file[]',files[0]);
                    // }
                    // console.log(formData);
                    $.ajax({
                        type: "POST",
                        url: '/campaign-creation/generate',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (msg) {
                            clearInterval(interval);
                            var interval = setInterval(function () {
                                if (progress <= 100) {
                                    $("#createModal .progress-bar").css("width", progress + "%")
                                    progress++;
                                } else {

                                    clearInterval(interval);
                                    $("#createModal .modal-body").html(msg);
                                    $("#createModal .modal-footer").css("display", "block");
                                    $("#createModalLabel").text("Created");

                                }
                            }, 50)

                        }
                    });
                }
            })

            // Ads logic start
            $("#addAds_title").click(function (e) {
                e.preventDefault();
                var $imageInputTemplate =  $("#ads-titles .ads_title-template .ads_title").clone();
                $imageInputTemplate.attr("style", "");
                $imageInputTemplate.find("textarea").attr("name", "ads_titles[]");
                $imageInputTemplate.appendTo( ".ads_titles-list" );
            })
            $("body").on("click", "#removeAds_title", function (e) {
                e.preventDefault();
                $(this).closest(".ads_title").remove();
            })
            $("#addImage").click(function (e) {
                e.preventDefault();
                var $imageInputTemplate =  $("#files .image-template .image").clone();
                $imageInputTemplate.attr("style", "");
                $imageInputTemplate.find(".image_file").attr("name", "images[]");
                $imageInputTemplate.find(".image_link").attr("name", "images_links[]");
                $imageInputTemplate.appendTo( ".images-list" );
            })
            $("body").on("click", "#removeImage", function (e) {
                e.preventDefault();
                $(this).closest(".image").remove();
            })
        })


    </script>

@endsection
