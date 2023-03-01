@if($display != 'dry')
    @if(isset($accounts['taboola']))
        <div class="form-group row" id="taboola_accounts">
            <label class="col-md-2 col-form-label">Taboola Accounts*</label>
            <div class="col-md-10">
                <select multiple="multiple" class="multi-select" data-plugin="multiselect" id="select_taboola_accounts" name="select_taboola_accounts[]" data-parsley-required>
                    @foreach($accounts['taboola'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    @if(isset($accounts['outbrain']))
        <div class="form-group row" id="outbrain_accounts">
            <label class="col-md-2 col-form-label">Outbrain Accounts*</label>
            <div class="col-md-10">
                <select multiple="multiple" class="multi-select" data-plugin="multiselect" id="select_outbrain_accounts" name="select_outbrain_accounts[]" data-parsley-required>
                    @foreach($accounts['outbrain'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
@endif
