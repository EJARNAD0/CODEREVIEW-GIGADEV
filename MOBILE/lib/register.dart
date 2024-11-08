import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'main.dart';
import 'package:shared_preferences/shared_preferences.dart';

class RegisterPage extends StatefulWidget {
  @override
  _RegisterPageState createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  String firstname = '';
  String lastname = '';
  String username = '';
  String password = '';
  String confirmPassword = '';
  String access = 'user';
  String address = '';
  String city = '';
  String userStatus = '1';
  bool rememberMe = false; // Flag to remember user

  // Save credentials using SharedPreferences
  Future<void> _saveCredentials() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    if (rememberMe) {
      await prefs.setString('username', username);
      await prefs.setString('password', password);
      await prefs.setBool('rememberMe', true);
    } else {
      await prefs.remove('username');
      await prefs.remove('password');
      await prefs.setBool('rememberMe', false);
    }
  }

  Future<void> registerUser() async {
    if (password != confirmPassword) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Passwords do not match!')),
      );
      return;
    }

    try {
      final response = await http.post(
        Uri.parse('https://plato.helioho.st/users-module/create-user.php'),
        body: {
          'user_firstname': firstname,
          'user_lastname': lastname,
          'username': username,
          'user_password': password,
          'user_access': access,
          'user_address': address,
          'user_city': city,
          'user_status': userStatus,
        },
      );

      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(responseData['message'])),
        );

        if (responseData['success']) {
          // Automatically log in after registration
          await loginUser();
        }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to register user: ${response.reasonPhrase}')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: ${e.toString()}')),
      );
    }
  }

  Future<void> loginUser() async {
  if (!_formKey.currentState!.validate()) return; // Validate form

  try {
    final response = await http.post(
      Uri.parse('http://plato.helioho.st/login.php'),
      body: {
        'username': username,
        'password': password,
        'is_api': 'true', // Inform server that this is an API request
      },
    );

    if (response.statusCode == 200) {
      final responseData = json.decode(response.body);

      // Show message from response
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(responseData['message'] ?? 'No message provided')),
      );

      // Check if success key exists and is true
      if (responseData['success'] == true) { // Ensure that we check for true explicitly
        // Save the credentials if "Remember Me" is checked
        await _saveCredentials();

        // Save user data in SharedPreferences
        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setInt('userId', responseData['user_id']); // Store as an int
        await prefs.setString('userFirstname', responseData['user_firstname']);
        await prefs.setString('userLastname', responseData['user_lastname']);
        await prefs.setString('userAddress', responseData['user_address']);
        await prefs.setString('userCity', responseData['user_city']);

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => HomePage()), // Replace with actual home page
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Login failed: ${responseData['message'] ?? 'Unknown error'}')),
        );
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to login: ${response.reasonPhrase}')),
      );
    }
  } catch (e) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Error: ${e.toString()}')),
    );
  }
}

  Future<void> _saveLoginInfo(String username) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool('isLoggedIn', true);
    await prefs.setString('username', username);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: SingleChildScrollView(
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Align(
                  alignment: Alignment.topLeft,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.pop(context);
                    },
                    icon: Icon(Icons.arrow_back, color: Colors.blue[900]),
                    label: Text('Back', style: TextStyle(fontSize: 18)),
                    style: ElevatedButton.styleFrom(
                      foregroundColor: Colors.blue[900],
                      backgroundColor: Colors.lightBlue[100],
                      minimumSize: Size(120, 40),
                      side: BorderSide(color: Colors.blue[900]!, width: 2),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(30),
                      ),
                    ),
                  ),
                ),
                SizedBox(height: 20),
                Center(
                  child: Image.asset(
                    'assets/plato.png',
                    height: 60,
                  ),
                ),
                SizedBox(height: 50),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'First Name',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your first name';
                    return null;
                  },
                  onChanged: (value) => firstname = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Last Name',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your last name';
                    return null;
                  },
                  onChanged: (value) => lastname = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Username',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your username';
                    return null;
                  },
                  onChanged: (value) => username = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Password',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  obscureText: true,
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your password';
                    return null;
                  },
                  onChanged: (value) => password = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Confirm Password',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  obscureText: true,
                  validator: (value) {
                    if (value!.isEmpty) return 'Please confirm your password';
                    return null;
                  },
                  onChanged: (value) => confirmPassword = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Address',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your address';
                    return null;
                  },
                  onChanged: (value) => address = value,
                ),
                SizedBox(height: 10),
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'City',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your city';
                    return null;
                  },
                  onChanged: (value) => city = value,
                ),
                SizedBox(height: 20),
                ElevatedButton.icon(
                  onPressed: () {
                    if (_formKey.currentState!.validate()) {
                      registerUser();
                    }
                  },
                  icon: Icon(Icons.person_add, color: Colors.blue[900]),
                  label: Text('Register', style: TextStyle(fontSize: 18)),
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