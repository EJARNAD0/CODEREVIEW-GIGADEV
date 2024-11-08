import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'register.dart'; // Ensure you have the register.dart file in your project
import 'main.dart'; // Import your home page (or target page after login)

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  String username = '';
  String password = '';
  bool rememberMe = false; // Flag to remember user

  @override
  void initState() {
    super.initState();
    _loadCredentials();
  }

  // Load stored credentials
  Future<void> _loadCredentials() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      username = prefs.getString('username') ?? '';
      password = prefs.getString('password') ?? '';
      rememberMe = prefs.getBool('rememberMe') ?? false;
    });
  }

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
          SnackBar(content: Text(responseData['message'])),
        );

        // If login is successful
        if (responseData['success']) {
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: true, // Ensure UI resizes when keyboard appears
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: SingleChildScrollView( // Add SingleChildScrollView to allow scrolling when keyboard appears
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                SizedBox(height: 50), // Add space at the top

                // Logo at the top
                Center(
                  child: Image.asset(
                    'assets/plato.png', // Ensure 'plato.png' is in the 'assets' folder
                    height: 100,
                  ),
                ),
                SizedBox(height: 50),

                // Username
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
                  initialValue: username, // Prefill with stored username
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your username';
                    return null;
                  },
                  onChanged: (value) => username = value,
                ),
                SizedBox(height: 10),

                // Password
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
                  initialValue: password, // Prefill with stored password
                  validator: (value) {
                    if (value!.isEmpty) return 'Please enter your password';
                    return null;
                  },
                  onChanged: (value) => password = value,
                ),
                SizedBox(height: 20),

                // Remember Me checkbox
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Checkbox(
                      value: rememberMe,
                      onChanged: (newValue) {
                        setState(() {
                          rememberMe = newValue!;
                        });
                      },
                    ),
                    Text('Remember Me'),
                  ],
                ),

                // Login Button
                ElevatedButton.icon(
                  onPressed: () {
                    loginUser(); // Call loginUser function
                  },
                  icon: Icon(Icons.login, color: Colors.blue[900]),
                  label: Text('Login', style: TextStyle(fontSize: 18)),
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
                SizedBox(height: 20),

                // Register Button
                Center(
                  child: TextButton(
                    onPressed: () {
                      // Navigate to the RegisterPage
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => RegisterPage()),
                      );
                    },
                    child: Text(
                      "Don't have an account? Register here",
                      style: TextStyle(
                        color: Colors.blue[900],
                        fontSize: 16,
                      ),
                    ),
                  ),
                ),
                SizedBox(height: 20), // Add some space at the bottom to prevent overflow
              ],
            ),
          ),
        ),
      ),
    );
  }
}