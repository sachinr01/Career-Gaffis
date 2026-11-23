<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Application Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #218838;
        }

        .result-box {
            margin-top: 30px;
            border-top: 2px solid #ccc;
            padding-top: 20px;
        }

        .result-box h2 {
            text-align: center;
        }

        .result-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .result-box table th,
        .result-box table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .result-box table th {
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>

    <div class="container">
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

            // Normally here you would make an AJAX call to fetch data from server.
            // For now, this is static data.
        }
    </script>

</body>

</html>