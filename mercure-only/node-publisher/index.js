import https from 'https'
import querystring from 'querystring';

import jwt from "jsonwebtoken"

const token = jwt.sign({
  "mercure": {
    "publish": [
      'https://example.com/my-private-topic'
    ],
    "subscribe": [
      "*"
    ]
  }
}, '!ChangeThisMercureHubJWTSecretKey!', {
  algorithm: 'HS256'
})

console.log(token);
// token === 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiKiJdfSwiaWF0IjoxNzA1NTUwNjcxfQ.zccZIIvy3MH0h7dRKWUgUPLw6-wHh5O3JB5sZjMV1o0'

const postData = querystring.stringify({
  'topic': 'https://example.com/my-private-topic',
  'data': JSON.stringify({foo: 'hello world'})
});

const req = https.request({
  hostname: 'localhost',
  path: '/.well-known/mercure',
  method: 'POST',
  headers: {
    // use subscriber token
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/x-www-form-urlencoded',
    'Content-Length': Buffer.byteLength(postData),
  },
  // rejectUnauthorized: false
})

req.write(postData);
req.end();