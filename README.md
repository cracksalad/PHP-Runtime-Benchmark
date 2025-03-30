# PHP Runtime Benchmarks

There are quite a few PHP runtime right now and all of them address performance as one of the main concerns. 
So the obvious question is, which one is actually the fastest. Since runtimes are hard to compare in total, we have to start somewhere: I chose HTTP Server as the first use case to compare the runtimes in.

This whole benchmark is oriented on ["Performance benchmark of PHP runtimes" by Dzmitry Kazbiarovich from January 2024](https://dev.to/dimdev/performance-benchmark-of-php-runtimes-2lmc), which lacks AMPHP and ReactPHP as runtime alternatives and is pretty focused on Symfony and Docker.

So this benchmarks work independent of Symfony and Docker. The actual measurements are performed by [k6 by Grafana Labs](https://k6.io/open-source/).

## Featuring...

- AMPHP
- FrankenPHP (classic and worker mode)
- ReactPHP
- RoadRunner
- Swoole

If you want to see other alternatives, please let me know!

## Results

During the benchmark, the servers handled as many requests as they can in a fixed amount of time and with different amounts of concurrent requests. 
The servers respond with a simple `"Hello, world!"` and a `Content-Type: text/plain` header as well as a status code 200.
The following numbers have been measured/calculated by *k6*:

![Requests per Second](./img/requests-per-second.png)
![Average Response Time](./img/average-response-time.png)

<details>
<summary>Raw numbers</summary>

|Runtime|VUS|Requests per second|Average response time (ms)|Failed Request Ratio (%)|
|-------|--:|------------------:|--------------------------|------------------------|
|AMPHP (amphp/http-server@3.4.2)|10|240|41.5|0|
|AMPHP (amphp/http-server@3.4.2)|100|2,408|41.4|0|
|AMPHP (amphp/http-server@3.4.2)|1000|18,241|53.5|0|
|AMPHP (amphp/http-server@3.4.2)|10000|10,269|63.6|1.57|
|FrankenPHP classic mode (frankenphp@1.5.0)|10|25,065|0.357|0|
|FrankenPHP classic mode (frankenphp@1.5.0)|100|35,369|2.76|0|
|FrankenPHP classic mode (frankenphp@1.5.0)|1000|43,290|22.9|0|
|FrankenPHP classic mode (frankenphp@1.5.0)|10000|38,911|253|0|
|FrankenPHP worker mode (frankenphp@1.5.0)|10|20,836|0.452|0|
|FrankenPHP worker mode (frankenphp@1.5.0)|100|20,567|4.8|0|
|FrankenPHP worker mode (frankenphp@1.5.0)|1000|20,467|48.7|0|
|FrankenPHP worker mode (frankenphp@1.5.0)|10000|20,243|486|0|
|ReactPHP (react/http@1.11.0)|10|46,828|0.188|0|
|ReactPHP (react/http@1.11.0)|100|45,636|2.15|0|
|ReactPHP (react/http@1.11.0)|1000|38,679|25.74|0|
|ReactPHP (react/http@1.11.0)|10000|19,355|26.16|0.71|
|RoadRunner (rr@2024.3.5)|10|24,604|0.36|0|
|RoadRunner (rr@2024.3.5)|100|27,552|3.55|0|
|RoadRunner (rr@2024.3.5)|1000|28,209|35.3|0|
|RoadRunner (rr@2024.3.5)|10000|27,430|360|0|
|Swoole (ext-openswoole@25.2.0)|10|84,888|0.084|0|
|Swoole (ext-openswoole@25.2.0)|100|134,038|0.695|0|
|Swoole (ext-openswoole@25.2.0)*|1000|123,066|5.03|0|
|Swoole (ext-openswoole@25.2.0)*|10000|92,793|13.3|0|

\* `nofile` (number of open files) has been increased in */etc/security/limits.conf*

</details>

### Notes

First of all, it should be noted, that I am comparing mostly stock configurations here. There are most probably ways to tweak the performance of the individual runtimes. Feel free to look at the server implementations and test your own configurations.

- **AMPHP and ReactPHP have failed to respond successfully to 1.57 % and 0.71 % of the requests respectively at 10k concurrent requests**
- The average response time seems to be roughly proportional to the amount of concurrent requests. However, this does not hold at all for AMPHP and also not for ReactPHP and Swoole with 10k concurrent requests.
- FrankenPHP in worker mode handles a constant amount of requests per second regardless of the concurrency. I have to dig deeper here!
- There seems to be a trade off between low response times and high amount of handled requests when it comes to heavy load: AMPHP's, ReactPHP's and Swoole's handled requests per second decrease significantly while keeping up the rather low response times. FrankenPHP (both modes) and RoadRunner on the other hand are able to keep up their handled requests per second, but the response time increases significantly. 
- **The elephant in the room (no, not PHP at this point!): AMPHP is by far the slowest alternative while Swoole is by far the fastest.**

### Personal opinion

This is an unexpectedly clear result. It is a pity that Swoole is the most poorly documented of the alternatives compared (the sample code from the documentation contains many errors that first had to be fixed to create this benchmark). Sadly, this makes a recommendation difficult.

## How to benchmark

1. Start a server of your choice from the `src` folder. 
    - In case of *FrankenPHP classic mode*, you need to run `frankenphp php-server --root src --listen localhost:1337`.
    - In case of *FrankenPHP worker mode*, you need to run `frankenphp php-server --root src --listen localhost:1337 --worker src/frankenphp-workers.php`.
    - In case of *RoadRunner*, you need to run `rr serve`.
    - Otherwise, run `php src/<SERVER>.php`.
2. Run `k6 run --vus <VUS> bench/mark.ts` with `<VUS>` being the number of parallel executions.
3. Wait 30 seconds and voilà!

## Docs of different runtimes

- [amphp.org](https://amphp.org/http-server)
- [frankenphp.org](https://frankenphp.dev/docs/worker/)
- [reactphp.org](https://reactphp.org/http/#server-usage)
- [docs.roadrunner.dev](https://docs.roadrunner.dev/docs/general/quick-start)
- [openswoole.com](https://openswoole.com/docs/modules/swoole-http-server-doc)
