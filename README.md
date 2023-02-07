# Event tracker

Package for tracking any custom events and logging them to influxdb or prometheus in Laravel framework.

## Installation

- Install

```shell
composer require umbrellio/event-tracker
```

## Features

- Out of box it has few ready trackers:
    - Response time - middleware for logging response-time of different endpoints
    - Jobs duration - set of listeners for logging execution time of all jobs
    - Jobs log - set of listeners for logging all executed jobs
- Log anything you want by multipurpose classes - EventRepository and PrometheusRepository
- Log into influxdb directly or by telegraf
- Prometheus support

## Known issues

- Prometheus integration. If your app deployed to kubernetes and it has more than one pod, where Prometheus gets metrics, then your metrics will be doubled. The reason it happens is one storage for metrics (Redis). Prometheus requests metrics from every fpm pod and they give same metrics, which prometheus sums up. 

## Integration

### General

1. Execute `php artisan vendor:publish` for publishing config example in your `config` directory
2. Write your credentials and settings in config. If you don't need some connections or trackers you can delete them.

## Trackers

### Response time tracker

This tracker collects information about time, that your application spends for handle request on different endpoints.

All you need for integrate this tracker is include a middleware - `ResponseTimeTrackerMiddleware` in your route's list.
This middleware has to be last one in your routes. Use Laravel's feature

- [Sorting middleware](https://laravel.com/docs/9.x/middleware#sorting-middleware).

> **Influx**
>
> Records have follow format: `app_prefix.response_time,urlName=domain.com/path1 val=1608201916"`, where `url` is tag's
> name, and val is field's name.

> **Prometheus**
>
> - Buckets can be configured in config
> - Metrics have follow format: app_prefix_response_time_bucket{namespace="app-nc",urlName="/",le="1"} 1

You can change measurement name instead of 'response_time' in config at `trackers` block. All trackers have this
opportunity.

### Jobs duration tracker

This one collects metrics about duration time of jobs in your application.

> **Influx**
>
> This tracker will write records in follow format: `app_prefix.jobs_duration,jobName=App\Example\JobName val=160820191`
> .

> **Prometheus**
>
> - Buckets can be configured in config
> - Metrics have follow format: app_prefix_jobs_duration_bucket{namespace="app-ns",jobName="App\\Jobs\\JobName",le="1"}
    2

This tracker allows you to skip any jobs, you don't want to track via config.

### Jobs log

This one collects information about amount of each kind of jobs in application.

> **Influx**
>
> Format of writing will be: `app_prefix.jobs_log,event=processing,jobName=App\Example\JobName`

> **Prometheus**
>
> Metrics have follow format: app_prefix_jobs_log{namespace="app-ns",jobName="App\\Jobs\\JobName",eventName="processed"}
> 2

You can skip any jobs similarly like in previous tracker.

### External api response

This one collects information about requests, that your application sends to external apis.

For integrate this tracker you need create all GuzzleClient's instances with config `on_stats`.
During creating GuzzleClient in your ServiceProvider you need write something like:

```
$callbackCreator = app(Umbrellio\EventTracker\Trackers\ExternalApiResponse\GuzzleClientOnStatsCallbackCreator);
$client = new GuzzleHttp\Client(['on_stats' => $callbackCreator->create()]);
```

In this case you can modify client as you want. But if you dont need it - you can create new ServiceProvider and use
tracker's binder:

```
class GuzzleClientServiceProvider extends ServiceProvider
{
    public function boot(Umbrellio\EventTracker\Trackers\ExternalApiResponse\GuzzleClientBinder $binder): void
    {
        $binder->bind($this->app);
    }
}
```

> **Influx**
>
> Format of writing will
>
be: `app_prefix.external_api_response,urlName=https://api.domain.com/endpoint/edit,host=api.domain.com,status=200,total_time=1.0,connect_time=0.5,namelookup_time=0.5 val=1.0`

> **Prometheus**
>
> - Buckets can be configured in config
> - Metrics have follow format: app_prefix_external_api_response_bucket{namespace="app-ns",host="domain.com"
    ,status=200,urlName="/",le="2"} 1

You can configure last three tags in `metrics` option, if you don't need something. Beside of this tags you can specify
any fields from handlerStats attribute in GuzzleHttp\TransferStats object.

By default, option `group_redirects_in_one_request` is set in `false`. It means **every** request will be tracked. Even
if it was response with redirect status code.
However if you want track common time of response, set this option to `true`.

Field `val` can be configured by option `main_metric`. This field must be one of list in `metrics` option.

### Custom trackers

#### Influx

You can create your own wrap for EventRepository class and log any custom events from your app.
Pass `timestamp` attribute in `write` method for use specific time of event instead of current one. Use only nanoseconds
for specify time. For example - 1612369449000000000.

#### Prometheus

Similarly, like in Influx case, you can use PrometheusRepositoryContract for write custom metrics.
There are methods for each type of Prometheus metrics: counter, gauge, histogram, summary.

For disable all trackers - use `enabled` field in package config.

## Connections

### Influx

This connection allows you to send event directly in influx db. This way is not recommended, because there is can be
problems with performance.

### Telegraf

This connection just like previous one, but all events is sent to Telegraf daemon. This way is faster and more optimized
than previous one.

### Prometheus

This connection uses Redis for temporary storing metrics. All your events will be written to redis.
All metrics can be fetched by `/metrics` endpoint (it will be automatically added to your routes). You can configure
endpoint name.
