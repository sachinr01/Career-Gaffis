@extends('layouts.front')

@section('content')

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

site-footer{
    display:none!important;
}

@media (max-width: 767px) {
    .header.inner-header {
        height: 224px;
    }
}
</style>
<section class="section bg-white min-height-section">
    <div class="container unsubscribe-container">
    <h2 class="unsubscribe-success-title">Done!</h2>
    <hr class="unsubscribe-divider">
    <h4 class="unsubscribe-email">
    <strong>{{ session('unsubscribed_data.candidate_email') }}</strong>
</h4>

    <p class="unsubscribe-message lead">We have received your request and will remove your email from our database within 24 hours. Thank you.</p>
  </div>
</section>
@endsection
