// ---- Modules ----

var finalhandler = require('finalhandler'),
    http = require('http'),
    serveStatic = require('serve-static');

// ---- Local Variables ----

var serve = serveStatic('examples');

// ---- HTTP Server ----

http.createServer(function (req, res) {
  var done = finalhandler(req, res);
  serve(req, res, done);
}).listen(3000);