@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@if (in_array('add_campaign_template', $userPermissions))
    @section('create-button')
        <a href="{{ route('admin.campaign-template.create') }}" class="btn btn-dark btn-sm m-l-15">
            <i class="fa fa-plus-circle"></i> @lang('app.createNew')
        </a>
    @endsection
@endif

@section('content')
    <style>
    #campaign-link{
            background-color: transparent !important;

    }
        .content-header {
            display: none !important;
        }

        .content-header1 {
            padding: 15px .5rem !important;
        }

        .breadcrumb {
            padding: 0.3rem 0rem !important;
        }
		.showcampsection2 {
			margin: 0px 10px;
		}
		.btn-cust2 {
			padding: 3px 8px;
			font-size: 13px;
			margin-right: 5px;
		}
    </style>

    <section class="content-header1">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-5">
                    <h4 class="mb-xs-2">Email Templates</h4>
                </div>
                <div class="col-md-7">
                    <span class="float-sm-right">
                        <a href="{{ route('admin.campaign-template.create') }}" class="btn btn-dark btn-sm m-l-15">
                            <i class="fa fa-plus-circle"></i> Create New
                        </a>
                    </span>
					<!--
                    <ol class="breadcrumb float-sm-right mr-2 mt-xs-2">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item active">Email Templates</li>
                    </ol>
					-->
                </div>
            </div>
        </div>
    </section>

    <div class="row showcampsection2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="templateTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Template</th>
                                     <th>Preview</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates as $key => $template)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route('admin.campaign-template.edit', $template->id) }}">{{ $template->title ?? '' }}</a></td>
                                        <td>
    @if ($template->preview)
        <a href="{{ asset($template->preview) }}" target="_blank">
            <img src="{{ asset($template->preview) }}" alt="Preview" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
        </a>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>

                                        <td>
                                            <a href="{{ route('admin.campaign-template.edit', $template->id) }}"
                                                class="btn btn-info btn-sm btn-cust2">@lang('app.edit')</a>

                                            <button class="btn btn-danger btn-sm sa-params btn-cust2"
                                                data-row-id="{{ $template->id }}">@lang('app.delete')</button>
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
        $(document).ready(function () {
            var table = $('#templateTable').DataTable({
                responsive: true,
                "fnDrawCallback": function () {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                }
            });

            new $.fn.dataTable.FixedHeader(table);

            $('body').on('click', '.sa-params', function () {
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
                }, function (isConfirm) {
                    if (isConfirm) {
                        var url = "{{ route('admin.campaign-template.destroy', ':id') }}".replace(':id', id);

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': '{{ csrf_token() }}',
                                '_method': 'DELETE'
                            },
                            success: function (response) {
                                if (response.status === "success") {
                                    table.draw();
                                            $.unblockUI(); // stop loader
        location.reload(true); // force hard reload

                                }
                            }
                        });
                    }
                });
            });

            const breadcrumbItem = document.querySelector('.breadcrumb-item.active');
            if (breadcrumbItem) {
                breadcrumbItem.textContent = 'Campaign Templates';
            }

            const heading = document.querySelector('h1.mb-xs-2');
            if (heading) {
                heading.textContent = 'Campaign Templates';
            }
        });
    </script>
@endpush
