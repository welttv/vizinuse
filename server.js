const express = require('express');
const axios = require('axios');
const cors = require('cors');

const app = express();
const PORT = 3000;

const HOSTNAMES = [
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

const axiosInstance = axios.create({
    timeout: 1000 // 1 second timeout
});

// Enable CORS for all routes
app.use(cors());

app.get('/check-artist-free', async (req, res) => {
    try {
        const results = await Promise.all(
            HOSTNAMES.map(async (hostname) => {
                try {
                    const rendererResponse = await axiosInstance.get(`http://${hostname}:61000/api/v1/renderer/layer`);
                    const versionResponse = await axiosInstance.get(`http://${hostname}:61000/api/v1/system/version`);
                    const version = `${versionResponse.data._Major}.${versionResponse.data._Minor}.${versionResponse.data._Build}.${versionResponse.data._Revision}`;
                    const artistFree = rendererResponse.data.every(item => item.Scene === "00000000-0000-0000-0000-000000000000");
                    return {
                        hostname,
                        version,
                        artist_free: artistFree
                    };
                } catch (error) {
                    //console.error(`Error connecting to ${hostname}:`, error.message);
                    return {
                        hostname,
                        error: `Failed to connect to ${hostname}`
                    };
                }
            })
        );

        const resultsFormatted = results.map(result => ({
            hostname: result.hostname,
            version: result.version || 'N/A',
            artist_free: result.artist_free || false,
            error: result.error || null
        }));

        res.json({ results: resultsFormatted });

    } catch (error) {
        console.error('Error processing request:', error);
        res.status(500).json({ error: 'Failed to process the request' });
    }
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
