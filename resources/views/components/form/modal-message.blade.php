<x-adminlte-modal id="{{ $id }}" title="myobject" theme="{{ $theme }}"
    icon="fas fa-circle-question" size='sm' disable-animations static-backdrop>
    {{ $slot }}
    <x-slot name="footerSlot">
        <x-adminlte-button theme="success" label="{{ __('Ok') }}" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>