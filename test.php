<?php

$xml = simplexml_load_file("https://news.google.com/rss/search?q=family%2Blaw&hl=en-US&gl=US&ceid=US%3Aen"); 

if (!$xml) {
    die("Error: Cannot load XML file.");
}

$limit = 7; // <-- Change this to show latest X items
$count = 0;

echo "<h2>Legal News</h2>";
echo "<ul>";

foreach ($xml->channel->item as $item) {
    if ($count >= $limit) break;

    $title = (string) $item->title;
    $link = (string) $item->link;
    $pubDate = date("d M Y, h:i A", strtotime((string) $item->pubDate));
    $source = (string) $item->source; 

    echo "<li>";
    echo "<strong>{$title}</strong><br>";
    echo "Source: " . ($source ?: "Google News") . " | Published On {$pubDate} ";
    echo "<a href='{$link}' target='_blank'>Read More</a>";
    echo "</li>";

    $count++;
}

echo "</ul>";
?>
