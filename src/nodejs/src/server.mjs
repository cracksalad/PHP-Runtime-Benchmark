import { createServer } from 'node:http';

const server = createServer((req, res) => {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.end('Hello, world!');
});

server.listen(1337, '0.0.0.0', () => {
  console.log('Listening on 0.0.0.0:1337');
});
