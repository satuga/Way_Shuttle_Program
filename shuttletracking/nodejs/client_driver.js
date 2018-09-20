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
totalArray.push({'lat': "23.076068", 'lon': "72.529325", 'heading': "358.828800713229", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.076418", 'lon': "72.529437", 'heading': "16.4041604634259", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.076883", 'lon': "72.529561", 'heading': "13.7840470387713", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.077213", 'lon': "72.529631", 'heading': "11.0423212586258", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.077500", 'lon': "72.529708", 'heading': "13.8647924084812", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.077866", 'lon': "72.529813", 'heading': "14.7847694759211", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.077964", 'lon': "72.529888", 'heading': "35.1478710765529", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.077960", 'lon': "72.530037", 'heading': "91.6714691352242", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077938", 'lon': "72.530336", 'heading': "94.5727331687107", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077908", 'lon': "72.530690", 'heading': "95.263102138158", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077901", 'lon': "72.531049", 'heading': "91.2141888803159", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077899", 'lon': "72.531542", 'heading': "90.2526550167097", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077899", 'lon': "72.532068", 'heading': "90.0", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077869", 'lon': "72.532497", 'heading': "94.3468755844616", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077879", 'lon': "72.532980", 'heading': "88.7107796671971", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077869", 'lon': "72.533409", 'heading': "91.4514342755375", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077879", 'lon': "72.533795", 'heading': "88.3869585925735", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077865", 'lon': "72.533958", 'heading': "95.3337288762112", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077832", 'lon': "72.534033", 'heading': "115.56061694479", 'Previous_DTP_TripStopID':"173"});
totalArray.push({'lat': "23.077634", 'lon': "72.534039", 'heading': "178.403122026976", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.077411", 'lon': "72.534058", 'heading': "175.518117314357", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.077137", 'lon': "72.534101", 'heading': "171.784634501553", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.076919", 'lon': "72.534086", 'heading': "183.622063893152", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.076652", 'lon': "72.534082", 'heading': "180.789627807001", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.076344", 'lon': "72.534090", 'heading': "178.631140863569", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.076067", 'lon': "72.534081", 'heading': "181.712129140218", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.075892", 'lon': "72.534071", 'heading': "183.009304610807", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.075795", 'lon': "72.534093", 'heading': "168.213949513349", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.075744", 'lon': "72.534083", 'heading': "190.225598222183", 'Previous_DTP_TripStopID':"174"});
totalArray.push({'lat': "23.075709", 'lon': "72.533882", 'heading': "259.282208830244", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075699", 'lon': "72.533692", 'heading': "266.725734854013", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075671", 'lon': "72.533469", 'heading': "262.228252717164", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075618", 'lon': "72.533005", 'heading': "262.922474432818", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075562", 'lon': "72.532510", 'heading': "262.989513555341", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075553", 'lon': "72.531954", 'heading': "268.991994257013", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075535", 'lon': "72.531394", 'heading': "267.998996193399", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075577", 'lon': "72.530814", 'heading': "274.500563535103", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075609", 'lon': "72.530538", 'heading': "277.182862690244", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075672", 'lon': "72.530250", 'heading': "277.182862690244", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075716", 'lon': "72.529941", 'heading': "283.375120861335", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075743", 'lon': "72.529757", 'heading': "279.062409159359", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075761", 'lon': "72.529541", 'heading': "275.17578110602", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075782", 'lon': "72.529397", 'heading': "279.00739105687", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075820", 'lon': "72.529342", 'heading': "306.906500126463", 'Previous_DTP_TripStopID':"175"});
totalArray.push({'lat': "23.075888", 'lon': "72.529329", 'heading': "350.024849706338", 'Previous_DTP_TripStopID':"172"});
totalArray.push({'lat': "23.076068", 'lon': "72.529325", 'heading': "358.828800713229", 'Previous_DTP_TripStopID':"172"});

//Create a new service object
var svc = new Service({
  name:'Shuttle tracking Service',
  description: 'Shuttle tracking services',
  script: 'C:\\Apache24\\htdocs\\responsive\\shuttletracking\\nodejs\\server.js'
});

var globalvalue = 1;
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
socket.emit('joinRoom', globalroom, u, 'Driver');
    allsocketobj[u] = socket;
}
console.log(allsocketobj);
function sendmsg(totalArray)
{
    (function () {
        var i = 0,
                socket = allsocketobj[parseInt(i)+parseInt(1)];
                action = function () {
                    lat = totalArray[i].lat;
                    lon = totalArray[i].lon;
                    heading = totalArray[i].heading;
                    Previous_DTP_TripStopID = totalArray[i].Previous_DTP_TripStopID;

                    for(var r=1;r<=globalvalue;r++){
                        driverid = r;
                        var current_datetime = moment().format('YYYY-MM-D h:mm:ss');
                        var driverDetail = {"current_timestamp": current_datetime, "user_name": "Ankit Solanki"+driverid, "Previous_DTP_TripStopID": Previous_DTP_TripStopID, "speed": "0.0", "lng": lon, "type": "Driver", "user": driverid, "DTP_DLG_DriverTripLogID": "19", "heading": heading, "lat": lat, "room": globalroom}
                        console.log(driverDetail);
                        socket.emit('track', driverDetail);
                    }
                    i++;
                    if (i < 46) { //46
                        setTimeout(action, 1000);
                    } else {
                      i = 0;
                      setTimeout(action, 1000);
                        //socket.emit('leave', globalroom, driverid, "driver");
                    }
                };

        setTimeout(action, 1000);

    })();

}

sendmsg(totalArray);
