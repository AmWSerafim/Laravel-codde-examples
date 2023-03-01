<div class="col-md-4" data-account_id="{{$accountId}}">
    <h5>{{$accountName}}</h5>
    @if(count($audiencesList) > 0)
    <select multiple="multiple" class="multi-select" id="audience" name="audience[{{$accountId}}][]" data-plugin="multiselect" >
        @foreach ($audiencesList as $slug => $name)
            <option value="{{ $slug }}">{{ $name }}</option>
        @endforeach

    </select>
    @else
        This account dont have audience
    @endif
</div>
