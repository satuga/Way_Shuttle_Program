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

var globalvalue = 20;
var globalroom = '1010';

winston.configure({
	transports: [
	  new (winston.transports.File)({ filename: 'somefile.log' })
	]
});
for(var u=1;u<=globalvalue;u++){
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
socket.emit('joinRoom', globalroom, u, 'website');
    socket.on('tracked', function (data) {
        var userID = data.user;
        var userType = data.type;
        hashGlobalTripID = data.DTP_DLG_DriverTripLogID;
				if (userType == 'Driver') {
        	console.log(data);
				}
    });

    allsocketobj[u] = socket;
}
