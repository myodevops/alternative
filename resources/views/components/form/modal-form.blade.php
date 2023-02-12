<x-adminlte-modal id="{{ $id }}" title="{{ $title }}" theme="{{ $theme }}"
 icon="fas fa-pen-to-square" size='lg' disable-animations>
    {{ $slot }}
    <x-slot name="footerSlot">
        <x-adminlte-button id="{{ $id . '-button' }}" theme="success" label="{{ __('Submit') }}" data-actionread="{{ $actionread }}" data-actionwrite="{{ $actionread }}" data-method="{{ $method }}" data-dismiss="modal"/>
        <x-adminlte-button theme="danger" label="{{ __('Cancel') }}" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>