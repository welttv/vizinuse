<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$hostnames = [
    'vizartst01ap',
    'vizartst02ap',
    'vizartst03ap',
    'vizartst04ap',
    'vizartst05ap',
    'vizartst06ap',
    'vizartst07ap',
    'vizartst08ap',
    'vizartst09ap',
    'vizartst10ap'
];

$results = [];

function fetch_data($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // 1 second timeout
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code != 200) {
        return false;
    }
    return $response;
}

foreach ($hostnames as $hostname) {
    $renderer_url = "http://$hostname:61000/api/v1/renderer/layer";
    $version_url = "http://$hostname:61000/api/v1/system/version";

    $renderer_response = fetch_data($renderer_url);
    $version_response = fetch_data($version_url);

    if ($renderer_response === FALSE || $version_response === FALSE) {
        $results[] = [
            'hostname' => $hostname,
            'version' => 'N/A',
            'artist_free' => false,
            'error' => 'Failed to connect to ' . $hostname
        ];
        continue;
    }

    $renderer_data = json_decode($renderer_response, true);
    $version_data = json_decode($version_response, true);

    $artist_free = true;
    foreach ($renderer_data as $item) {
        if ($item['Scene'] !== "00000000-0000-0000-0000-000000000000") {
            $artist_free = false;
            break;
        }
    }

    $version = $version_data['_Major'] . '.' . $version_data['_Minor'] . '.' . $version_data['_Build'] . '.' . $version_data['_Revision'];

    $results[] = [
        'hostname' => $hostname,
        'version' => $version,
        'artist_free' => $artist_free,
        'error' => null
    ];
}

echo json_encode(['results' => $results]);

?>
