<h1>Barangay Mambulac Community Hit Map</h1>
    <div id="map"></div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-icon safe"></div><span>Approved</span>
        </div>
        <div class="legend-item">
            <div class="legend-icon caution"></div><span>Pending</span>
        </div>
        <div class="legend-item">
            <div class="legend-icon danger"></div><span>Rejected</span>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([10.7957, 122.9702], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            shadowSize: [41, 41]
        });

        var greenIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            shadowSize: [41, 41]
        });

        var pendingIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            shadowSize: [41, 41]
        });

        var userMarkers = [];

        // Poll for new locations every 10 seconds
        setInterval(fetchRequests, 10000);

        // Function to fetch requests and update markers
        function fetchRequests() {
           fetch('https://plato.helioho.st/fetch_requests.php') // Ensure HTTPS
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // Log the fetched data
                    if (data.error) {
                        console.error(data.error);
                        alert('Error: ' + data.error);
                        return;
                    }

                    // Clear existing markers
                    userMarkers.forEach(({ marker }) => marker.remove());
                    userMarkers = [];

                    // Check if data is empty and exit if so
                    if (data.length === 0) {
                        return; // Exit if no data
                    }

                    // Add new markers to the map based on latitude and longitude
                    data.forEach(request => {
                        // Validate request data
                        if (!request.latitude || !request.longitude || !request.request_status) {
                            console.error('Request data missing fields:', request);
                            return; // Skip invalid data
                        }

                        let icon;
                        switch (request.request_status) {
                            case 'pending': icon = pendingIcon; break; // Use the blue icon for pending
                            case 'approved': icon = greenIcon; break; // Use the green icon for approved
                            case 'rejected': icon = redIcon; break; // Use the red icon for rejected
                            default: icon = greenIcon; // Default to green if status is unknown
                        }

                        // Create a marker and bind a popup with user details
                        const marker = L.marker([request.latitude, request.longitude], { icon: icon })
                            .addTo(map);

                        // Create a popup with user details
                        const popupContent = `
                            <div>
                                <strong>User:</strong> ${request.user_firstname} ${request.user_lastname} <br>
                                <strong>Request:</strong> ${request.request_details} <br>
                                <strong>Status:</strong> ${request.request_status}
                            </div>`;
                        const popup = L.popup().setContent(popupContent);

                        // Bind mouseover and mouseout events to show/hide the popup
                        marker.on('mouseover', function() {
                            marker.bindPopup(popup).openPopup();
                        });

                        marker.on('mouseout', function() {
                            marker.closePopup();
                        });

                        userMarkers.push({ marker, request });
                    });
                })
                .catch(error => {
                    console.error('Error fetching requests:', error);
                    alert('Failed to load requests. Please try again.');
                });
        }

        // Initial fetch to populate the map
        fetchRequests();
 </script>
