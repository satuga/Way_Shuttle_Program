var static = require('node-static');
var http = require('http');
var Person = require('./person.js');
var ConfigDetail = require('./config.js');
var mysql = require("mysql");
var moment = require("moment");
var getConnection = require('./db');
/*var pool = mysql.createPool({
    connectionLimit: ConfigDetail.connectionLimit,
    host: ConfigDetail.host,
    user: ConfigDetail.user,
    password: ConfigDetail.password,
    database: ConfigDetail.database,
    debug: ConfigDetail.debug,
    queueLimit:0,
    acquireTimeout: 1000000,
    waitForConnection: true,
    dateStrings: true,
    connectionLimit: 400
});*/
var mainCalculateETAForStops = require('./function.js');
//var hashDriverDetail = {};
var hashMapParkings = {};
var obj = {trackData: []};
var people = [];
var sockets = [];
var insertedLogID = 0;
var current_datetime = moment().format('YYYY-MM-D h:mm:ss');g
var _ = require('underscore')._;
var app = http.createServer(function (req, res) {
    res.writeHead(200, {'Content-Type': 'text/plain'});
    res.end('You are using Nodejs With Socket connection.');
}).listen(process.env.PORT || 3000);

app.listen(3000, function () {
    console.log('App listen port number: 3000, DateTime: '+current_datetime);
});

var io = require('socket.io').listen(app);
io.sockets.on('connection', function (socket) {
    console.log('Connected with socket: '+socket.id);
    console.log('Connect socket date and time: '+current_datetime);
    socket.on('joinRoom', function (room_ids, userid, type, device_type) {
        room_ids = room_ids+ '';
                var splitRoom = room_ids.split(',');
                for (var i = 0; i < splitRoom.length; i++) {
                   if (device_type !== 'website') {
                //InsertDriverLog(socket.id, room_ids, splitRoom[i], userid, type);
                var person = new Person(socket.id, userid, room_ids, type, '');
                people.push(person);
                try{
                  updateShuttleStatusAsOnline(splitRoom[i], type);
                }catch(e){
                  console.log(e);
                }
            }
            console.log('Socket joined userid: ' + userid);
            console.log('Socket joined userid time: ' +current_datetime);
            socket.join(splitRoom[i]);
            sockets.push(socket);
    }
    try{
      updateDriverStatus(userid, type, 1);
    }catch(e){
      console.log('Update driver Status when socket join try and catch error: '+e+' current time: ' +current_datetime);
    }
    });

    socket.on('track', function (message) {
        //var person = _.findWhere(people, {'id': socket.id});
        //console.log('Tracked1: ' + JSON.stringify(person));
        /*var driverType = message.type;
        if(driverType == 'Driver' || driverType == 'driver' || driverType == 'DRIVER'){
            if (typeof hashDriverDetail[message.user] === 'undefined') {
                hashDriverDetail[message.user] = [message];
            } else {
                hashDriverDetail[message.user].push(message);
            }
        }*/
    message.room = message.room+ '';
        var splitRoom = message.room.split(',');
        //obj.trackData.push(message);
        console.log("====================start=========================");
        console.log(current_datetime);
        console.log(message);
        console.log("======================end=======================");
        for (var i = 0; i < splitRoom.length; i++) {            
            var userType = message.type;
            //console.log("userType: "+userType);
            if (userType == 'Driver') {
                //var MainCalculateETAForStops = new mainCalculateETAForStops(message);
                var MainCalculateETAForStops = new mainCalculateETAForStops.mainCalculateETAForStops(message);
        //message['StopsEtaInfo'] = MainCalculateETAForStops.StopsEtaInfo;
        io.sockets.in(splitRoom[i]).emit('tracked', message);
            } else {
                
        io.sockets.in(splitRoom[i]).emit('tracked', message);
        }
        }
    });

    socket.on('disconnect', function () {
        var o = _.findWhere(sockets, {'id': socket.id});
        var person = _.findWhere(people, {'id': socket.id});
        console.log('Socket disconnected date and time:' +current_datetime);
        console.log('When socket disconnected - socketid: ' + socket.id);
        console.log('When socket disconnected find person: ' + JSON.stringify(person));        
        if (typeof person !== 'undefined') {
        person.name = person.name+ '';
            var splitRoom = person.name.split(',');
            for (var i = 0; i < splitRoom.length; i++) {
                io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: person.userid, type: person.type});
                socket.leave(splitRoom[i]);

                //var getinfobyuser = getObjects(obj.trackData, 'user', person.userid);
                //var getinfobyuser = hashDriverDetail[person.userid];
                //var jsonDetail = JSON.stringify(getinfobyuser);
                //UpdateDriverLog(person.userid, person.type, person.insertedLogID, splitRoom[i], jsonDetail);

                try{
                   updateShuttleStatusAsOffline(splitRoom[i], person.type);
                }catch(e){
                   console.log('Update driver Status when socket disconnectd try and catch error: '+e+' date and time: '+current_datetime);
                }
                console.log('When socket disconnectd user left the room ' + splitRoom[i]);
            }
            if (person.type == 'Driver' || person.type == 'driver' || person.type == 'DRIVER'){
              if (typeof person.userid !== 'undefined') {
                try{
                  deleteDriverFromDB(person.userid);
                }catch(e){
                  console.log('Update driver delete from databases when socket disconnectd try and catch error: '+e+' date and time: '+current_datetime);
                }
              }
              new mainCalculateETAForStops.leaveDriverInformation(person.userid);
            }

            try{
               updateDriverStatus(person.userid, person.type,0);
            }catch(e){
              console.log('Update Driver status when socket disconnectd try and catch error: '+e+' date and time: '+current_datetime);
            }

            var i = people.indexOf(person.id);
            if (i != -1) {
                people.splice(i, 1);
            }
            people = _.without(people, person);
        }
        sockets = _.without(sockets, o);
    });
    socket.on('leave', function (room, userid, type) {
        var o = _.findWhere(sockets, {'id': socket.id});
        var person = _.findWhere(people, {'id': socket.id});
        if (typeof person !== 'undefined') {
        person.name = person.name+ '';
            var splitRoom = person.name.split(',');
            for (var i = 0; i < splitRoom.length; i++) {
                io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: person.userid, type: person.type});
                socket.emit('left', {room: splitRoom[i], userid: userid, type: type});
                socket.leave(splitRoom[i]);
                console.log('Driver leave socket date and time: ' + current_datetime);
                console.log('Driver leave socket: ' + splitRoom[i]);
                //console.log('-----------------------------');
                //console.log(hashDriverDetail);
                //console.log('-----------------------------');
                //var getinfobyuser = getObjects(obj.trackData, 'user', userid);
                //var getinfobyuser = hashDriverDetail[userid];
                //var jsonDetail = JSON.stringify(getinfobyuser);
                //UpdateDriverLog(person.userid, person.type, person.insertedLogID, splitRoom[i], jsonDetail);
                try{
                  updateShuttleStatusAsOffline(splitRoom[i], person.type);
                }catch(e){
                  console.log('Update driver status as offline when socket leave try and catch error: '+e+' date and time: '+current_datetime);
                }

            }
            if (person.type == 'Driver' || person.type == 'driver' || person.type == 'DRIVER'){
              if (typeof person.userid !== 'undefined') {
                try{
                  deleteDriverFromDB(person.userid);
                }catch(e){                  
                  console.log('Delete driver from db when socket leave try and catch error: '+e+' date and time: '+current_datetime);
                }
              }
              new mainCalculateETAForStops.leaveDriverInformation(person.userid);
            }
            try{
               updateDriverStatus(person.userid, person.type,0);
            }catch(e){
               console.log('Update driver from db when socket leave try and catch error: '+e+' date and time: '+current_datetime);
            }
            var i = people.indexOf(person.id);
            if (i != -1) {
                people.splice(i, 1);
            }
            //people.splice(person.id);
            people = _.without(people, person);
        }
        sockets = _.without(sockets, o);
    });
    socket.on('uncaughtException', function (exception) {
      // handle or ignore error
       console.log("uncaughtException date and time: "+current_datetime);
       console.log(exception);
    });
    /*socket.on('leave', function (room, userid, type) {
     var splitRoom = room.split(',');
     for (var i = 0; i < splitRoom.length; i++) {
     io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: userid, type: type});
     socket.emit('left', {room: splitRoom[i], userid: userid, type: type});
     socket.leave(splitRoom[i]);
     console.log('There is someone left the room ' + splitRoom[i]);
     updateShuttleStatusAsOffline(splitRoom[i], type);
     }
     var o = _.findWhere(sockets, {'id': socket.id});
     var person = _.findWhere(people, {'id': socket.id});
     if (typeof person !== 'undefined') {
     people.splice(person.id);
     people = _.without(people, person);
     }
     var splitRoom = person.name.split(',');
     for (var i = 0; i < splitRoom.length; i++) {
     io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: person.userid, type: person.type});
     socket.emit('left', {room: splitRoom[i], userid: userid, type: type});
     console.log('There is someone left the room ' + splitRoom[i]);
     var getinfobyuser = getObjects(obj.trackData, 'user', userid);
     var jsonDetail = JSON.stringify(getinfobyuser);
     UpdateDriverLog(person.userid, person.type, person.insertedLogID, splitRoom[i], jsonDetail);
     }
     sockets = _.without(sockets, o);
     });*/

});
function deleteDriverFromDB(DriverID){
    console.log('Delete driver date and time: '+current_datetime);
  getConnection(function (err, con) {
      if (err) {
          console.log('Delete driver db error: '+err+' date and time: '+current_datetime);
          if (typeof con !== 'undefined') {
              con.release();
          }
          return;
      } else {
        con.query('SELECT * FROM tbl_shuttle_googlecall_eta where SGE_DriverID = ? limit 1', [DriverID], function (err, rows) {
            if (err) {
                console.log('Delete driver db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                if (rows != '') {
                    con.query('delete from tbl_shuttle_googlecall_eta where SGE_DriverID = ? ', [DriverID], function (err, results) {
                        if (err) {
                            console.log('Delete driver db error: '+err+' date and time: '+current_datetime);
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                            return;
                        } else {
                            if (results) {
                                if (typeof con !== 'undefined') {
                                    con.release();
                                }
                            }
                        }
                    });
                }
            }
        });
      }
    });
    }

function updateShuttleStatusAsOnline(room_id, type) {
    console.log('Update Shuttle Status As Online date and time: '+current_datetime);
    var deferred = '';
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        if (hashMapParkings[room_id]) {
            valForParkCount = hashMapParkings[room_id] = parseInt(hashMapParkings[room_id]) + 1;
        } else {
            valForParkCount = 1;
        }
        hashMapParkings[room_id] = valForParkCount;

        getConnection(function (err, con) {
            if (err) {
                console.log('Update Shuttle Status As Online db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                con.query('SELECT * FROM tbl_shuttle_active_status where SAS_PatkingID = ? limit 1', [room_id], function (err, rows) {
                    if (err) {
                        console.log('Update Shuttle Status As Online db error: '+err+' date and time: '+current_datetime);
                        if (typeof con !== 'undefined') {
                            con.release();
                        }
                        return;
                    } else {
                        
            console.log('Update Shuttle Status As Online: '+current_datetime);
                        if (rows == '') {
                            con.query('insert into tbl_shuttle_active_status set SAS_PatkingID = "' + room_id + '", SAS_ShuttleActive_LastTime = "' + current_datetime + '", SAS_ShuttleActive_LastTime_1Hour = "' + current_datetime + '",SAS_ShuttleActive_LastTime_2Hour = "' + current_datetime + '", SAS_ShuttleActive_LastTime_3Hour = "' + current_datetime + '", SAS_ShuttleActive = "1"', function (err, rows) {
                                if (err) {
                                    console.log('Update Shuttle Status As Online db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {
                                        deferred = rows.insertId;
                                        console.log('Insert driver Shuttle Status detail As Online: '+deferred);
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        } else {
                            deferred = rows[0]['SAS_ActiveID'];
                            con.query('update tbl_shuttle_active_status set SAS_ShuttleActive_LastTime = "' + current_datetime + '", SAS_ShuttleActive_LastTime_1Hour = "' + current_datetime + '",SAS_ShuttleActive_LastTime_2Hour = "' + current_datetime + '", SAS_ShuttleActive_LastTime_3Hour = "' + current_datetime + '", SAS_ShuttleActive = "1" where SAS_ActiveID = "' + deferred + '"', function (err, rows) {
                                if (err) {
                                    console.log('Update Shuttle Status As Online db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {
                                        console.log('Update driver Shuttle Status detail As Online: '+deferred);                                        
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });
    }
}

function updateShuttleStatusAsOffline(room_id,type ,id) {
  console.log('Update Shuttle Status As offline date and time: '+current_datetime);
    var deferred = '';
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        
        if (hashMapParkings[room_id]) {
            hashMapParkings[room_id] = parseInt(hashMapParkings[room_id]) - 1;
        }
        if (hashMapParkings[room_id] !== 0) {
            return;
        }

        getConnection(function (err, con) {
            if (err) {
                console.log('Update Shuttle Status As offline db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                con.query('SELECT * FROM tbl_shuttle_active_status where SAS_PatkingID = ? limit 1', [room_id], function (err, rows) {
                    if (err) {
                         console.log('Update Shuttle Status As offline db error: '+err+' date and time: '+current_datetime);
                        if (typeof con !== 'undefined') {
                            con.release();
                        }
                        return;
                    } else {
                 // console.log('Update Shuttle Status As Offline: '+current_datetime);
                        if (rows) {
                            deferred = rows[0]['SAS_ActiveID'];
                            con.query('update tbl_shuttle_active_status set SAS_ShuttleActive_LastTime = "' + current_datetime + '", SAS_ShuttleActive_LastTime_1Hour = "' + current_datetime + '",SAS_ShuttleActive_LastTime_2Hour = "' + current_datetime + '", SAS_ShuttleActive_LastTime_3Hour = "' + current_datetime + '", SAS_ShuttleActive = "0" where SAS_ActiveID = "' + deferred + '"', function (err, rows) {
                                if (err) {
                                    console.log('Update Shuttle Status As offline db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {                                        
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {                   
                                        console.log('Shuttle Status Updated as offline detail: ' + deferred);
                                        //console.log(id);
                                         if(hashParkingsMailsendtime[room_id] != current_datetime){                                           
                                            con.query('SELECT tbl_registeration.email_add FROM tbl_parking INNER JOIN tbl_registeration ON tbl_parking.P_UserID = tbl_registeration.id where tbl_parking.P_ID > 0 And tbl_parking.P_ID = ? limit 1', [room_id], function (err, rows) {
                                                 if (err) {
                                                     console.log('vendor send mail detail not find db error: '+err+' date and time: '+current_datetime);
                                                    if (typeof con !== 'undefined') {
                                                        con.release();
                                                    }
                                                    return;
                                                } else {                                               
                                                if (rows) {
                                                    vendor_email = rows[0]['email_add']; 
                                                    con.query('SELECT * FROM tbl_registeration where id = ? limit 1', [id], function (err, rows) {
                                                        if (err) {
                                                             console.log('user send mail detail not find db error: '+err+' date and time: '+current_datetime);
                                                            if (typeof con !== 'undefined') {
                                                                con.release();
                                                            }
                                                            return;
                                                        } else {                                               
                                                            if (rows) {   
                                                                drivername = rows[0]['firstname']+' '+rows[0]['lastname'];                                                
                                                                //send mail to owner
                                                                 mailOptions = {
                                                                    from: 'dipika.patel@letsnurture.com', 
                                                                    to: vendor_email, 
                                                                    subject: 'Stg.way.com - Shuttle Status As offline - '+current_datetime,      
                                                                    html: drivername +' is offline at ' + current_datetime
                                                                };
                                                                // send mail with defined transport object
                                                                transporter.sendMail(mailOptions, (error, info) => {
                                                                    if (error) {
                                                                        return console.log(error);
                                                                    }
                                                                    console.log('Message sent: ', info.messageId, info.response);
                                                                    hashParkingsMailsendtime[room_id] = current_datetime;                                              
                                                                }); 
                                                            }
                                                         }                                            
                                                    });                                                    
                                                 }}
                                            });                                            
                                         }                                        
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        } else {
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                        }
                    }
                });
            }
        });
    }
}

function updateDriverStatus(driver_id, type,driverStatus) {
    console.log('Update driver Status date and time: '+current_datetime);
    var deferred = '';
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        
        getConnection(function (err, con) {
            if (err) {
                console.log('Update driver Status db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                con.query('SELECT * FROM tbl_shuttle_driver_active_status where SAS_DriverID = ? limit 1', [driver_id], function (err, rows) {
                    if (err) {
                        console.log('Update driver Status db error: '+err+' date and time: '+current_datetime);
                        if (typeof con !== 'undefined') {
                            con.release();
                        }
                        return;
                    } else {
                        console.log('Update Driver Status change time: '+current_datetime);
                        if (rows != '') {
                            deferred = rows[0]['SAS_DriverActiveID'];
                            con.query('update tbl_shuttle_driver_active_status set SAS_DriverActive_LastTime = "' + current_datetime + '", SAS_DriverActiveStatus = "'+driverStatus+'" where SAS_DriverActiveID = "' + deferred + '"', function (err, rows) {
                                if (err) {
                                    console.log('Update driver Status db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {
                                        console.log('Update Driver Status detail: '+deferred);                                        
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        } else {
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                        }
                    }
                });
            }
        });
    }
}

function InsertDriverLog(socket_id, room_ids, room_id, userid, type) {
    var deferred = '';
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        
        created = moment().format('YYYY-MM-D');        
        try{
        getConnection(function (err, con) {
            if (err) {
                console.log('Insert driver log db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {                
                con.query('SELECT * FROM tbl_shuttle_driver_log where SDL_DriverID = ? and SDL_ParkingID = ? and SDL_CreatedDateTime = ? limit 1', [userid, room_id, created], function (err, rows) {
                    if (err) {
                        console.log('Insert driver log db error: '+err+' date and time: '+current_datetime);
                        if (typeof con !== 'undefined') {
                            con.release();
                        }
                        return;
                    } else {
                        if (rows == '') {
                            con.query('insert into tbl_shuttle_driver_log set SDL_DriverID = "' + userid + '", SDL_ParkingID = "' + room_id + '", SDL_JSONData = "", SDL_StatrtDateTime = "' + current_datetime + '", SDL_CreatedDateTime = "' + created + '"', function (err, rows) {
                                if (err) {
                                    console.log('Insert driver log db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {
                                        deferred = rows.insertId;
                                        var person = new Person(socket_id, userid, room_ids, type, deferred);
                                        people.push(person);
                                        console.log('Insert driver log detail in socket array datetime: '+current_datetime);
                                        console.log('Insert driver log detail in socket array: '+people);                                        
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        } else {
                            deferred = rows[0]['SDL_LogID'];
                            var person = new Person(socket_id, userid, room_ids, type, deferred);
                            people.push(person);                            
                            console.log('Insert driver detail in socket array: '+people);
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                        }
                    }
                });
            }
        });
     }catch(e){
       console.log(e);
     }
    } else {
        var person = new Person(socket_id, userid, room_ids, type, deferred);
        people.push(person);
    }
}
function UpdateDriverLog(SDL_DriverID, type, insertedLogID, room_id, jsonDetail) {
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {       

        created = moment().format('YYYY-MM-D');
        
        getConnection(function (err, con) {
            if (err) {
                console.log('Update driver log db error: '+err+' date and time: '+current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                con.query('SELECT * FROM tbl_shuttle_driver_log where SDL_DriverID = "' + SDL_DriverID + '" and SDL_ParkingID = "' + room_id + '"  and SDL_LogID = "' + insertedLogID + '" limit 1', function (err, rows) {
                    if (err) {
                        console.log('Update driver log db error: '+err+' date and time: '+current_datetime);
                        if (typeof con !== 'undefined') {
                            con.release();
                        }
                        return;
                    } else {
                        if (rows != '') {
                            con.query('update tbl_shuttle_driver_log set SDL_JSONData = "' + mysql_real_escape_string(jsonDetail) + '", SDL_EndDateTime = "' + current_datetime + '" where SDL_DriverID = "' + SDL_DriverID + '" and SDL_ParkingID = "' + room_id + '" and SDL_LogID = "' + insertedLogID + '"', function (err, rows) {
                                if (err) {
                                    console.log('Update driver log db error: '+err+' date and time: '+current_datetime);
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                    return;
                                } else {
                                    if (rows) {
                                        console.log('Shuttle driver log updated datetime: ' +current_datetime);
                                        console.log('Shuttle driver log updated: ' + SDL_DriverID);                                        
                                    }
                                    if (typeof con !== 'undefined') {
                                        con.release();
                                    }
                                }
                            });
                        } else {
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                        }
                    }
                });
            }
        });
    }
}

function mysql_real_escape_string(str) {
    if (typeof str !== 'undefined') {
        return str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function (char) {
            switch (char) {
                case "\0":
                    return "\\0";
                case "\x08":
                    return "\\b";
                case "\x09":
                    return "\\t";
                case "\x1a":
                    return "\\z";
                case "\n":
                    return "\\n";
                case "\r":
                    return "\\r";
                case "\"":
                case "'":
                case "\\":
                case "%":
                    return "\\" + char; // prepends a backslash to backslash, percent,
                    // and double/single quotes
            }
        });
    } else {
        return '';
    }
}

//return an array of objects according to key, value, or key and value matching
function getObjects(obj, key, val) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i))
            continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getObjects(obj[i], key, val));
        } else
        //if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
        if (i == key && obj[i] == val || i == key && val == '') { //
            objects.push(obj);
        } else if (obj[i] == val && key == '') {
            //only add if the object is not already in the array
            if (objects.lastIndexOf(obj) == -1) {
                objects.push(obj);
            }
        }
    }
    return objects;
}

//return an array of values that match on a certain key
function getValues(obj, key) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i))
            continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getValues(obj[i], key));
        } else if (i == key) {
            objects.push(obj[i]);
        }
    }
    return objects;
}

//return an array of keys that match on a certain value
function getKeys(obj, val) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i))
            continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getKeys(obj[i], val));
        } else if (obj[i] == val) {
            objects.push(i);
        }
    }
    return objects;
}
