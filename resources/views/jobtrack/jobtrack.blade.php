@extends('layouts.front')

@section('header-text')
    <h1 class="text-white fs-40 mb-10">Track Application Status</h1>
@endsection

@section('content')
<section class="section pb-40">
    <div class="container">
        <div class="row">

            <div class="col-lg-9 col-md-7">
                <div class="bg-white p-15">
                    <div class="col-md-12">

                        <h1>Track Application Status</h1>

                        <!-- Form to enter email and select job -->
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="Enter applicant's email" required>
                        </div>

                        <div class="form-group">
                            <label for="job">Select Job:</label>
                            <select id="job" name="job" required>
                                <option value="">--Select Job--</option>
                                <option value="1">Software Developer</option>
                                <option value="2">Project Manager</option>
                                <option value="3">Data Analyst</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="button" onclick="trackApplication()">Submit</button>
                        </div>

                        <!-- Static result box -->
                        <div class="result-box">
                            <h2>Application Details</h2>
                            <table>
                                <tr>
                                    <th>Candidate</th>
                                    <td>
                                        Name: John Doe <br>
                                        Email: johndoe@example.com <br>
                                        Phone: +1234567890
                                    </td>
                                </tr>
                                <tr>
                                    <th>Application Status</th>
                                    <td>Under Review</td>
                                </tr>
                                <tr>
                                    <th>Last Modified</th>
                                    <td>2025-01-06 14:30:00</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>Applicant's resume is under review. Awaiting interview scheduling.</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <script>
                        function trackApplication() {
                            // Simulate submission and show result.
                            alert("Form submitted. Displaying application status...");

                            // Normally here you would make an AJAX call to fetch data from the server.
                            // For now, this is static data.
                        }
                    </script>
                </div>
            </div>

            <div class="col-lg-3 col-md-5">
                <div class="sidebar bg-white">
                    <div class="p-30 bb-1">
                        <p class="fw-900 fs-16 text-dark mb-0">Job Application Tracking</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
