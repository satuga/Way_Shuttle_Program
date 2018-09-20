var Service = require('node-windows').Service;

// Create a new service object
var svc = new Service({
  name:'Shuttle tracking Service',
  description: 'Shuttle tracking services',
  script: 'E:\\Apache24\\htdocs\\responsive\\shuttletracking\\nodejs\\server.js'
});

// Listen for the "install" event, which indicates the
// process is available as a service.
svc.on('install',function(){
  svc.start();
});

svc.install();