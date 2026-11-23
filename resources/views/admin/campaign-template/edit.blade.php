@extends('layouts.app')

@section('content')
<div class="container">

	<div class="card">
                 
		<div class="card-body">
				
			<h4>Edit Email Template</h4>
			<form method="POST" action="{{ route('admin.campaign-template.update', $template->id) }}" enctype="multipart/form-data">
				@csrf
				@method('PUT')

			  
				<div class="form-group">
					<label for="title">Title</label>
					<input type="text" class="form-control" name="title" value="{{ $template->title }}" required>
				</div>
				<!-- <div class="form-group">
					<label for="slug">Slug</label>
					<input type="text" class="form-control" name="slug" value="{{ $template->slug }}" required>
				</div>
				-->
				<div class="form-group">
					<label for="preview">Preview Image </label>
					<input type="file" class="form-control" name="preview" value="{{ $template->preview }}">
				</div>
				 <div class="form-group">
					<label for="html">HTML Code</label>
					<textarea class="form-control" name="html" rows="6">{{ $template->html }}</textarea>
				</div>
				 <div class="form-group">
					<label for="css">CSS Code</label>
					<textarea class="form-control" name="css" rows="6">{{ $template->css }}</textarea>
				</div>
				<div>
					<ul>
						<li>
							<strong>Candidate Details Replacement in HTML Code</strong>
							<span style="display: none !important;">Table - job_applications</span>
							<ul>
								<li><strong>[APPLICANT_ID]</strong> — Use this for Candidate ID</li>
								<li><strong>[APPLICANT_NAME]</strong> — Use this for Candidate Name</li>
								<li><strong>[APPLICANT_EMAIL]</strong> — Use this for Candidate Email</li>
								<li><strong>[APPLICANT_PHONE]</strong> — Use this for Candidate Phone Number</li>
								<li><strong>[APPLICANT_GENDER]</strong> — Use this for Candidate Gender</li>
								<li><strong>[APPLICANT_STATUS]</strong> — Use this for Application Status</li>
							</ul>
						</li>
						<li>
							<strong>Job Details Replacement in HTML Code</strong>
							<span style="display: none !important;">Table - jobs</span>
							<ul>
								<li><strong>[JOB_ID]</strong> — Use this for Job ID</li>
								<li><strong>[JOB_TITLE]</strong> — Use this for Job Title</li>
								<li><strong>[JOB_DESCRIPTION]</strong> — Use this for Job Description</li>
								<li><strong>[JOB_REQUIREMENT]</strong> — Use this for Job Requirements</li>
								<li><strong>[JOB_HIGHLIGHT]</strong> — Use this for Job Highlights</li>
							</ul>
						</li>
					</ul>
				</div>

				<button type="submit" class="btn btn-primary">Update</button>
				<a href="{{ route('admin.campaign-template.index') }}" class="btn btn-secondary">Back</a>
			</form>
			
		</div>
		
	</div>
	
</div>
@endsection
