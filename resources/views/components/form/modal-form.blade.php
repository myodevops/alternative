<x-adminlte-modal id="{{ $id }}" 
                  title="{{ $title }}" 
                  theme="{{ $theme }}" 
                  data-actionread="{{ $actionread }}"
                  icon="fas fa-pen-to-square" 
                  size='lg' 
                  disable-animations>
    {{ $slot }}
    <x-slot name="footerSlot">
        <x-adminlte-button id="{{ $id . '-button' }}" 
                           theme="success" 
                           label="{{ __('Submit') }}" 
                           data-actionwrite="{{ $actionwrite }}" 
                           data-method="{{ $method }}" />
        <x-adminlte-button theme="danger" 
                           label="{{ __('Cancel') }}" 
                           data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>

@section ('js')
@parent
<script>
$(document).ready(function () {
        $('#{{ $id }}').on('click','#{{ $id }}-button',function(e){
            switch (this.dataset.method.toLowerCase()) {
                case 'modify':
                    if (typeof(this.dataset.actionwrite) != "undefined") {
                        var response = myo.WS.callUpdateRequest ("{{ $id }}", this.dataset.actionwrite, "{{ csrf_token() }}");
                        if (response == true) {
                            jQuery('#{{ $id }}').modal('hide');
                            dttable.ajax.reload();
                        }
                    }
                break;
            }
        });
    });
</script>
@endsection
