# PHP Runtime Benchmarks

There are quite a few PHP runtimes right now and all of them address performance as one of the main concerns. 
So the obvious question is, which one is actually the fastest. Since runtimes are hard to compare in total, we have to start somewhere: I chose HTTP Server as the first use case to compare the runtimes in.

This whole benchmark is oriented on ["Performance benchmark of PHP runtimes" by Dzmitry Kazbiarovich from January 2024](https://dev.to/dimdev/performance-benchmark-of-php-runtimes-2lmc), which lacks AMPHP and ReactPHP as runtime alternatives and is pretty focused on Symfony.

So this benchmarks work independent of Symfony. The actual measurements are performed by [k6 by Grafana Labs](https://k6.io/open-source/).

## Featuring...

- AMPHP
- FrankenPHP (classic and worker mode)
- OpenSwoole
- ReactPHP
- RoadRunner
- Swoole
- Workerman

If you want to see other alternatives, please let me know!

As references I chose Apache mod_php with mpm_prefork as well as Nginx with PHP-FPM as baseline, Rust ActiX Web as some kind of upper limit and NodeJS as the probably main competitor.

### AMPHP

AMPHP uses modern PHP features like Fibers to provide pseudo-parallel execution with Coroutines.

This benchmark currently does not look at event loop extensions, which are supported by Revolt (which is internally used by AMPHP). See [https://revolt.run/extensions](https://revolt.run/extensions).

### FrankenPHP and RoadRunner

FrankenPHP and RoadRunner are different Go implementations of the PHP runtime.

### ReactPHP

ReactPHP is a PHP library for event-driven programming introducing an event loop.

### Swoole and OpenSwoole

Swoole and OpenSwoole are C++ extensions for PHP which include e.g. an HTTP server and provide Coroutine, Thread and Process based concepts.
OpenSwoole is actually a fork of Swoole.

## Results

During the benchmark, the servers handled as many requests as they can in a fixed amount of time and with different amounts of concurrent requests. 
The servers respond with a simple `"Hello, world!"` and a `Content-Type: text/plain` header as well as a status code 200.
The following numbers have been measured/calculated by *k6*:

![Requests per Second](./img/requests-per-second.png)
![Average Response Time](./img/average-response-time.png)

<details>
<summary>Raw numbers</summary>

All HTTP servers run in an Alpine-based PHP 8.4 Docker image limited to a single CPU core to get comparable results. Memory is not limited since it is not expected to make any difference here.

|Runtime|VUS|Requests per second|Average response time (ms)|
|-------|--:|------------------:|--------------------------|
|Apache mod_php mpm_prefork|10|8,122|1.17|
|Apache mod_php mpm_prefork|100|6,873|14.47|
|Apache mod_php mpm_prefork|1000|3,401|176|
|Nginx PHP-FPM|10|4,054|2.41|
|Nginx PHP-FPM|100|3,755|26.5|
|Nginx PHP-FPM|1000|4,222|235|
|AMPHP (amphp/http-server@3.4.3)|10|240|41.5|
|AMPHP (amphp/http-server@3.4.3)|100|2,407|41.5|
|AMPHP (amphp/http-server@3.4.3)|1000|10,455|95.3|
|FrankenPHP classic mode (frankenphp@1.10.1)|10|9,033|1.07|
|FrankenPHP classic mode (frankenphp@1.10.1)|100|8,501|11.7|
|FrankenPHP classic mode (frankenphp@1.10.1)|1000|8,669|115|
|FrankenPHP worker mode (frankenphp@1.10.1)|10|11,115|0.87|
|FrankenPHP worker mode (frankenphp@1.10.1)|100|10,395|9.58|
|FrankenPHP worker mode (frankenphp@1.10.1)|1000|10,237|97.4|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 1 worker process)|10|22,555|0.41|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 1 worker process)|100|22,214|4.46|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 1 worker process)|1000|20,469|48.7|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 2 worker processes)|10|19,013|0.490|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 2 worker processes)|100|19,260|5.13|
|OpenSwoole (ext-openswoole@25.2.0, 1 reactor thread, 2 worker processes)|1000|19,949|49.95|
|OpenSwoole (ext-openswoole@25.2.0, 2 reactor threads, 2 worker processes)|10|16,427|0.571|
|OpenSwoole (ext-openswoole@25.2.0, 2 reactor threads, 2 worker processes)|100|16,611|5.96|
|OpenSwoole (ext-openswoole@25.2.0, 2 reactor threads, 2 worker processes)|1000|15,907|62.56|
|ReactPHP (react/http@1.11.0)|10|37,935|0.236|
|ReactPHP (react/http@1.11.0)|100|38,903|2.52|
|ReactPHP (react/http@1.11.0)|1000|32,651|30.5|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=1)|10|6,327|1.55|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=1)|100|6,080|16.4|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=1)|1000|5,868|170|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=2)|10|5,732|1.71|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=2)|100|5,588|17.85|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=2)|1000|5,402|184|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=3)|10|5,188|1.88|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=3)|100|5,038|19.8|
|RoadRunner (rr@2025.1.5, http.pool.num_workers=3)|1000|4,863|205|
|Swoole (ext-swoole@6.1.3)|10|42,548|0.209|
|Swoole (ext-swoole@6.1.3)|100|39,665|2.48|
|Swoole (ext-swoole@6.1.3)|1000|35,634|28|
|Workerman (workerman/workerman@5.1.6, 1 worker, no event/swoole/swow)|10|70,911|0.105|
|Workerman (workerman/workerman@5.1.6, 1 worker, no event/swoole/swow)|100|79,957|1.2|
|Workerman (workerman/workerman@5.1.6, 1 worker, no event/swoole/swow)|1000|63,015|15.7|
|Workerman (workerman/workerman@5.1.6, 2 workers, no event/swoole/swow)|10|61,787|0.136|
|Workerman (workerman/workerman@5.1.6, 2 workers, no event/swoole/swow)|100|69,186|1.36|
|Workerman (workerman/workerman@5.1.6, 2 workers, no event/swoole/swow)|1000|57,131|16.5|
|Rust ActiX Web (v4.12.1)|10|69,655|0.109|
|Rust ActiX Web (v4.12.1)|100|71,923|1.34|
|Rust ActiX Web (v4.12.1)|1000|60,664|16.34|
|NodeJS (v25.2.1)|10|50,208|0.172|
|NodeJS (v25.2.1)|100|47,932|2.05|
|NodeJS (v25.2.1)|1000|39,121|25.5|

</details>

### Notes

First of all, it should be noted, that I am comparing mostly stock configurations here. There are most probably ways to tweak the performance of the individual runtimes. Feel free to look at the server implementations and test your own configurations (and please let me know if you find something interesting!).

- The average response time seems to be roughly proportional to the amount of concurrent requests.
- AMPHP is really bad below 1000 parallel requests, but it outperformes FrankenPHP and RoadRunner at 1000 parallel requests.
- Although ReactPHP is plain PHP - no Go, no C++ - it is way faster than I expected.
- Why is OpenSwoole about half as fast as Swoole? They are expected to be quite similar.

## How to benchmark

1. Start a server of your choice from the `src` folder. 
    1. `cd src/<runtime>`
    2. `docker build -t cracksalad/php-runtime-benchmark-http-server-<runtime> .`
    3. `docker run --rm --cpus 1 -p 1337:1337 -it cracksalad/php-runtime-benchmark-http-server-<runtime>`
        - for Apache, use container port 80
        - for Nginx, use container port 8080
2. Run `k6 run --vus <VUS> bench/mark.ts` with `<VUS>` being the number of parallel executions.
3. Wait 30 seconds and voilà!

## Docs of different runtimes

- [amphp.org](https://amphp.org/http-server)
- [frankenphp.org](https://frankenphp.dev/docs/worker/)
- [openswoole.com](https://openswoole.com/docs/modules/swoole-http-server-doc)
- [reactphp.org](https://reactphp.org/http/#server-usage)
- [docs.roadrunner.dev](https://docs.roadrunner.dev/docs/general/quick-start)
- [swoole.com](https://wiki.swoole.com/en/#/start/start_http_server)
- [workerman.net](https://manual.workerman.net/doc/en/getting-started/simple-example.html)
