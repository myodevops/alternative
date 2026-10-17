<x-adminlte-modal id="{{ $id }}" 
                  title="myobject" 
                  theme="{{ $theme }}"
                  icon="fas fa-circle-question" 
                  size='sm' 
                  disable-animations
                  static-backdrop>
    {{ $slot }}
    <x-slot name="footerSlot">
        <x-adminlte-button id="{{ $id . '-button' }}" theme="success" label="{{ $oklabel }}" data-action="{{ $action }}" data-method="{{ $method }}"/>
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
                        // On error callDeleteRequest has already shown the message to the user, and the modal stays open
                        var response = myo.WS.callDeleteRequest (this.dataset.actionset, "{{ csrf_token() }}");
                        if (response === true) {
                            jQuery('#{{ $id }}').modal('hide');
                            // Reload the current page of the table from the server, keeping the paging
                            dttable.ajax.reload(null, false);
                        }
                    }
                break;
            }
        });
    });
</script>
@endsection