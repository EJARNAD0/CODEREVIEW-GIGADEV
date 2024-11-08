import 'package:flutter/material.dart';
import 'timeline.dart';
import 'request.dart';
import 'user.dart';
import 'settings.dart';
import 'login.dart';
import 'notifications.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Plato',
      home: LoginPage(),
      theme: ThemeData(
        primarySwatch: Colors.blue,
        scaffoldBackgroundColor: Colors.blueGrey[50],
      ),
    );
  }
}

class HomePage extends StatefulWidget {
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  String firstName = '';

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      setState(() {
        firstName = prefs.getString('userFirstname') ?? '';
      });
    } catch (e) {
      print("Error loading user data in HomePage: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Image.asset(
                  'assets/plato.png',
                  height: 150,
                ),
                SizedBox(height: 20),
                Text(
                  'Hello, $firstName!',
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                ),
                SizedBox(height: 30),
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => TimelinePage()),
                    );
                  },
                  icon: Icon(Icons.timeline, color: Colors.blue[900]),
                  label: Text(
                    'View Timeline',
                    style: TextStyle(fontSize: 18),
                  ),
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
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => RequestPage()),
                    );
                  },
                  icon: Icon(Icons.add_circle, color: Colors.blue[900]),
                  label: Text(
                    'Request',
                    style: TextStyle(fontSize: 18),
                  ),
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
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => UserPage()),
                    );
                  },
                  icon: Icon(Icons.person, color: Colors.blue[900]),
                  label: Text(
                    'User',
                    style: TextStyle(fontSize: 18),
                  ),
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
                ElevatedButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => SettingsPage()),
                    );
                  },
                  child: Icon(Icons.settings, color: Colors.blue[900]),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.lightBlue[100],
                    minimumSize: Size(60, 60),
                    shape: CircleBorder(),
                    side: BorderSide(color: Colors.blue[900]!, width: 2),
                  ),
                ),
              ],
            ),
          ),
          Positioned(
            top: 40,
            right: 20,
            child: ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => NotificationsPage()),
                );
              },
              child: Icon(Icons.notifications, color: Colors.blue[900]),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.lightBlue[100],
                minimumSize: Size(60, 60),
                shape: CircleBorder(),
                side: BorderSide(color: Colors.blue[900]!, width: 2),
              ),
            ),
          ),
          Positioned(
            bottom: 20,
            left: 20,
            child: ElevatedButton(
              onPressed: () {
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(builder: (context) => LoginPage()),
                );
              },
              child: Icon(Icons.logout, color: Colors.blue[900]),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.lightBlue[100],
                minimumSize: Size(60, 60),
                shape: CircleBorder(),
                side: BorderSide(color: Colors.blue[900]!, width: 2),
              ),
            ),
          ),
        ],
      ),
    );
  }
}