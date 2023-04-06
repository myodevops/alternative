<x-adminlte-card id="{{ $id }}" 
                 title="{{ $title }}" 
                 theme="{{ $theme }}" 
                 icon="fas fa-pen-to-square" 
                 size='lg' 
                 bodyclass="{{ $bodyclass }}"
                 headerclass="{{ $headerclass }}"
                 footerclass="{{ $footerclass }}"
                 icon="{{ $icon }}"
                 :collapsible="$collapsible"
                 :removable="$removable"
                 :maximizable="$maximizable"
                 data-actionread="{{ $actionread }}">
    {{ $slot }}
    <x-slot name="footerSlot" class="col-auto">
        <x-adminlte-button theme="danger"
                           class="float-right ml-2"
                           label="{{ __('Cancel') }}"  />
        <x-adminlte-button id="{{ $id . '-button' }}" 
                           theme="success" 
                           label="{{ __('Submit') }}"
                           data-actionwrite="{{ $actionwrite }}"
                           class="float-right" 
                           data-method="{{ $method }}" />
    </x-slot>
</x-adminlte-card>

@section ('js')
@parent
<script>
    $(document).ready(function(){
        if (typeof($('#{{ $id }}')[0].dataset.actionread) != "undefined") {
            var form = jQuery('#{{ $id }}')[0];
            var response = myo.WS.callReadRequest (form.dataset.actionread, "{{ csrf_token() }}");
            if (response.success !== true) {
                alert (response.data);
            } else {
                myo.UI.populateFields (form, response.data);
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#{{ $id }}').on('click','#{{ $id }}-button',function(e){
            switch (this.dataset.method.toLowerCase()) {
                case 'modify':
                    if (typeof(this.dataset.actionwrite) != "undefined") {
                        var response = myo.WS.callUpdateRequest ("{{ $id }}", this.dataset.actionwrite, "{{ csrf_token() }}");
                        if (response !== true) {
                            alert (response);
                        }
                    }
                break;
            }
        });
    });
</script>
@endsection