import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:timezone/data/latest.dart' as tz; // Import timezone package

class NotificationsPage extends StatefulWidget {
  @override
  _NotificationsPageState createState() => _NotificationsPageState();
}

class _NotificationsPageState extends State<NotificationsPage> {
  List<dynamic> notifications = [];
  int _userId = 0;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    tz.initializeTimeZones();
    _loadUserId();
  }

  // Load the logged-in user's user_id from SharedPreferences
  Future<void> _loadUserId() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    if (mounted) {
      setState(() {
        _userId = prefs.getInt('userId') ?? 0;
      });
      _fetchNotifications();
    }
  }

  // Fetch notifications for the logged-in user from the server
  Future<void> _fetchNotifications() async {
    final response = await http.get(
      Uri.parse('http://plato.helioho.st/notifications-module/view_notifications.php?user_id=$_userId'),
    );

    if (mounted) {
      if (response.statusCode == 200) {
        setState(() {
          notifications = json.decode(response.body);
          isLoading = false;
        });
      } else {
        print('Failed to load notifications');
        setState(() {
          isLoading = false;
        });
      }
    }
  }

  // Helper function to convert timestamp to "X hours ago" or "X days ago"
  String timeAgo(String timestamp) {
    try {
      DateTime notificationTime = DateTime.parse(timestamp).toUtc().add(Duration(hours: 8));
      Duration difference = DateTime.now().difference(notificationTime);

      if (difference.inDays > 0) {
        return '${difference.inDays} day${difference.inDays > 1 ? 's' : ''} ago';
      } else if (difference.inHours > 0) {
        return '${difference.inHours} hour${difference.inHours > 1 ? 's' : ''} ago';
      } else if (difference.inMinutes > 0) {
        return '${difference.inMinutes} minute${difference.inMinutes > 1 ? 's' : ''} ago';
      } else {
        return 'Just now';
      }
    } catch (e) {
      print('Error parsing timestamp: $e');
      return 'Unknown time';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Notifications'),
        backgroundColor: Colors.lightBlue[100],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: isLoading
            ? Center(child: CircularProgressIndicator())
            : notifications.isEmpty
                ? Center(child: Text('No notifications available.'))
                : ListView.builder(
                    itemCount: notifications.length,
                    itemBuilder: (context, index) {
                      return Card(
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(15),
                          side: BorderSide(color: Colors.blue[900]!, width: 1.5),
                        ),
                        margin: EdgeInsets.symmetric(vertical: 10),
                        child: ListTile(
                          leading: Icon(
                            Icons.notifications,
                            color: Colors.blue[900],
                          ),
                          title: Text(
                            notifications[index]['message'],
                            style: TextStyle(fontSize: 18),
                          ),
                          subtitle: Text(
                            'Received ${timeAgo(notifications[index]['created_at'])}',
                            style: TextStyle(fontSize: 14, color: Colors.grey),
                          ),
                        ),
                      );
                    },
                  ),
      ),
    );
  }
}