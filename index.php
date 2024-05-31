<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerCloud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">CareerCloud</h1>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Categories</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="search-section">
        <div class="container">
            <h2>Find your dream job & make your goal.</h2>
            <p>We are the best global job finder agency and millions of people have used and trusted our platform.</p>
            <form id="job-search-form" method="GET" action="">
                <input type="text" id="title" name="title" placeholder="Enter your keyword or job" required>
                <input type="text" id="location" name="location" placeholder="Location" required>
                <button type="submit" name="search_type" value="title">Search</button>
            </form>
            <div id="loader" class="loader" style="display: none;"></div>
        </div>
    </section>

    <div id="job-listings" class="container">
        <?php
        if (isset($_GET['search_type']) && $_GET['search_type'] == 'title' && isset($_GET['title']) && isset($_GET['location'])) {
            echo '<script>document.getElementById("loader").style.display = "block";</script>';
            $title = urlencode($_GET['title']);
            $location = urlencode($_GET['location']);
            $query = $title . "%20in%20" . $location;

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://jsearch.p.rapidapi.com/search?query=$query&page=1&num_pages=1",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "X-RapidAPI-Host: jsearch.p.rapidapi.com",
                    "X-RapidAPI-Key: 25a8302908mshfea8c1eab20a400p1b2494jsnbf4ea76642f0"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            echo '<script>document.getElementById("loader").style.display = "none";</script>';

            if ($err) {
                echo "<p>Error fetching job listings: " . $err . "</p>";
            } else {
                $jobs = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "<p>Error decoding JSON response: " . json_last_error_msg() . "</p>";
                } elseif ($jobs && isset($jobs['data']) && count($jobs['data']) > 0) {
                    foreach ($jobs['data'] as $offer) {
                        echo "<div class='job'>";
                        echo "<div class='job-details'>";
                        echo "<img src='" . htmlspecialchars($offer['employer_logo']) . "' alt='Company Logo'>";
                        echo "<div class='job-info'>";
                        echo "<h2>" . htmlspecialchars($offer['job_title']) . "</h2>";
                        echo "<p><strong>Company:</strong> " . htmlspecialchars($offer['employer_name']) . "</p>";
                        echo "<p><strong>Location:</strong> " . htmlspecialchars($offer['job_city']) . ", " . htmlspecialchars($offer['job_state']) . ", " . htmlspecialchars($offer['job_country']) . "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "<a href='" . htmlspecialchars($offer['job_apply_link']) . "' target='_blank'>View job details</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No job listings found.</p>";
                }
            }
        } else {
            echo "<p>Please provide a job title and location for the search.</p>";
        }
        ?>
    </div>

    <script>
        document.getElementById('job-search-form').addEventListener('submit', function() {
            document.getElementById('loader').style.display = 'block';
        });
    </script>
</body>
</html>
