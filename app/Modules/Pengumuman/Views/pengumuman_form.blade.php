@foreach ($forms as $key => $value)
    <div class="row">
        <div class="col-md-3 text-sm-start text-md-end pt-2">
            <label>{{ $value[0] }}</label>
        </div>
        <div class="col-md-9 form-group">
            {{ $value[1] }}
            @error($key)
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endforeach
