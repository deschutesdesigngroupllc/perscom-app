from flask import Flask, request, Response, jsonify
import json

app = Flask(__name__)
app.config['JSONIFY_PRETTYPRINT_REGULAR'] = True

@app.route('/webhook', methods=['POST'])
def respond():
    print('\033[92m' + 'Incoming webhook request...'  + '\033[0m' + '\n')
    print('\033[92m' + 'Headers:'  + '\033[0m')
    print(request.headers)
    print('\033[92m' + 'Payload:'  + '\033[0m')
    print(json.dumps(request.json, indent=2))
    return Response(status=200)
