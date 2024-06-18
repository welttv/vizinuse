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

foreach ($hostnames as $hostname) {
    $renderer_url = "http://$hostname:61000/api/v1/renderer/layer";
    $version_url = "http://$hostname:61000/api/v1/system/version";

    $renderer_response = @file_get_contents($renderer_url);
    $version_response = @file_get_contents($version_url);

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
