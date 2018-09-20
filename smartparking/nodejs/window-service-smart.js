var Service = require('node-windows').Service;

// Create a new service object
var svc = new Service({
  name:'Smart parking Service',
  description: 'Smart parking services',
  script: 'E:\\Apache24\\htdocs\\responsive\\smartparking\\nodejs\\smart.js'
});

// Listen for the "install" event, which indicates the
// process is available as a service.
svc.on('install',function(){
  svc.start();
});

svc.install();