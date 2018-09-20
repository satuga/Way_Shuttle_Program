var Service = require('node-linux').Service;

  // Create a new service object
  var svc = new Service({
    name:'linuxservice',
    description: 'Shuttle tracking linux service.',
    script: '/var/www/html/LN_Projects/live/checkout/webservices/shuttletracking/nodejs/server.js',
    // env: {
    //     name: "HOME",
    //     value: process.env["USERPROFILE"]
    //     //service is now able to access the user who created its home directory
    //   }
  });

  // Listen for the "install" event, which indicates the
  // process is available as a service.
  svc.on('install',function(){
    svc.start();
  });

  svc.install();

  
