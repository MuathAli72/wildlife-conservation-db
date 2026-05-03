<?php
$supabase_url = 'https://yjrsvgqmjqmigohgguzq.supabase.co/rest/v1';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InlqcnN2Z3FtanFtaWdvaGdndXpxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzc2ODg5MjcsImV4cCI6MjA5MzI2NDkyN30.9QFFtFfzCGlnX87CaG0MdjkI7PAMU_411xvsaYTZEW8';

function supabase_query($endpoint, $query = '') {
    global $supabase_url, $supabase_key;
    $url = $supabase_url . $endpoint;
    if ($query) {
        $url .= '?' . $query;
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key",
        "Authorization: Bearer $supabase_key"
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}
?>