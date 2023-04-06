@if($multiple)
    <x-adminlte-select2 name="{{ $name }}" 
                        label="{{ $label }}" 
                        placeholder="{{ $placeholder }}" 
                        :disabled="$disabled" 
                        label-class="{{ $labelclass }}" 
                        data-fieldname="{{ $fieldname }}"  
                        multiple 
                        class="select2">
    @if($prependSlotClass!=="")
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="{{ $prependSlotClass }}"></i>
            </div>
        </x-slot>
    @else
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="fa-solid fa-square-xmark"></i>
            </div>
        </x-slot>
    @endif
    @if($appendSlotClass!=="")
        <x-slot name="appendSlot">
            <div class="{{ $labelclass }}">
                <i class="{{ $appendSlotClass }}"></i>
            </div>
        </x-slot>
    @endif
    @foreach ($options as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
    </x-adminlte-select2>
@else
    <x-adminlte-select name="{{ $name }}" 
                       label="{{ $label }}" 
                       placeholder="{{ $placeholder }}" 
                       :disabled="$disabled"
                       data-fieldname="{{ $fieldname }}" 
                       label-class="{{ $labelclass }}" >
    @if($prependSlotClass!=="")
        <x-slot name="prependSlot">
            <div class="{{ $labelclass }}">
                <i class="{{ $prependSlotClass }}"></i>
            </div>
        </x-slot>
    @endif
    @if($appendSlotClass!=="")
        <x-slot name="appendSlot">
            <div class="input-group-text">
                <i class="{{ $appendSlotClass }}"></i>
            </div>
        </x-slot>
    @endif
    @foreach ($options as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
    </x-adminlte-select>
@endif
