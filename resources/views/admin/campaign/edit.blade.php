@extends('layouts.app')







@push('head-script')
    <style>
        .field-icon {



            float: right;



            cursor: pointer;



            margin-left: -25px;



            margin-top: -25px;



            position: relative;



            z-index: 2;



            margin-right: 7px;



        }
    </style>



    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush







@section('content')
    {!! $smtpSetting->set_smtp_message !!}







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
		
		.showcampsection2 {
			margin: 0px 10px;
		}
    </style>



    <div class="row showcampsection2">



        <div class="col-12">



            <div class="card">



                <div class="card-body">



                    <h4 class="card-title">Edit Campaign</h4>







                    <!-- Progress Indicator -->



                    <div class="progress-indicator d-flex justify-content-between mb-4">



                        <div class="step-indicator p-3 bg-light text-secondary active rounded-circle" id="indicator-step1">1

                        </div>



                        <div class="step-indicator p-3 bg-light text-secondary rounded-circle" id="indicator-step2">2</div>



                        <div class="step-indicator p-3 bg-light text-secondary rounded-circle" id="indicator-step3">3</div>



                    </div>







                    <form id="createCampaignForm" class="ajax-form" action="{{ route('campaign.data.updateCampaign', $campaign->id) }}" 
                        method="POST">

                        @csrf



                        <!-- Step 1: Create Campaign -->



                        <div id="step1" class="step active">



                            <h5>Step 1</h5>



                            <div class="form-row">



                                <div class="form-group col-md-6">



                                    <label for="campaignName">Campaign Name</label>



                               <input type="text" class="form-control" id="campaignName" name="campaignName" required
       value="{{ old('campaignName', $campaign->name ?? '') }}">



                                </div>







                                <div class="form-group col-md-6">



                                    <label for="description">Description</label>



                           <textarea class="form-control" id="description" name="description" required>{{ old('description', $campaign->description ?? '') }}</textarea>




                                </div>



                            </div>



                            <div class="form-group">

                                <label for="subject">Subject</label>

                            <input type="text" class="form-control" id="subject" name="subject" required
       value="{{ old('subject', $campaign->subject ?? '') }}">

                            </div>



                            <div class="form-group">



                                <label for="campaignCC">Campaign CC</label>



                     <input type="text" class="form-control" id="campaignCC" name="campaignCC" required
       value="{{ old('campaignCC', $campaign->cc ?? '') }}">



                            </div>





                            <div class="form-group">



                                <label for="activeStatus">Active Status</label>



   <select class="form-control" id="activeStatus" name="activeStatus">
    <option value="">Select Status</option>
    <option value="active" {{ (isset($campaign) && $campaign->status == 1) ? 'selected' : '' }}>Active</option>
    <option value="inactive" {{ (isset($campaign) && $campaign->status == 0) ? 'selected' : '' }}>Inactive</option>
</select>




                            </div>



                            <button type="button" class="btn btn-primary next-step">@lang('Next')</button>



                        </div>







                        <!-- Step 2: Email Template -->



                        <div id="step2" class="step">



                            <h5>Step 2</h5>



                            <div class="form-group">

                                <label for="emailTemplate">@lang('Email Template')</label>



                          
        @foreach ($templates as $template)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="template_id"
                    value="{{ $template->id }}" id="template{{ $template->id }}"
                    {{ (isset($campaign) && $campaign->template_id == $template->id) ? 'checked' : '' }}>

                <label class="form-check-label" for="template{{ $template->id }}">
                    {{ $template->title }}
                </label>
            </div>
        @endforeach

                            </div>





                            <button type="button" class="btn btn-secondary prev-step">@lang('Previous')</button>



                            <button type="button" class="btn btn-primary next-step">@lang('Next')</button>



                        </div>







                        <!-- Step 3: Select Audience -->



                        <div id="step3" class="step">
                              <h5>@lang('Step 3')</h5>
    <h4>Emails Associated With This Campaign</h4>

                    <div class="table-responsive email-table">

                        <table class="table table-bordered table-striped" id="emailsTable">

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>@lang('Name')</th>

                                    <th>@lang('app.email')</th>

                                    <th>Email Sent status</th>

                                    <th>Email Read status</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($emails as $key => $email)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $email->full_name ?? '' }}</td>
                                        <td>{{ $email->email ?? '' }}</td>
                                        <td>{{ isset($email->email_sent) && $email->email_sent == 1 ? 'Sent' : 'Not Sent' }}
                                        </td>
                                        <td>{{ isset($email->email_read) && $email->email_read == 1 ? 'Read' : 'Unread' }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>
<div class="d-flex gap-3 mb-3">
    <button type="button" class="btn btn-secondary prev-step previouslastend">
        @lang('Previous')
    </button>  

    <button type="button" id="toggleBtn" class="btn btn-primary ml-2">
        Add More Candidates
    </button>

    <button type="button" id="saveCloseBtn1" class="btn btn-success ml-2">
        Save & Close
    </button>
</div>

<div id="emailsSection" style="display: none;">
                            <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="selectJobs">@lang('Select Jobs')</label>
            <select class="form-control selectpicker" id="selectJobs" name="selectJobs[]" multiple>
                @foreach ($jobs as $job)
                    <option value="{{ $job->id }}"
                        {{ (isset($selectedJobs) && in_array($job->id, $selectedJobs)) ? 'selected' : '' }}>
                        {{ $job->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="selectStatus">@lang('Select Status')</label>
            <select class="form-control selectpicker" id="selectStatus" name="selectStatus[]" multiple>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}"
                        {{ (isset($selectedStatuses) && in_array((string)$status->id, array_map('strval', $selectedStatuses))) ? 'selected' : '' }}>
                        {{ $status->status }} 
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>


                            <div class="form-group" id="candidateList">



                                <!-- Candidate List Table -->



                                <table class="table" id="candidatesTable">



                                    <thead>



                                        <tr>



                                            <th>



                                                <div class="checkbox form-check">

                                                    <input id="selectAllCheckbox" type="checkbox" class="form-check-input">

                                                    <label for="selectAllCheckbox"></label>

                                                </div>







                                            </th>







                                            <th>@lang('ID')</th>



                                            <th>@lang('Full Name')</th>



                                            <th>@lang('Email')</th>



                                            <th>@lang('Phone')</th>



                                            <th>@lang('Gender')</th>



                                            <th>@lang('Date of Birth')</th>



                                        </tr>



                                    </thead>



                                    <tbody id="candidateTableBody">



                                        <!-- Candidate rows will be inserted here by JavaScript -->



                                    </tbody>



                                </table>



                            </div>





                            <input type="hidden" id="emailInputField" name="email[]">



                         <div class="d-flex mb-4">
    <button type="button" class="btn btn-secondary prev-step originallastprevious">
        @lang('Previous')
    </button>  

    <button type="button" id="createCampaignFormbtn" class="btn btn-primary next-step ml-2">
        @lang('Add')
    </button>
</div>


<!-- Space before Save & Close -->
<div class="my-4"></div>

<!-- Center Save & Close -->
<div class="text-center">
    <button type="button" id="saveCloseBtn" class="btn btn-success">
        Save & Close
    </button>
</div>







</div>


                        </div>















                    </form>







                </div>



            </div>



        </div>



    </div>
    
@endsection







@push('footer-script')
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>

<script>
    const selectedEmails = @json($selectedEmails).map(String);
    const selectedSet = new Set(selectedEmails);
    console.log('Selected Emails:', selectedEmails);
</script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {





            let currentStep = 1;

            const totalSteps = 3;



            function updateProgressIndicator() {

                for (let i = 1; i <= totalSteps; i++) {

                    const indicator = document.getElementById(`indicator-step${i}`);

                    indicator.classList.remove('active', 'completed');

                    if (i < currentStep) {

                        indicator.classList.add('completed');

                    } else if (i === currentStep) {

                        indicator.classList.add('active');

                    }

                }

            }



            document.querySelectorAll('.next-step').forEach(button => {

                button.addEventListener('click', function() {

                    if (currentStep < totalSteps) {

                        document.getElementById(`step${currentStep}`).style.display = 'none';

                        currentStep++;

                        document.getElementById(`step${currentStep}`).style.display = 'block';

                        updateProgressIndicator();

                    } else {

                        // Directly call the form submission handler instead of triggering click

                        handleFormSubmission();

                    }

                });

            });



            document.querySelectorAll('.prev-step').forEach(button => {

                button.addEventListener('click', function() {

                    if (currentStep > 1) {

                        document.getElementById(`step${currentStep}`).style.display = 'none';

                        currentStep--;

                        document.getElementById(`step${currentStep}`).style.display = 'block';

                        updateProgressIndicator();

                    }

                });

            });
            
            
             document.getElementById('saveCloseBtn').addEventListener('click', function () {
        window.location.href = "{{ route('admin.campaign.index') }}";
    });



            function handleFormSubmission() {

                const form = document.getElementById('createCampaignForm');

                let formData = new FormData(form);



                // Validate campaignName

                let campaignName = formData.get('campaignName');

                if (!campaignName || campaignName.trim() === '') {

                    alert('Campaign Name is required.');

                    return;

                }
             


                // Validate description

                let description = formData.get('description');

                if (!description || description.trim() === '') {

                    alert('Description is required.');

                    return;

                }



                // Validate activeStatus

                let activeStatus = formData.get('activeStatus');

                if (!activeStatus || (activeStatus !== 'active' && activeStatus !== 'inactive')) {

                    alert('Active Status is required and should be either active or inactive.');

                    return;

                }



                // Validate selected emails

                let selectedEmails = [];

                document.querySelectorAll('.candidateCheckbox:checked').forEach(function(checkbox) {

                    let emailInput = checkbox.closest('tr').querySelector(

                        'input[type="hidden"][name="email[]"]');

                    if (emailInput) {

                        let emailValue = emailInput.value;

                        if (emailValue) {

                            selectedEmails.push(emailValue);

                        }

                    }

                });



                // if (selectedEmails.length === 0) {

                //     alert('At least one email must be selected.');

                //     return;

                // }



                // Append selected emails as a JSON string to the form data

                formData.append('selected_emails', JSON.stringify(selectedEmails));



                // Send AJAX request to store the form data

               formData.append('_method', 'PUT');
               

fetch("{{ route('campaign.data.updateCampaign', $campaign->id) }}", {
    method: 'POST',

                        body: formData,

                    })

                    .then(response => response.json())

                    .then(data => {
    if (data.status === 'success') {
        // Clear old table rows
        const tbody = document.querySelector("#emailsTable tbody");
        tbody.innerHTML = "";

        // Append new emails
        data.emails.forEach((email, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${email.full_name ?? ''}</td>
                    <td>${email.email ?? ''}</td>
                    <td>${email.email_sent == 1 ? 'Sent' : 'Not Sent'}</td>
                    <td>${email.email_read == 1 ? 'Read' : 'Unread'}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML("beforeend", row);
        });
      //  document.getElementById("createCampaignFormbtn").innerText = "Add More Candidate";

        alert("Campaign updated successfully!");
    } else {
        alert("An error occurred: " + (data.message || "Unknown error"));
    }
})


                    .catch(error => {

                        alert('An error occurred while creating the campaign.');

                    });

            }





            // Attach the submit handler to form submission

            document.getElementById('createCampaignForm').addEventListener('submit', function(e) {

                e.preventDefault(); // Prevent default form submission

                handleFormSubmission(); // Call the form submission function

            });



            // Initialize the progress indicator

            updateProgressIndicator();

        });







        // Handle dynamic changes in job or status selection
function fetchFilteredCandidates() {
    let jobId = Array.from(document.getElementById('selectJobs').selectedOptions).map(option => option.value);
    let statusId = Array.from(document.getElementById('selectStatus').selectedOptions).map(option => option.value);

    $('#candidateTableBody').empty();

    if (jobId.length === 0 || statusId.length === 0) {
        return;
    }

    $.ajax({
        url: "{{ route('campaign.data.job_Appilcation_data_filter') }}",
        type: 'GET',
        data: {
            job_id: jobId,
            status_id: statusId
        },
        success: function(response) {
            $('#candidateTableBody').empty();

            if ($.fn.DataTable.isDataTable('#candidatesTable')) {
                $('#candidatesTable').DataTable().clear().destroy();
            }

            if (response.data && response.data.length > 0) {
                response.data.forEach(function(candidate) {
                     const idStr = candidate.id.toString();

    // clear match check log
    if (selectedSet.has(idStr)) {
      console.log('✅ Matched:', idStr);
    } else {
      console.log('❌ Not matched:', idStr);
    }
 $('#candidateTableBody').append(`
            <tr>
                <td>
                    <div class="checkbox form-check">
                        <input id="candidate-${candidate.id}" type="checkbox"
                            class="form-check-input candidateCheckbox"
                            name="email[]"
                            value="${candidate.id}"
                            data-id="${candidate.id}"
                            ${selectedSet.has(candidate.id.toString()) ? 'checked' : ''}>
                        <label for="candidate-${candidate.id}"></label>
                    </div>
                </td>
                <td>${candidate.id || 'N/A'}</td>
                <td>${candidate.full_name || 'N/A'}</td>
                <td>${candidate.email || 'N/A'}</td>
                <td>${candidate.phone || 'N/A'}</td>
                <td>${candidate.gender || 'N/A'}</td>
                <td>${candidate.dob || 'N/A'}</td>
            </tr>
        `);

                });



// Initialize DataTable
let candidatesTable = $('#candidatesTable').DataTable({
    "paging": true,
    "pageLength": 50,
    "lengthChange": false,
    "info": true,
    "ordering": true,
    "searching": true,
});


            } else {
                $('#candidateTableBody').append('<tr><td colspan="7">No candidates found.</td></tr>');
            }
        },
        error: function() {
            alert('Error fetching candidates.');
        }
    });
}

// On select box change
document.onchange = function(event) {
    if (event.target && (event.target.id === 'selectJobs' || event.target.id === 'selectStatus')) {
        fetchFilteredCandidates();
    }
};

// On page load
window.onload = function() {
    fetchFilteredCandidates();
};




        // Handle 'Select All' candidates checkbox

        document.getElementById('selectAllCheckbox').onclick = function() {

            console

            let isChecked = this.checked;

            if (isChecked) {

                this.setAttribute('name', 'all');

            } else {

                this.removeAttribute('name');

            }



            // Check or uncheck all candidate checkboxes

            let candidateCheckboxes = document.querySelectorAll('.candidateCheckbox');

            candidateCheckboxes.forEach(function(checkbox) {

                checkbox.checked = isChecked;

            });

        };



        // Handle individual checkbox selection

       $(document).on('change', '.candidateCheckbox', function () {
    let isChecked = $(this).is(':checked');

    if (isChecked) {
        $(this).attr('name', 'email[]');
    } else {
        $(this).removeAttr('name');
    }

    // Collect selected IDs
    let selectedIds = [];
    $('.candidateCheckbox:checked').each(function () {
        selectedIds.push($(this).val());
    });

    console.log("Selected Candidate IDs:", selectedIds);

    // Build form data (reuse same campaign fields if needed)
    let formData = new FormData(document.getElementById('createCampaignForm'));
    formData.append('selected_emails', JSON.stringify(selectedIds));
    formData.append('_method', 'PUT');

    // Call same API route
    fetch("{{ route('campaign.data.updateCampaign', $campaign->id) }}", {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const tbody = document.querySelector("#emailsTable tbody");
                tbody.innerHTML = "";
                data.emails.forEach((email, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${email.full_name ?? ''}</td>
                            <td>${email.email ?? ''}</td>
                            <td>${email.email_sent == 1 ? 'Sent' : 'Not Sent'}</td>
                            <td>${email.email_read == 1 ? 'Read' : 'Unread'}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML("beforeend", row);
                });
                console.log("✅ Campaign updated via checkbox change");
            } else {
                console.warn("API error:", data.message || "Unknown error");
            }
        })
        .catch(err => console.error("API fetch error:", err));
});






        document.addEventListener('DOMContentLoaded', function() {

            // Handle 'Select All' candidates checkbox

            document.getElementById('selectAllCheckbox').addEventListener('change', function() {

                let isChecked = this.checked;



                // Get all individual candidate checkboxes and set their checked state

                const checkboxes = document.querySelectorAll('.candidateCheckbox');

                checkboxes.forEach(function(checkbox) {

                    checkbox.checked = isChecked;

                });



                // Update the email array when the "Select All" checkbox is changed

                updateEmailArray();

            });



            // Onchange event listener for individual candidate checkboxes

            document.querySelectorAll('.candidateCheckbox').forEach(function(checkbox) {

                checkbox.addEventListener('change', function() {

                    // Check if all candidate checkboxes are selected

                    const allChecked = document.querySelectorAll('.candidateCheckbox:checked')

                        .length === document.querySelectorAll('.candidateCheckbox').length;



                    // Set the "Select All" checkbox state

                    document.getElementById('selectAllCheckbox').checked = allChecked;



                    // Update the email array when a checkbox is checked/unchecked

                    updateEmailArray();

                });

            });



            // Function to update the email array (selected candidate IDs) in the hidden input

            function updateEmailArray() {

                const checkedCheckboxes = document.querySelectorAll('.candidateCheckbox:checked');

                const emailArray = [];



                // Loop through the checked checkboxes and get their corresponding candidate IDs

                checkedCheckboxes.forEach(function(checkbox) {

                    let candidateId = checkbox.getAttribute('data-id');

                    emailArray.push(candidateId);

                });



                // Update the hidden input field with the selected email IDs

                const emailInput = document.getElementById('emailInputField');

                emailInput.value = emailArray.join(',');



                // Log the selected candidate IDs for debugging

                console.log("Selected email IDs: ", emailArray);

            }



            const breadcrumbItem = document.querySelector('.breadcrumb-item.active');

            if (breadcrumbItem) {

                breadcrumbItem.textContent = 'Campaign'; // Change the breadcrumb text to "Campaign"

            }



            // Update <h1> text

            const heading = document.querySelector('h1.mb-xs-2');

            if (heading) {

                heading.textContent = 'Campaign'; // Change the h1 text to "Campaign"

            }

        });
    </script>
    
<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("toggleBtn");
  const saveCloseBtn1 = document.getElementById("saveCloseBtn1");
  const emailsSection = document.getElementById("emailsSection");
  const prevBtn11 = document.querySelector(".previouslastend");

  toggleBtn.addEventListener("click", function () {
    emailsSection.style.display = "block"; // show section
    toggleBtn.style.display = "none";      // hide Add More Candidates button
    saveCloseBtn1.style.display = "none";  // hide Save & Close button
        prevBtn11.style.display = "none";        // hide Previous button

  });

  saveCloseBtn1.addEventListener("click", function () {
    window.location.href = "{{ route('admin.campaign.index') }}";
  });
});
</script>

@endpush
