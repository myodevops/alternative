<x-adminlte-date-range name="{{ $name }}" label="{{ $label }}" placeholder="{{ $placeholder }}" data-fieldname="{{ $fieldname }}" :config=$config :disabled="$disabled">
@if($prependSlotClass!=="")
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="{{ $prependSlotClass }}"></i>
        </div>
    </x-slot>
@else
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fa-solid fa-calendar-clock"></i>
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
</x-adminlte-date-range>