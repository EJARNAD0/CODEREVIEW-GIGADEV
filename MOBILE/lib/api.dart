import 'dart:convert';
import 'package:http/http.dart' as http;

Future<void> sendData(String name) async {
  final url = Uri.parse('http://plato.helioho.st/api/api.php'); //local ip bol

  try {
    final response = await http.post(
      url,
      headers: <String, String>{
        'Content-Type': 'application/json; charset=UTF-8',
      },
      body: jsonEncode(<String, String>{
        'name': name, // mang send'name' field
      }),
    );

    // Check kng ok ang server
    if (response.statusCode == 200) {
      final jsonResponse = jsonDecode(response.body);
      if (jsonResponse['success'] == true) {
        print("Data submitted successfully: ${jsonResponse['message']}");
      } else {
        print("Error: ${jsonResponse['error']}");
      }
    } else {
      print("Server Error: ${response.statusCode} - ${response.reasonPhrase}");
    }
  } catch (e) {
    // Catch any errors during the request
    print("Failed to connect to the server: $e");
  }
}

void main() {
  sendData('Jane Doe'); 
}