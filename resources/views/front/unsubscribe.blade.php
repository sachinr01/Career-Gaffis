@extends('layouts.front')

@section('header-text')
<h1 class="hidden-sm-down text-white fs-40 mb-10">Unsubscribe</h1>
<h3 class="hidden-sm-up text-white mb-10"></h3>
<div class="text-white">
    <a class="text-white" href=""><u></u>&nbsp; </a> &raquo; &nbsp;<span class="text-white"></span>
</div>
@endsection
<style>.unsubscribe-container {
    background-color: #fff;
    border: 0.8px solid #d6d4d4;
    border-radius: 10px;
    font-size: 14px;
    line-height: 26px;
    margin: 25px auto;
    padding: 45px 60px;
    max-width: 800px;
    text-align: center;
}
hr{
margin:0!important;
margin-top:15!important;
}
a{
    display:none!important;
}


section{
    background-color:#efeaea!important;
}

.footerlinks2{
        display:none!important;

}
.candidate-email {
    display: inline-block;
    font-size: 24px;
    font-weight: 700;
    line-height: 31px;
    text-align: center;
}

.candidate-text {
    font-size: 18px;
    line-height: 26px;
    margin: 0;
    padding: 0;
    text-align: center;
}

.unsubscribe-title {
    color: #ff3300;
    font-size: 36px;
    font-weight: 700;
    margin: 24px 0;
    text-align: center;
}

.unsubscribe-description {
    margin-bottom: 30px;
    font-size: 18px;
}

.unsubscribe-form-wrap {
    line-height: 1.5;
    text-align: center;
}

.unsubscribe-form {
    max-width: 500px;
    margin: 0 auto;
}

.btn-unsubscribe {
    background-color: #00aef0;
    border-color: #00aef0;
    color: #fff;
    font-weight: bold;
    padding: 10px 25px;
    border-radius: 6px;
    font-size: 20px;
}

.btn-unsubscribe:hover {
    background-color: #0095d3;
    border-color: #0095d3;
}

/* Hide elements on unsubscribe page */
.header.inner-header, 
.footerinfo2, 
.track-box {
    display: none !important;
}

.header.inner-header {
    height: 274px;
}

.header {
    padding: 104px 100px !important;
}

@media (max-width: 767px) {
    .header.inner-header {
        height: 224px;
    }
}
</style>
@section('content')
<section class="section bg-white py-60 min-height-section">
    <div class="container unsubscribe-container">
        <div class="unsubscribe-info">
            <h3><strong class="candidate-email">{{ $candidateEmail }}</strong></h3>
            <p class="candidate-text">is subscribed to our mailing list(s).</p>
        </div>
        <hr>
        <div class="unsubscribe-form-wrap">
            <h2 class="unsubscribe-title">Unsubscribe from our mailing list</h2>
            <p class="unsubscribe-description">To help us improve our services, we would be grateful if you could tell us why:</p>

            <form method="POST" action="{{ route('jobs.unsubscribe.submit') }}" class="unsubscribe-form">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidateId }}">
                <input type="hidden" name="candidate_email" value="{{ $candidateEmail }}">
                <input type="hidden" name="campaign_email_id" value="{{ $campaignEmailId }}">

                <div class="form-group mb-3">
                    <select name="unsubscribed_reason" id="unsubscribed_reason" class="form-control" required onchange="toggleCommentBox(this.value)">
                        <option disabled selected value="">Select a reason</option>
                        <option>Too many emails</option>
                        <option>Irrelevant content</option>
                        <option>No longer relevant</option>
                        <option>Emails weren't loading correctly</option>
                        <option>Don't remember subscribing</option>
                        <option>It looks like spam</option>
                        <option>Repetitive content</option>
                        <option>Doesn't want promotional emails</option>
                        <option>Irrelevant emails</option>
                        <option>Privacy concerns</option>
                        <option>Subscriber disengagement</option>
                        <option>Too frequent newsletters</option>
                        <option>You are selling too much</option>
                        <option>You're content is too boring</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group mb-4" id="commentBox" style="display: none;">
                    <textarea name="unsubscribed_comment" class="form-control" rows="4" placeholder="Provide your reason here..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Unsubscribe</button>
            </form>
        </div>
    </div>
</section>
<script>
    function toggleCommentBox(value) {
        const commentBox = document.getElementById('commentBox');
        commentBox.style.display = value === 'Other' ? 'block' : 'none';
    }
</script>
@endsection

@push('scripts')

@endpush
