import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class FeedbackPage extends StatefulWidget {
  @override
  _FeedbackPageState createState() => _FeedbackPageState();
}

class _FeedbackPageState extends State<FeedbackPage> {
  final _formKey = GlobalKey<FormState>();
  String feedback = '';
  int? user_Id;

  @override
  void initState() {
    super.initState();
    _loadUserId(); // Load user_id when the page initializes
  }

  // Load user ID from SharedPreferences
  Future<void> _loadUserId() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      user_Id = prefs.getInt('userId'); // Use the same key as in login.dart
    });

    // Debugging: Print user_id to check if it's loaded correctly
    print('Loaded user_id: $user_Id');
  }

  // Submit feedback to the server
  Future<void> submitFeedback() async {
    // Validate the form
    if (_formKey.currentState!.validate()) {
      print('Feedback: $feedback');
      print('User ID: ${user_Id?.toString()}');

      try {
        final response = await http.post(
          Uri.parse('http://plato.helioho.st/feedback-module/create-feedback.php'),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: {
            'feedback': feedback,
            'user_id': user_Id?.toString() ?? '', // Send empty if user_Id is null
          },
        );

        print('Response status: ${response.statusCode}');
        print('Response body: ${response.body}');

        // Handle the response from the server
        final responseData = json.decode(response.body);
        if (response.statusCode == 200 && responseData['success']) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(responseData['message'])),
          );
          Navigator.pop(context); // Go back to the previous screen
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Failed: ${responseData['message']}')),
          );
        }
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: ${e.toString()}')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Submit Feedback'),
        backgroundColor: Colors.lightBlue[100],
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () {
            Navigator.pop(context); // Go back to the previous screen
          },
        ),
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Your Feedback',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  maxLines: 4,
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your feedback';
                    return null;
                  },
                  onChanged: (value) => feedback = value,
                ),
                SizedBox(height: 20),
                ElevatedButton.icon(
                  onPressed: () {
                    if (_formKey.currentState!.validate()) {
                      submitFeedback();
                    }
                  },
                  icon: Icon(Icons.send, color: Colors.blue[900]),
                  label: Text('Submit Feedback', style: TextStyle(fontSize: 18)),
                  style: ElevatedButton.styleFrom(
                    foregroundColor: Colors.blue[900],
                    backgroundColor: Colors.lightBlue[100],
                    minimumSize: Size(200, 60),
                    side: BorderSide(color: Colors.blue[900]!, width: 2),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(30),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}