@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@section('content')
<style>
    .content-header { display: none !important; }
    .content-header1 { padding: 15px 10px !important; }
    .breadcrumb { padding: 0px !important; }
    .btn-cust2 { padding: 3px 8px; font-size: 13px; margin-right: 5px; }
</style>

<section class="content-header1">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <h4 class="mb-xs-2">Campaign Stats</h4>
            </div>
            <div class="col-md-7">
                <ol class="breadcrumb float-sm-right mr-2 mt-xs-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Campaign Stats</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Filter -->
<div class="row mb-3 showcampsection2">
   <div class="col-md-3">
    <select name="filter" class="form-control">
        <option value="" selected>All</option> <!-- default all -->
        <option value="unsubscribed">Unsubscribed</option>
        <option value="error_or_bounced">Error / Bounced</option>
    </select>
</div>

    <div class="col-md-2">
        <button id="filterBtn" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
    </div>
</div>

<!-- Table -->
<div class="row showcampsection2">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40">
                   <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Candidate Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Total Emails Sent</th>
                                <th>Unsubscribed</th>
                                <th>Error/Bounced</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#myTable').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
        pageLength: 50, // default 50 records per page
        stateSave: true, // save table state
        ajax: {
            url: '{{ route("admin.campaign.stats") }}',
            type: 'GET',
            data: function(d) {
                d.filter = $('select[name="filter"]').val(); // send selected filter
            }
        },
        columns: [
            { data: null, render: function(data, type, row, meta){
                return meta.row + 1 + meta.settings._iDisplayStart;
            }},
            { data: 'full_name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'total_emails_sent' },
            { data: 'unsubscribed', render: function(data){
                return data == 'Yes' 
                    ? '<span class="badge badge-danger">Yes</span>' 
                    : '<span class="badge badge-success">No</span>';
            }},
            { data: 'error_or_bounced', render: function(data){
                return data == 'yes' 
                    ? '<span class="badge badge-danger">Yes</span>' 
                    : '<span class="badge badge-success">No</span>';
            }},
        ]
    });

    new $.fn.dataTable.FixedHeader(table);

    // Reload table when filter button is clicked
    $('#filterBtn').click(function() {
        table.ajax.reload();
    });
});

</script>
@endpush
