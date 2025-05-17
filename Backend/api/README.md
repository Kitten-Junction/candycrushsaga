## Candy Crush Saga | `/backend/api/`

This folder contains all API routing files, each route request is sent to individual files (each one of them having different methods), then passed to the JSON-RPC server, which processes the method by calling the required modules and returning the response

Since the server is now highly dependent on King's ClientApi server, most of the API files here contain only a single function that calls the JSON-RPC service and returns its response, older versions of the codebase used poorly written functions to process the responses

Please migrate any APIs that do not use `callJsonRpc` to either `/rpc/ClientApi` or `/rpc/JsonRpcTest` to keep the codebase clean and readable

Here’s an example of how to use `callJsonRpc`:

```
<?php
$argumentX = $_GET['argX'];
$sessionKey = $_GET['_session'];

$result = callJsonRpc('METHOD.SUBMETHOD', [$argument0], $sessionKey); // Returns the response from the RPC server

// callJsonRpc has more arguments which are not shown here, please go to common.php if you want to learn more about them!
?>
```

Made with love by idkwhattoput.

