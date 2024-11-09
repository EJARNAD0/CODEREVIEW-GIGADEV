import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class UserPage extends StatefulWidget {
  @override
  _UserPageState createState() => _UserPageState();
}

class _UserPageState extends State<UserPage> {
  String _userFirstName = "";
  String _userLastName = "";
  String _userAddress = "";
  String _userCity = "";
  bool _isLoading = true; // Track loading state

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  // Load user data with error handling
  Future<void> _loadUserData() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      setState(() {
        _userFirstName = prefs.getString('userFirstname') ?? "";
        _userLastName = prefs.getString('userLastname') ?? "";
        _userAddress = prefs.getString('userAddress') ?? "";
        _userCity = prefs.getString('userCity') ?? "";
        _isLoading = false; // Set loading to false when done
      });
    } catch (e) {
      print("Error loading user data: $e");
      setState(() {
        _isLoading = false; // Ensure loading is false even on error
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('User Profile'),
        backgroundColor: Colors.lightBlue[100],
      ),
      body: Center(
        child: _isLoading
            ? CircularProgressIndicator() // Show loading indicator if data is still loading
            : Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    CircleAvatar(
                      radius: 60,
                      backgroundColor: Colors.lightBlue[100],
                      child: Icon(Icons.person, size: 60, color: Colors.blue[900]),
                    ),
                    SizedBox(height: 20),
                    Text(
                      'First Name: $_userFirstName',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 10),
                    Text(
                      'Last Name: $_userLastName',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 10),
                    Text(
                      'Address: $_userAddress',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 10),
                    Text(
                      'City: $_userCity',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
      ),
    );
  }
}