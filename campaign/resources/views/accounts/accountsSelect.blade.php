<label class="col-md-2 col-form-label">Account*</label>
<div class="col-md-10">
    <select class="form-control account_selector" id="account" name="api_account_id">
        <option value="">{{ __('Please select') }}</option>
        @foreach ($accounts as  $account)
            @if($platform == 'taboola')
                <option value="{{$account->account_id}}" @if($selected == $account->account_id) selected="selected" @endif >{{$account->name}}</option>
            @elseif($platform == 'outbrain')
                <option value="{{$account->id}}" @if($selected == $account->id) selected="selected" @endif>{{$account->name}}</option>
            @else
            @endif
        @endforeach
    </select>
</div>
