<x-adminlte-input name="{{ $name }}" label="{{ $label }}" placeholder="{{ $placeholder }}" :disabled="$disabled" onchange="myo.UI.validateEmailField(this, event)" email >
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