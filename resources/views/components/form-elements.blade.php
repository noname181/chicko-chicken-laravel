@switch($type)

@case("text")

<div class="form-group row">
    <label for={{$name}} class="col-sm-3 col-form-label">{{$label}}
        @isset($required)<span class="text-danger">*</span>
        @endisset
    </label>
    <div class="col-sm-9 input-wrapper">
        <input type={{ isset($subtype) ? $subtype : $type  }} class="form-control" name={{$name}} id={{$name}}
            value="{!!$value !!}" @isset($subtype) @if($subtype=='number' ) step=".00000001" @endif @endisset />
    </div>
</div>

@break

@case("file")

<div class="form-group row">
    <label class="col-sm-3 col-form-label">{{$label}}
        @isset($required)<span class="text-danger">*</span>
        @endisset
    </label>
    <div class="col-sm-9 input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id={{$name}} accept={{$accept}} name={{$name}} />
            <label class="custom-file-label" for={{$name}}>Choose file</label>
        </div>
        <div class="input-group-append">
            <span class="input-group-text">Upload</span>
        </div>
        <h6 class="d-block w-100 mt-2 mb-0">{{$info}}</h6>
        @if(!empty($value))
        <img src={{$value}} class="img-preview w-25" />
        @else
        <img src="" class="img-preview w-25" />
        @endif
    </div>
</div>

@break

@case("switch")

<div class="form-group row">
    <label for={{$name}} class="col-sm-3 col-form-label">{{$label}}
        @isset($required)<span class="text-danger">*</span>
        @endisset
    </label>
    <div class="col-sm-9 input-group  p-1">
        <input type="checkbox" name={{$name}} @if($value==1 || $value=='1' || $value=='on' ) checked @else @isset($checked) checked
            @endisset @endif data-bootstrap-switch data-off-color="danger" data-on-color="success" />
    </div>
</div>

@break

@case("datepicker")

<div class="form-group row">
    <label for={{$name}} class="col-sm-3 col-form-label">{{$label}}
        @isset($required)<span class="text-danger">*</span>
        @endisset
    </label>
    <div class="col-sm-9 input-group  p-1">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
            </div>
            <input type="text" name={{$name}} value="{{ date("d-m-Y", strtotime($value) ) }}" class="form-control" data-inputmask-alias="datetime"
                data-inputmask-inputformat="dd/mm/yyyy" data-mask>
        </div>
    </div>
</div>

@break

@case("select2")

<div class="form-group row">
    <label for={{$name}} class="col-sm-3 col-form-label">{{$label}}
        @isset($required)<span class="text-danger">*</span>
        @endisset
    </label>
    <select name={{$name}} class="col-sm-9 form-control select2" @isset($multiple) multiple="multiple" @endisset>
        @foreach ($options as $option)
        <option value="{{ $option->id }}" {{ ($option->id == $value ) ? "selected":"" }}>
            {{ $option->name }}</option>
        @endforeach
    </select>
</div>

@break

@default
Default case...
@endswitch