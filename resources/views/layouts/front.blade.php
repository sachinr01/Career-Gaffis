<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"            content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!--  <meta name="description"         content="{{ !empty($metaDescription) ? $metaDescription : '' }}"> -->
      <meta name="description" content="Explore exciting career opportunities at Gaffis. Join our team and grow your career with amazing projects and internships. Apply today!">


    <meta property="og:url"          content="{{ !empty($pageUrl) ? $pageUrl : '' }}" />
    <meta property="og:type"         content="website" />
    <meta property="og:title"        content="{{ !empty($metaTitle) ? $metaTitle : '' }}" />
    <meta property="og:description"  content="{{ !empty($metaDescription) ? $metaDescription : '' }}" />
    <meta property="og:image"        content="{{ !empty($metaImage) ? $metaImage : '' }}" />
    <meta property="og:image:width"  content="600" />
    <meta property="og:image:height" content="600" />

    <meta itemprop="name"            content="{{ !empty($metaTitle) ? $metaTitle : '' }}">
    <meta itemprop="description"     content="{{ !empty($metaDescription) ? $metaDescription : '' }}">
    <meta itemprop="image"           content="{{ !empty($metaImage) ? $metaImage : '' }}"> 
    
  <!--  <title>{{ $pageTitle }}</title> -->
      <title>Careers at Gaffis – Join Our Team</title>

    <style>
        :root {
            --main-color: {{ $frontTheme->primary_color }};
        }

        {!! $frontTheme->front_custom_css !!}
        /* .bg-img-shape{
  padding-left: 20px; 
  
} */

@media only screen and (max-width:767px){
header .location-search {
  display: none !important;
}
.top-left1 {
  display: none;
}
.top-right1 {
  display: none;
}
.logo-inverse{
	margin-top: 20px;
}	
}

@media only screen and (max-width:480px){
.header.inner-header {
    padding: 170px 0 40px !important;
	height: 400px !important;
  }
  .logo-inverse{
	margin-top: 50px;
}
}
  
  
.cstslug2 {
display: none;
}
.track_output ul {
list-style: none;
text-align: left;
}
.track_output label {
font-weight: bold;
font-size: 16px;
etter-spacing: 1px;
} 
.track_output {
font-size: 16px;
padding: 20px 0px;
letter-spacing: 1px;
}
.track_output_no_resut {
margin-bottom: 20px;
font-size: 15px;
color: red;
}
.label2 {
display: block;
font-weight: bold;
}
#btn_get_status {
margin: 27px 0px 10px 0px;
text-align: center;
white-space: nowrap;
vertical-align: middle;
color: #fff;
padding: 6px 10px;
font-size: 15px;
line-height: 1.5;
background: #535353;
border: 1px solid #535353;
border-radius: .25rem;
width: 100%;
font-weight: bold;
letter-spacing: 2px;
}
.track_activity_cls li {
list-style: disc;
text-align: left;
}
.track_activity_cls span {
font-size: 13px;
display: block;
font-style: italic;
}
.track-subtitle {
font-size: 13px;
margin-bottom: 15px;
letter-spacing: 1px;

font-size: 12px;
  color: #bb273f;
  font-weight: 600;
  
}
.footerinfo2{ 
margin-bottom: 40px;
font-size: 14px;
line-height: 20px;
border: 2px solid var(--main-color);
padding: 10px;
border-radius: 4px;
color: var(--main-color);
text-align: left;
}
.footerinfo2 ul {
margin-bottom: 0px;
padding: 15px 20px;
}
</style>


    <!-- Styles -->
    <link href="{{ asset('froiden-helper/helper.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet">

    <link href="{{ asset('front/assets/css/core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/thesaas.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/custom.css') }}" rel="stylesheet">
    @stack('header-css')
    <link rel='stylesheet prefetch'
    href='//cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    @stack('style')
</head>

<body>


<!-- Topbar -->
<nav class="topbar topbar-inverse topbar-expand-md">
    <div class="container">
		
		<div class="top-left1"><a href="mailto:helpdesk@gaffis.com">helpdesk@gaffis.com</a></div>
		
        <div class="col-sm-4 text-center"> <a class="topbar-brand" href="{{ url('/') }}">
                <img src="{{ $global->logo_url }}" class="logo-inverse" alt="home" />
            </a></div>
		
		<div class="top-right1"><a href="https://gaffis.com/">Back to Main Site</a></div>
		
    </div>
</nav>
<!-- END Topbar -->

<!-- Header -->
<header class="bg-img-shape">
        
 		<div class="header inner-header" style="background-image: url({{ $frontTheme->background_image_url }})" data-overlay="8">
	 
			<div class="container text-center titlehead">
				
				 <div class="row">
					<div class="col-12 col-lg-8 offset-lg-2">

						@yield('header-text')

					</div>
				</div>
	 
			</div>
		</div>
</header>
<!-- END Header -->

<!-- Main container -->
<main class="main-content">

    @yield('content')

</main>
<!-- END Main container -->

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row text-center">
            <div class="col-12 col-lg-12 mb-10 cstslug2">
               @forelse($customPages as $customPage)
                   <a class="px-5 fw-400" href="/{{ $customPage->slug }}"><span>{{ ($customPage->name) }}</span></a>
               @empty
               @endforelse
			</div>
		
			 
			
			<div class="col-12 col-md-12 col-lg-12" id="track" style="padding-bottom: 30px;display: block;">
				<div class="track-box">
 					<header class="section-header" style="margin-bottom: 0px !important; margin-top: 0px; ">
						<h2>Track Your Application Status</h2> <hr>
					</header>

					<div class="track-subtitle">If you have already applied, you can track the status of your application here.</div> 
					
 					<div class="row">
						<div class="col-2 col-lg-2 fw-400">
						</div>
						<div class="col-3 col-lg-3 fw-400">
							<div class="form-group">
								<label for="email" class="label2">Email:</label>
								<input type="email" id="email" name="email" placeholder="Enter applicant's email" required="" style="width: 100%;height: 36px;">
							</div>
						</div>
						<div class="col-3 col-lg-3 fw-400">
							<div class="form-group">
								<label for="job_id" class="label2">Select Job:</label>
								<select id="job_id" name="job_id" required=""  style="width: 100%;height: 36px;">
									<option value="">--Select Job--</option>
									<option value="6">Angular JS Developer</option>
<option value="21">Automation Quality Assurance Engineer</option>
<option value="4">Business Development Executive</option>
<option value="3">Digital Marketing Expert  (Junior)</option>
<option value="7">Flutter Developer</option>
<option value="24">HTML Designer (Urgent)</option>
<option value="11">Human Resource Executive</option>
<option value="19">Internship - Digital Marketing</option>
<option value="18">Internship - Engineering Graduates</option>
<option value="15">Internship - PHP/Full Stack Developer</option>
<option value="26">Internship- Business Development Executive</option>
<option value="27">Internship/Training Power BI</option>
<option value="12">Java Developer</option>
<option value="1">Jr PHP Developer</option>
<option value="8">Laravel Developer</option>
<option value="22">Lead Automation Quality Assurance Engineer Experie...</option>
<option value="10">Memory Layout Engineer</option>
<option value="14">Physical Design Engineer</option>
<option value="13">Python Developer</option>
<option value="5">React JS Developer</option>
<option value="23">Senior Digital Marketing Expert</option>
<option value="20">Senior Dot Net Developer</option>
<option value="16">SEO Executive Fresher/Intern</option>
<option value="2">Sr PHP Developer</option>
<option value="17">Web Support Executive Cum Accountant</option>
<option value="9">WordPress Developer</option>
 								</select>
							</div>
						</div>
						<div class="col-2 col-lg-2 fw-400">
							<div class="form-group">
								 <button type="button"  id="btn_get_status" name="btn_get_status">Submit</button>
							</div>
						</div>
 					</div>
					<div class="row">
						<div class="col-2 col-lg-2 fw-400">&nbsp;</div>
						<div class="col-8 col-lg-8 fw-400" id="applicant_track_success">
						</div>
					</div>
				
				</div>
			</div>
		
		 
			
            <div class="col-12 col-lg-12 fw-400">
 				 
				<div class="footerinfo2">
					<ul>
						<li>Please do not contact us via phone or WhatsApp. </li>
						<li>For any inquiries, please send your resumes, queries, and feedback to <a href="mailto: helpdesk@gaffis.com">helpdesk@gaffis.com</a>. </li>
 					</ul>
 				</div>
  				
				<div class="footerlinks2"><a href="https://gaffis.com/">Back to Main Site</a> | <a href="https://gaffis.com/about-us/">About Us</a> | <a href="https://gaffis.com/services/">Services</a> | <a href="https://gaffis.com/blog/">Blog</a> | <a href="https://gaffis.com/contact-us/">Contact Us</a></div>
				<p>&copy; {{ \Carbon\Carbon::today()->year }} @lang('app.by') {{ $companyName }}. All rights reserved.</p>
            </div>
         </div>
    </div>
</footer>
<!-- END Footer -->



<!--START of Tawk.to Script-->
<script type="text/javascript">
var $_Tawk_API={},$_Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55a8ca9084d307454c02fc2d/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--END of Tawk.to Script-->




<!-- Scripts -->
<script src="{{ asset('front/assets/js/core.min.js') }}"></script>
<script src="{{ asset('front/assets/js/thesaas.min.js') }}"></script>
<script src="{{ asset('front/assets/js/script.js') }}"></script>
<script src="{{ asset('front/assets/js/select2.min.js') }}"></script>
<script src="{{ asset('froiden-helper/helper.js') }}"></script>
<script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
<script>
    setActiveClassToLanguage();
    $('.language-drop .dropdown-item').click(function () {
        let code = $(this).data('lang-code');

        let url = '{{ route('jobs.changeLanguage', ':code') }}';
        url = url.replace(':code', code);

        if (!$(this).hasClass('active')) {
            $.easyAjax({
                url: url,
                type: 'POST',
                container: 'body',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                        setActiveClassToLanguage();
                    }
                }
            })
        }
    })
    function setActiveClassToLanguage() {
        // language switcher
        if ('{{ \Cookie::has('language_code') }}') {
            $('.language-drop .dropdown-item').filter(function () {
                return $(this).data('lang-code') === '{{ \Cookie::get('language_code') }}'
            }).addClass('active');
        }
        else {
            $('.language-drop .dropdown-item').filter(function () {
                return $(this).data('lang-code') === '{{ $global->locale }}'
            }).addClass('active');
        }
    }
</script>


@stack('footer-script')


<script>
			
			$('#btn_get_status').click(function () {
				
				//alert('working here');
      
		
		var url = "https://careers.gaffis.com/custom_apis/track_status.php";

        var job_id = $('#job_id').val();
        var email = $('#email').val();
         //var application_notify = 'yes';
        var token = '{{ csrf_token() }}';
		
		if(email==""){
			alert('Please enter valid email address.');
			return false;
		}
 		if(job_id==""){
			alert('Please select job.');
			return false;
		}
 		
		
 		//alert(job_id + ' - ' + email + ' - ' + url );
 		 
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token':token, 'email':email, 'job_id': job_id},
            success: function (response) {
                if(response.status == 'success') {
                    $('#applicant_track_success').html(response.view);
                 }
            }
        });
		 
	 
		
    });
	</script>
	
	
</body>
</html>