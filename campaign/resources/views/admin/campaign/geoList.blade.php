@foreach ($geoList as $geo)
    <div class="col-md-1" style="text-align: left">
        <div><label class="col-form-label" for="">__{{$geo->slug}}</label></div>
        <input class="geos_addition" name="geos[]" type="checkbox" @if(in_array($geo->slug, $selected)) checked @endif  value="{{$geo->slug}}" data-plugin="switchery" data-color="#00b19d" data-parsley-mincheck="1 " required>
    </div>
@endforeach
