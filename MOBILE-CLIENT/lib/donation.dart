import 'package:flutter/material.dart';

class DonationPage extends StatefulWidget {
  @override
  _DonationPageState createState() => _DonationPageState();
}

class _DonationPageState extends State<DonationPage> {
  final _formKey = GlobalKey<FormState>();
  String amount = '';
  String referenceNumber = '';
  String purpose = '';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Submit Donation'),
        backgroundColor: Colors.lightBlue[100],
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Center(
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                // Static QR Code Image with reduced size
                Image.asset(
                  'assets/ej.jpg', // Replace with your QR code image asset path
                  width: 150, // Adjust width to make image smaller
                  height: 150, // Adjust height to make image smaller
                ),
                SizedBox(height: 20),

                // Amount Text Input
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Amount',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  onChanged: (value) {
                    setState(() {
                      amount = value;
                    });
                  },
                ),
                SizedBox(height: 20),

                // Reference Number Text Input
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Reference Number',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  onChanged: (value) {
                    setState(() {
                      referenceNumber = value;
                    });
                  },
                ),
                SizedBox(height: 20),

                // Purpose Text Input
                TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Purpose',
                    filled: true,
                    fillColor: Colors.lightBlue[100],
                    enabledBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.blue[900]!, width: 2),
                    ),
                  ),
                  onChanged: (value) {
                    setState(() {
                      purpose = value;
                    });
                  },
                ),
                SizedBox(height: 20),

                // Submit Donation Button
ElevatedButton(
  onPressed: () {
    // Implement submit donation logic here
  },
  style: ElevatedButton.styleFrom(
    minimumSize: Size(200, 60),
    backgroundColor: Colors.lightBlue[100],
    side: BorderSide(color: Colors.blue[900]!, width: 2),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(30),
    ),
  ),
  child: Row(
    mainAxisSize: MainAxisSize.min,
    mainAxisAlignment: MainAxisAlignment.center,
    children: [
      Icon(
        Icons.volunteer_activism, // Donation icon
        color: Colors.blue[900], // Icon color
      ),
      SizedBox(width: 8), // Space between icon and text
      Text(
        'Submit Donation',
        style: TextStyle(fontSize: 18, color: Colors.blue[900]), // Blue text color
                    ),
                    ]
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