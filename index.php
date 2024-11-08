<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="#userManagement">User Management</a>
        <a href="#foodListing">Food Listing Management</a>
        <a href="#systemConfig">System Configuration</a>
        <a href="#dashboard">Personalized Dashboard</a>
    </nav>

    <section id="userManagement">
        <h2>User Management</h2>
        <table id="userTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- User rows will be inserted here -->
            </tbody>
        </table>
    </section>

    <section id="foodListing">
        <h2>Food Listing Management</h2>
        <table id="foodTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Food rows will be inserted here -->
            </tbody>
        </table>
    </section>

    <section id="systemConfig">
        <h2>System Configuration</h2>
        <form id="configForm">
            <label for="notificationEmail">Notification Email:</label>
            <input type="email" id="notificationEmail" name="notificationEmail">
            <button type="submit">Save</button>
        </form>
    </section>

    <section id="dashboard">
        <h2>Personalized Dashboard</h2>
        <table id="listingTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Listing rows will be inserted here -->
            </tbody>
        </table>
    </section>

    <script src="script/user-management.js"></script>
    <script src="script/food-listing.js"></script>
    <script src="script/config.js"></script>
    <script src="script/dashboard.js"></script>
</body>
</html>
