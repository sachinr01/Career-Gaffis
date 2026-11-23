@extends('layouts.app')



@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush



@section('content')
    <style>
        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .step-indicator {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            color: #ccc;
            font-weight: bold;
        }
        .step-indicator.active {
            border-color: #007bff;
            color: #007bff;
        }

        .step-indicator.completed {
            background-color: #007bff;
            color: white;
        }
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-group {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
        .email-table {
            margin-top: 20px;
            width: 100%;
        }
        .email-table th,
        .email-table td {
            text-align: center;
        }

        /* Loader Styles */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            text-align: center;
        }
        .spinner-border {
            margin-right: 70px;
        }
  
      
    	/* Force all checkboxes in the emails table to be visible */
    	#emailsTable input[type="checkbox"],
    	#emailsTable .form-check-input {
    		display: inline-block !important;
    		visibility: visible !important;
    		opacity: 1 !important;
    		width: 18px !important;
    		height: 18px !important;
    		position: static !important; /* reset absolute positioning */
    		margin: 0 auto !important;   /* center in cell */
    	}
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
     	.subheading2 {
    		font-size: 12px;
    		line-height: 14px;
    	}
    	.table-responsive.mt15 {
    		margin-top: 15px !important;
    	}
    	.statsshow {
    		text-align: right;
    		letter-spacing: 1px;
    	}
    	.statsshow span {
    		font-style: italic;
    		font-weight: 700;
    	}
     	.tablink2 {
    		font-size: 13px;
    		letter-spacing: 1px;
    		margin-right: 15px;
    		background: #DBDBDB;
    		padding: 5px 10px;
    		border: 2px solid #DBDBDB;
    		border-radius: 4px;
    	}
    	.tablink2.active2 {
    		background: green;
    		border: 2px solid green;
     		color: #FFF;
    	}
    	.statsshow span {
    font-style: italic;
    font-weight: 400 !important;
    font-size: 13px;
    letter-spacing: 1px !important;
    margin-right: 5px;
    padding: 6px 12px !important;
}
    </style>


    <section class="content-header1">

        <div class="container-fluid">
  
            <div class="row">

				<div class="col-md-8">
					<h4 class="mb-xs-2">{{ __($campaign->name) }}</h4>
					<div class="form-group mb-1 subheading2">
						<label>@lang('Subject:')</label>
						<span>{{ $campaign->subject ?? 'N/A' }}</span>
					</div>
					<div class="form-group mb-1 subheading2">
						<label>@lang('Template:')</label>
						<span>{{ $campaign->template_name ?? 'N/A' }}</span>
					</div>
				</div>

                <div class="col-md-4">
                    <!-- Tabs Buttons -->
					<div class="mb-3 tablink2_section">
 						<button class="tablink2 active2" id="showAllEmails">View Campaign </button>
						<button class="tablink2" id="showSentEmails">Sent Emails</button>
					</div>
				</div>

            </div>
			
			<div class="row">
				<div class="col-md-12">
					
                </div>
			</div>

        </div><!-- /.container-fluid -->

    </section>

    <style>
        .duplicate-row {
    background: #fff3cd !important;
    font-weight: 600;
}

    </style>
  <div class="row showcampsection2">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                 
 
                <!-- All Emails Section -->
                <div id="allEmailsDiv">
					
					<div class="row">
						<div class="col-md-7">
							<h4>Candidates in the campaign</h4>
						</div>
						<div class="col-md-5">
							<div class="statsshow">
							    <span class="badge badge-success"><strong>Sent:</strong> {{ $sentCount ?? '0'}} </span> 
							    <span class="badge badge-warning"><strong>Pending:</strong> {{ $unsentCount ?? '0'}}</span>
							    <span class="badge badge-info"><strong>Total:</strong> {{ $sentCount+$unsentCount ?? '0'}} </span>
						    </div>
						</div>
					</div>
					
					
					
                    
                    <div class="table-responsive mt15 email-table">
                        
                        <table class="table table-bordered table-striped" id="emailsTable">
                            <thead>
                                <tr>
                                    <th>ALL
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" id="selectAll" type="checkbox">
                                        </div>
                                    </th>
                                    <th>@lang('Name')</th>

                                    <th>Job Title</th>
                                    <th>@lang('app.email')</th>
                                    <!--<th>Sent</th>
                                    <th>Read</th> -->
                                </tr>
                            </thead>



                            <tbody>
@foreach ($emails->where('email_sent', 0) as $email)
                                    <tr>
                                        <td>
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input emailCheckbox" type="checkbox" value="{{ $email->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $email->full_name ?? '' }}</td>
                                        <td>{{ $email->job_title ?? '' }}</td>
                                        <td>{{ $email->email ?? '' }}</td>
                                        <!--<td>{{ $email->email_sent == 1 ? 'Sent' : 'Not Sent' }}</td>
                                        <td>{{ $email->email_read == 1 ? 'Read' : 'Unread' }}</td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                           
                        </table>
                    </div>

                    @if($campaign->status == 1)
                        <div class="text-center mt-2">
                            <button class="btn btn-primary" type="button" id="sendEmailButton">Send Email</button>
                        </div>
                    @endif
                    
                 

                    <!-- Loader -->

                    <div id="loader">

                        <div class="spinner-border text-light" role="status">

                            <span class="sr-only">Loading...</span>

                        </div>

                        <p>Sending emails...</p>

                    </div>
                </div>

                <!-- Sent Emails Section -->
                <div id="sentEmailsDiv" style="display:none;">
 					
					<div class="row">
						<div class="col-md-7">
							<h4>Sent Emails</h4>
						</div>
						

						<div class="col-md-5">  
                            <div class="statsshow">
                                <span class="badge badge-success"><strong>Sent:</strong> {{ $sentCount ?? '0'}}</span>
                                <span class="badge badge-warning"><strong>Read:</strong> {{ $readCount ?? '0'}}</span>
                                <span class="badge badge-secondary"><strong>Unread:</strong> {{ $unreadCount ?? '0'}}</span>
                                <span class="badge badge-danger"><strong>Bounce:</strong> {{ $bounceCount ?? '0'}}</span> 
                            </div>
						</div>
					</div>
					
                    <div class="table-responsive mt15">
                        <table class="table table-bordered table-striped" id="sentEmailsTabletable">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>Job Title</th>
                                    <th>@lang('app.email')</th>
                                    <!--<th>Sent</th> -->
                                    <th>Read</th>
                                                     <th>Sent Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emails->where('email_sent', 1) as $email)
                                    <tr>
                                        <td>{{ $email->full_name ?? '' }}</td>
                                        <td>{{ $email->job_title ?? '' }}</td>
                                        <td>{{ $email->email ?? '' }}</td>
                                       <!-- <td>Sent</td> -->
                                        <td>{{ $email->email_read == 1 ? 'Read' : 'Unread' }}</td>
                                        
                             <td>
           <!--   {{ $email->id }} | {{ $email->campaign_id }}  -->

            @php
                $queue = DB::table('campaign_email_queue')
                    ->where('campaign_id', $email->campaign_id)
                    ->where('applicant_id', $email->id)
                    ->first();
            @endphp

            @if($queue)
                {{ \Carbon\Carbon::parse($queue->created_at)->format('jS M, Y g:i A') }}
            @else
                Not Found
            @endif
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
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function () {
    // ==============================
    // Initialize Dropify
    // ==============================
    $('.dropify').dropify({
        messages: {
            default: "{{ __('app.dragDrop') }}",
            replace: "{{ __('app.dragDropReplace') }}",
            remove: "{{ __('app.remove') }}",
            error: "{{ __('app.largeFile') }}"
        }
    });

    // ==============================
    // Initialize DataTables
    // ==============================
 $('#emailsTable, #sentEmailsTabletable').DataTable({
    pageLength: 250, // default entries shown
    lengthMenu: [ [50, 100, 250, 500, -1], [50, 100, 250, 500] ] // dropdown options
});


    // ==============================
    // Select All checkbox
    // ==============================
    $('#selectAll').on('change', function () {
        $('.emailCheckbox').prop('checked', this.checked);
    });

    // ==============================
    // Show/Hide Divs
    // ==============================
    $('#showAllEmails').on('click', function () {
        $('#allEmailsDiv').show();
        $('#sentEmailsDiv').hide();
		
		$('#showAllEmails').addClass("active2");
		$('#showSentEmails').removeClass("active2");
     });

    $('#showSentEmails').on('click', function () {
        $('#allEmailsDiv').hide();
        $('#sentEmailsDiv').show();
		
		$('#showAllEmails').removeClass("active2");
		$('#showSentEmails').addClass("active2");
    });

    // ==============================
    // Collapse toggle (safe check)
    // ==============================
    const toggleBtn = document.querySelector("[data-bs-target='#emailsSection']");
    const emailsSection = document.getElementById("emailsSection");

    if (toggleBtn && emailsSection) {
        emailsSection.addEventListener("show.bs.collapse", function () {
            toggleBtn.textContent = "Hide";
        });

        emailsSection.addEventListener("hide.bs.collapse", function () {
            toggleBtn.textContent = "View More";
        });
    }

    // ==============================
    // Send Email Button click
    // ==============================
    $('#sendEmailButton').on('click', function () {
        const $btn = $(this);
        const selectedIds = $('.emailCheckbox:checked').map(function () {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert("Please select at least one person to send email.");
            return;
        }

        // Prevent multiple clicks
        if ($btn.data('sending')) {
            return; // Already sending
        }
        $btn.data('sending', true).prop('disabled', true).text('Sending...');

        // Show loader
        $('#loader').css('display', 'flex');

        const requestData = {
            _token: '{{ csrf_token() }}',
            campaign_id: '{{ $campaign->id }}',
            template_id: '{{ $campaign->template_id }}',
            bcc: '{{ $campaign->bcc }}',
            cc: '{{ $campaign->cc }}',
            selected_ids: selectedIds
        };

        $.ajax({
            url: window.location.origin + "/custom_apis/SendMail.php",
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function (response) {
                $('#loader').hide();
                alert('Emails have been sent successfully!');
                location.reload();
            },
            error: function () {
                $('#loader').hide();
                alert('Emails have been sent successfully!');
                // Re-enable button on failure
                $btn.data('sending', false).prop('disabled', false).text('Send Email');
            }
        });
    });
});
</script>


@endpush
