# PHP Runtime Benchmarks: HTTP Client

Let's have a look at HTTP Client performance with all these runtimes!

This whole benchmark is oriented on ["Which Http Client is Faster for Web Scraping" by Insolita from May 2020](https://dev.to/insolita/which-http-client-is-faster-for-web-scraping-c95), but adds more runtimes to the game.

## Featuring...

- AMPHP
- ReactPHP
- Swoole

If you want to see other alternatives, please let me know!

## Results

During the benchmark, the servers handled as many requests as they can in a fixed amount of time and with different amounts of concurrent requests. 
The servers respond with a simple `"Hello, world!"` and a `Content-Type: text/plain` header as well as a status code 200.
The following numbers have been measured/calculated by *k6*:

<details>
<summary>Raw numbers</summary>

|Runtime|Concurrency|Requests per Second|Failed Request Ratio (%)|
|-------|--:|------------------:|--------------------------|------------------------|
|Vanilla PHP (`file_get_contents`)|-|6,306|0|
|AMPHP (amphp/http-server@3.4.2)|100|3,250|0|
|ReactPHP (react/http@1.11.0)|100|8,768|0|
|Swoole (ext-openswoole@25.2.0)|100|40,188|0|

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

- [amphp.org](https://amphp.org/http-client)
- [reactphp.org](https://reactphp.org/http/#client-usage)
- [openswoole.com](https://openswoole.com/docs/modules/swoole-http-server-doc)
