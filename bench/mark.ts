import http from 'k6/http';
import { sleep, check } from 'k6';

// VU = virtual user, number of parallel executions
export const options = {
  duration: '30s'
};

export default function() {
  let res = http.get('http://localhost:1337/frankenphp.php');
  check(res, { "status is 200": (res) => res.status === 200 });
}
