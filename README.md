# NiceHTTP

A lot of times in our PHP applications we have the need to make a request to an external service.
Oftentimes, we end up using things like `file_get_contents`, but there are a few drawbacks:

* There is no easy way to write tests that use your code without actually making calls to the external service
* Its ugly
* It doesn't work nicely with verbs other than `GET`.

_NiceHTTP_ aims to solve this problem by introducing a clean DSL in front of sockets, and allows you to write tests around the connections you make by stubbing out responses.

---

## Simple Requests

In its most basic use, NiceHTTP rolls like this:

    $response = NiceHTTP::get('http://www.google.com/')
    $response->status // 200

    $response = NiceHTTP::post('http://localhost:9393/', 'name=hello', array('Authorization' => 'xxx'));
    $response->status // 200
    $response->body // [String]

You also get the option to build your requests more methodically (builder):

    $request = new NiceHTTP\GetRequest();
    $request->setUrl('http://www.google.com/')->addHeader('Authorization' => 'xxx');
    $response = $request->send();

---

## Easy Testing

A large inspiration for this library existing was the ability to test resources you make calls to.  The way we do this is by registering matchers:

    NiceHTTP::register(function($request) {
        if ($request->isGET() && $request->hasPath('/')) return NiceHTTP\BasicResponse(200, 'hello world');
    });

When you make a request inside of your application, the first things that happens is we run through all of the matchers.  The first one that's a match, we return the result of as the response.  We have the convenience class `BasicResponse` to use here.

    new NiceHTTP\BasicResponse(code, body, headers);

This way we can write tests that's don't depend on actually hitting the service it uses.  There's all kinds of reasons you'd want to do this.

You get some helpers for free to make your testing easier:

* `isGet()` - Return true if the request was a GET
* `isPut()` - Return true if the request was a PUT
* `isPost()` - Return true if the request was a POST
* `isDelete()` - Return true if the request was a DELETE
* `hasPath($path)` - Return true if the request has an exact path
* `hasPathLike($regex)` - Returns true if the request has a path matching a pattern
* `hasHost($host)` - Return true if the request has an exact host
* `hasHostLike($regex)` - Returns true if the request has a path matching a pattern
* `hasPort($port)` - Return true if the request has a given port
* `hasHeader($name, $value = null)` - Return true if the header exists, (and has a certain value if $value is given)

---

## Never Connecting

If none of your matchers match the given request - then it will fall through and make the actual request.  If you want to stop that from happening in your tests (which I fully recommend) - you can just this in your `setUp()`:

    NiceHTTP::disallowExternalConnections();

---

### Requirements

NiceHTTP uses namespaces and anonymous functions, so you'll need `PHP >= 5.3`
Time to upgrade if you haven't already - there's a lot of cool stuff there waiting.

---

### Author

John Crepezzi - [john.crepezzi@gmail.com](mailto:john.crepezzi@gmail.com)

---

### License

MIT License (attached)
