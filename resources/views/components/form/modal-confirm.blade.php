<x-adminlte-modal id="{{ $id }}" 
                  title="myobject" 
                  theme="{{ $theme }}"
                  icon="fas fa-circle-question" 
                  size='sm' 
                  disable-animations>
    {{ $slot }}
    <x-slot name="footerSlot">
        <x-adminlte-button id="{{ $id . '-button' }}" theme="success" label="{{ $oklabel }}" data-action="{{ $action }}" data-method="{{ $method }}" data-dismiss="modal"/>
        <x-adminlte-button theme="danger" label="{{ $cancellabel }}" data-dismiss="modal"/>
    </x-slot>        
</x-adminlte-modal>

@section ('js')
@parent
<script>
$(document).ready(function () {
        $('#{{ $id }}').on('click','#{{ $id }}-button',function(e){
            switch (this.dataset.method.toLowerCase()) {
                case 'delete':
                    if (typeof(this.dataset.actionset) != "undefined") {
                        var response = myo.WS.callDeleteRequest (this.dataset.actionset, "{{ csrf_token() }}");
                        if (response === true) {
                            jQuery('#{{ $id }}').modal('hide');
                            dttable.row('.selected').remove().draw(false);
                        } else {
                            alert (response);
                        }
                    }
                break;
            }
        });
    });
</script>
@endsection