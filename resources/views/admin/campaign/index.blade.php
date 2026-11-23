@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@if (in_array('add_team', $userPermissions))
    @section('create-button')
        <a href="{{ route('admin.campaign.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i>
            @lang('app.createNew')</a>
    @endsection
@endif

@section('content')
    <style>
        .content-header {
            display: none !important;

        }

        .content-header1 {
            padding: 15px 10px 15px 10px !important;
        }

        .breadcrumb {
            padding: 0px !important;
        }
		.showcampsection2 {
			margin: 0px 10px;
		}
		.btn-cust2 {
			padding: 3px 8px;
			font-size: 13px;
			margin-right: 5px;
			margin-bottom: 10px;
		}
    </style>
    <section class="content-header1">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-5">
                    <h4 class="mb-xs-2">Email Campaigns</h4>
                </div>
                <div class="col-md-7">
                    <span class="float-sm-right"><a href="https://careers.gaffis.com/public/admin/campaign/create" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> Create New</a> </span>
                    <ol class="breadcrumb float-sm-right mr-2 mt-xs-2">
                        <li class="breadcrumb-item"><a href="https://careers.gaffis.com/public/admin/dashboard"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item active">Campaigns</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <div class="row showcampsection2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Name')</th> <!-- Campaign Name -->
                                    <th>@lang('Template')</th> <!-- Campaign Template Title -->
                                    <th>@lang('Senders')</th> <!-- Total Emails -->
                                            <th>@lang('Status')</th> <!-- New -->

                                    <th>@lang('Action')</th> <!-- View/Edit Actions -->
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaigns as $key => $campaign)
                                    <tr>
                                        <td style="width: 5%;">{{ $key + 1 }}</td>
                                        <td style="width: 35%;"><a href="{{ route('admin.campaign.show', ['campaign' => $campaign->campaign_id]) }}">{{ $campaign->campaign_name ?? '' }}</a></td>
                                        <td style="width: 30%;">{{ $campaign->template_name ?? '' }}</td>
                                        <td style="width: 5%;">{{ $campaign->total_emails ?? '' }}</td>
                                        <td>
              @if($campaign->status == 1)
<button class="btn btn-success btn-cust2 change-status" data-id="{{ $campaign->campaign_id }}" data-status="{{ $campaign->status }}">Active</button>
@else
<button class="btn btn-danger btn-cust2 change-status" data-id="{{ $campaign->campaign_id }}" data-status="{{ $campaign->status }}">Inactive</button>
@endif
            </td>
                                        <td style="width: 25%;">
                                             
                                            <a href="{{ route('admin.campaign.show', ['campaign' => $campaign->campaign_id]) }}"
                                                class="btn btn-warning btn-cust2">
                                                @lang('View')
                                            </a>
                                            <a href="{{ route('admin.campaign.edit', ['campaign' => $campaign->campaign_id]) }}"
                                                class="btn btn-info btn-cust2">
                                                @lang('app.edit')
                                            </a>
											<form action="{{ route('admin.campaign.destroy', ['campaign' => $campaign->campaign_id]) }}"
											method="POST" style="display:inline-block;"
											onsubmit="return confirm('Are you sure you want to delete this campaign?');">
											@csrf
											@method('DELETE')
												<button type="submit" class="btn btn-danger btn-cust2">
												@lang('Delete')
												</button>
											</form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                }
            });

            new $.fn.dataTable.FixedHeader(table);



            $('body').on('click', '.sa-params', function() {
                var id = $(this).data('row-id');
                swal({
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('app.delete')",
                    cancelButtonText: "@lang('app.cancel')",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {

                        var url = "{{ route('admin.campaign.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    //                                    swal("Deleted!", response.message, "success");
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('change', '.role_id', function() {
                var id = $(this).val();
                var teamId = $(this).data('row-id');
                var url = '{{ route('admin.campaign.changeRole') }}';

                $.easyAjax({
                    url: url,
                    type: 'POST',
                    data: {
                        roleId: id,
                        teamId: teamId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            //                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                })
            });

            const breadcrumbItem = document.querySelector('.breadcrumb-item.active');
            if (breadcrumbItem) {
                breadcrumbItem.textContent = 'Campaign'; // Change the breadcrumb text to "Campaign"
            }

            // Update <h1> text
            const heading = document.querySelector('h1.mb-xs-2');
            if (heading) {
                heading.textContent = 'Campaign'; // Change the h1 text to "Campaign"
            }
            
            
      $('body').on('click', '.change-status', function () {
    var id = $(this).data('id');
    var currentStatus = $(this).data('status');
    var newStatus = currentStatus == 1 ? 0 : 1;
    var statusText = newStatus == 1 ? "activate" : "deactivate";

    swal({
        title: "Are you sure?",
        text: "Do you really want to " + statusText + " this campaign?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, " + statusText,
        cancelButtonText: "Cancel",
        closeOnConfirm: true
    }, function (isConfirm) {
        if (isConfirm) {
            var url = "{{ route('admin.campaign.changeStatus', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    status: newStatus
                },
                success: function (response) {
                    if (response.status == "success") {
                        let row = $('button.change-status[data-id="' + id + '"]').closest('tr');

                        // update badge
                        if (newStatus == 1) {
                            row.find('td:nth-child(5)').html('<span class="badge badge-success">Active</span>');
                        } else {
                            row.find('td:nth-child(5)').html('<span class="badge badge-danger">Inactive</span>');
                        }

                        // update button status value
                        $('button.change-status[data-id="' + id + '"]').data('status', newStatus);

                        swal("Updated!", "Campaign status changed successfully.", "success");
                    }
                }
            });
        }
    });
});


        });
    </script>
@endpush
