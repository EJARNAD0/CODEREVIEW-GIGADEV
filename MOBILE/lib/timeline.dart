import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:timezone/data/latest.dart' as tz;
import 'feedback.dart';
import 'package:intl/intl.dart'; // Import the intl package

class TimelinePage extends StatefulWidget {
  @override
  _TimelinePageState createState() => _TimelinePageState();
}

class _TimelinePageState extends State<TimelinePage> {
  List<Map<String, dynamic>> feedbackEntries = [];
  bool isLoading = true; // Track loading state

  @override
  void initState() {
    super.initState();
    tz.initializeTimeZones(); // Initialize timezone data
    fetchFeedback(); // Fetch feedback when the page initializes
  }

  Future<void> fetchFeedback() async {
    final url = Uri.parse('https://plato.helioho.st/feedback-module/view-feedback.php'); // Replace with your actual URL

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final List<dynamic> jsonData = json.decode(response.body);
        if (mounted) {
          setState(() {
            feedbackEntries = jsonData.map((feedback) {
              return {
                'user': '${feedback['user_firstname']} ${feedback['user_lastname']}', // Combine first and last names
                'feedback': feedback['feedback'],
                'time': timeAgo(feedback['timestamp']), // Convert timestamp to human-readable format
              };
            }).toList();
            isLoading = false; // Set loading to false
          });
        }
      } else {
        throw Exception('Failed to load feedback');
      }
    } catch (e) {
      print('Error fetching feedback: $e');
      if (mounted) {
        setState(() {
          isLoading = false; // Set loading to false even if there’s an error
        });
      }
    }
}

  // Helper function to convert timestamp to "X hours ago" or "X days ago"
  // Helper function to convert timestamp to "X hours ago" or "X days ago"
String timeAgo(String timestamp) {
  try {
    // Parse the timestamp and convert to Manila time
    DateTime feedbackTime = DateTime.parse(timestamp).toUtc().add(Duration(hours: 8)); // Manually adjust to UTC+8 for Asia/Manila
    Duration difference = DateTime.now().difference(feedbackTime);

    String militaryTime = DateFormat.Hm().format(feedbackTime); // Format to military time

    if (difference.inDays > 0) {
      return '${difference.inDays} day${difference.inDays > 1 ? 's' : ''} ago';
    } else if (difference.inHours > 0) {
      return '${difference.inHours} hour${difference.inHours > 1 ? 's' : ''} ago';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes} minute${difference.inMinutes > 1 ? 's' : ''} ago';
    } else {
      return 'Just now at $militaryTime';
    }
  } catch (e) {
    print('Error parsing timestamp: $e');
    return 'Unknown time'; // Fallback if parsing fails
  }
}

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('User Feedback Timeline'),
        backgroundColor: Colors.lightBlue[100],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Page Title
            Text(
              'Feedbacks from Other Users',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 20),

            // Loading Indicator
            if (isLoading)
              Center(child: CircularProgressIndicator())
            else if (feedbackEntries.isEmpty)
              Center(child: Text('No feedback available.'))
            else
              // Feedback List
              Expanded(
                child: ListView.builder(
                  itemCount: feedbackEntries.length,
                  itemBuilder: (context, index) {
                    return Card(
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(15),
                        side: BorderSide(color: Colors.blue[900]!, width: 1.5),
                      ),
                      margin: EdgeInsets.symmetric(vertical: 10),
                      child: ListTile(
                        title: Text(
                          feedbackEntries[index]['user']!, // User name
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(height: 5),
                            Text(
                              feedbackEntries[index]['feedback']!, // Feedback text
                              style: TextStyle(fontSize: 16),
                            ),
                            SizedBox(height: 5),
                            Text(
                              feedbackEntries[index]['time']!, // Timestamp of feedback
                              style: TextStyle(fontSize: 14, color: Colors.grey),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),
          ],
        ),
      ),
      // Floating action button to submit feedback
      floatingActionButton: SizedBox(
        height: 70, // Height for the button
        width: 70,  // Width for the button
        child: ElevatedButton(
          onPressed: () {
            // Navigate to FeedbackPage when the button is pressed
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => FeedbackPage()),
            );
          },
          child: Icon(Icons.feedback, color: Colors.blue[900], size: 30), // Icon size adjusted
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.lightBlue[100], // Button color
            shape: CircleBorder(), // Circular shape
            side: BorderSide(color: Colors.blue[900]!, width: 2), // Border style
            padding: EdgeInsets.all(16), // Padding for the icon
          ),
        ),
      ),
    );
  }
}