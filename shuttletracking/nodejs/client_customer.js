var http = require('http');
var fs = require('fs');
var winston = require('winston');
const nodemailer = require('nodemailer');
var io = require('socket.io-client');
var moment = require('moment');
var smtpTransport = require('nodemailer-smtp-transport');
var Service = require('node-windows').Service;
var max_socket_reconnects = 5;

var allsocketobj = {};

var totalArray = [];
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631"});
//Create a new service object
var svc = new Service({
  name:'Shuttle tracking Service',
  description: 'Shuttle tracking services',
  script: 'C:\\Apache24\\htdocs\\responsive\\shuttletracking\\nodejs\\server.js'
});

var totalCustomers = 500; // add long lat information based on customer count
var howManyTimeRepeat = 50;
var globalroom = '1010';

winston.configure({
	transports: [
	  new (winston.transports.File)({ filename: 'somefile.log' })
	]
});
for(var u=0;u<totalCustomers;u++){
    socket = socket+u;
    var socket = io.connect('https://stg.way.com/',{
						path: '/node/socket.io',
            reconnection: true,
            reconnectionDelay: 10000,
            reconnectionAttempts: max_socket_reconnects
    });

    var datetime = moment().format('YYYY-MM-D h:mm:ss a');
    // Add a connect listener

    socket.on('connect', function (socket) {

            console.log('Socket is Connected datetime: '+datetime);
            winston.info('Socket is connected: '+datetime);

    });
socket.emit('joinRoom', globalroom, u, 'Customer');
    allsocketobj[u] = socket;
}
console.log(allsocketobj);
function sendmsg(totalArray)
{
    (function () {
        var i = 0,
                action = function () {
                    for(var r=0;r<totalCustomers;r++){
                        socket = allsocketobj[r];
                        customerid = r;
                        lat = totalArray[r % 50].lat;
                        lon = totalArray[r % 50].lon;
                        var current_datetime = moment().format('YYYY-MM-D h:mm:ss');
                        var customerDetail = {"current_timestamp": current_datetime, "user_image": "", "user_name": "Ujash Joshi"+customerid, "Is_Customer_Visible": "1", "speed": "0.0", "lng": lon, "type": "Customer", "user": customerid, "heading": "0.0", "lat": lat, "room": globalroom}
                        console.log(customerDetail);
                        socket.emit('track', customerDetail);
                    }
                    i++;
                    if (i < howManyTimeRepeat) { //46
                        setTimeout(action, 1000);
                    } else {
                        //socket.emit('leave', globalroom, customerid, "Customer");
                        i = howManyTimeRepeat;
                        setTimeout(action, 1000);
                    }
                };

        setTimeout(action, 1000);

    })();

}

sendmsg(totalArray);
