@if($api!=="")
    <x-adminlte-select2 name="{{ $name }}" 
                        label="{{ $label }}" 
                        data-placeholder="{{ $placeholder }}" 
                        :disabled="$disabled" 
                        label-class="{{ $labelclass }}" 
                        data-fieldname="{{ $fieldname }}"                     
                        :multiple="$multiple" 
                        data-ajax--url="{{ $api }}"
                        data-minimum-results-for-search="{{ $minimumresultsforsearch }}">
    @if($prependSlotClass!=="")
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="{{ $prependSlotClass }}"></i>
            </div>
        </x-slot>
    @elseif($multiple)
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
    </x-adminlte-select2>
@else
    <x-adminlte-select2 name="{{ $name }}" 
                        label="{{ $label }}" 
                        data-placeholder="{{ $placeholder }}" 
                        :disabled="$disabled" 
                        label-class="{{ $labelclass }}" 
                        data-fieldname="{{ $fieldname }}"                     
                        :multiple="$multiple" 
                        data-minimum-results-for-search="{{ $minimumresultsforsearch }}">
    @if($prependSlotClass!=="")
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="{{ $prependSlotClass }}"></i>
            </div>
        </x-slot>
    @elseif($multiple)
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
@endif