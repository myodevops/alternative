<x-adminlte-input name="{{ $name }}" label="{{ $label }}" placeholder="{{ $placeholder }}" data-fieldname="{{ $fieldname }}" :disabled="$disabled" type="number" min="{{ $min }}" max="{{ $max }}" >
@if($prependSlotClass!=="")
    <x-slot name="prependSlot">
        <div class="input-group-text">
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
</x-adminlte-input>