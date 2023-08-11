@php
$heads = [
            ['label' => 'ID', 'no-export' => true],
            ['label' => 'DateTime'],
            ['label' => 'Message'],
            ['label' => __('Actions'), 'no-export' => true]
         ]
@endphp

@section('content')
{{-- Include modal form for managing the record --}}
<x-alternative-modal-form id="laravellogTableModify" theme="light" title="Laravel Log" method="read" actionread="api/laravellogs/{id}" readonly>
    <x-alternative-input-key fieldname="id" />
    <div class="row">
        <div class="col">
            <x-alternative-input-textarea name="iCtrlStackTrace" label="Stack Trace" placeholder="Stacktrace" fieldname="stacktrace" rows=10 wrap="off" disabled />
        </div>
    </div>
</x-alternative-modal-form>

{{-- Standard myobject AdminLTE datatable definition --}}
<x-adminlte-datatable id="laravellog_datatable" :heads=$heads theme="light" head-theme="dark" bordered compressed striped hoverable>
</x-adminlte-datatable>
@endsection

@section ('js')
    <script>
    $(document).ready(function () {
        myo.UI.initializeDataTable('laravellog_datatable');

        $('#laravellog_datatable').DataTable({
            processing: true,
            serverSide: true,
            "bDestroy": true,
            createdRow: myo.UI.OnCreatedRow,
            ajax: '/api/laravellogs',
            "order": [[ 1, 'desc' ]],
            onModifyActionForm: 'laravellogTableModify',
            insertAllowed: false,
            columns: [
                    { data: 'id', 'searchable': false, 'visible': false },
                    { data: 'datetime', "width": "10%" },
                    { data: 'message', "width": "85%", 'orderable': false },
                    { data: 'actions', "width": "5%", 'searchable': false, 'orderable': false  }],
            dom: '<"container-fluid"<"row"<"col"B><"col-md-2"l><"col"f>>>rtip'
        });
    }); 
    </script>    
@endsection