@php
$heads = [
            ['label' => 'ID', 'no-export' => true],
            ['label' => 'Type'],
            ['label' => 'Datetime'],
            ['label' => 'Message'],
            ['label' => 'Userid'],
            ['label' => __('Actions'), 'no-export' => true]
         ]
@endphp

@section('content')
{{-- Include modal form for managing the record --}}
<x-alternative-modal-form id="errlogTableModify" theme="light" title="Error Log" method="read" actionread="api/errlogs/{id}" readonly>
    <x-alternative-input-key fieldname="id" />
    <div class="row">
        <div class="col">
            <x-alternative-input-textarea name="iCtrlMessage" label="Message" placeholder="Message" fieldname="message" disabled />
        </div>
    </div>
</x-alternative-modal-form>

{{-- Standard myobject AdminLTE datatable definition --}}
<x-adminlte-datatable id="errlog_datatable" :heads=$heads theme="light" head-theme="dark" bordered compressed striped hoverable>
</x-adminlte-datatable>
@endsection

@section ('js')
    <script>
    $(document).ready(function () {
        myo.UI.initializeDataTable('errlog_datatable');

        $('#errlog_datatable').DataTable({
            processing: true,
            serverSide: true,
            "bDestroy": true,
            createdRow: myo.UI.OnCreatedRow,
            ajax: '/api/errlogs',
            "order": [[ 2, 'desc' ]],
            onModifyActionForm: 'errlogTableModify',
            insertAllowed: false,
            columns: [
                    { data: 'id', "width": "10%" },
                    { data: 'type', "width": "10%", 'orderable': false },
                    { data: 'datetime', "width": "10%" },
                    { data: 'message', "width": "60%", 'orderable': false },
                    { data: 'userid', "width": "10%", 'orderable': false },
                    { data: 'actions', "width": "5%", 'searchable': false, 'orderable': false  }],
            dom: '<"container-fluid"<"row"<"col"B><"col-md-2"l><"col"f>>>rtip'
        });
    }); 
    </script>    
@endsection