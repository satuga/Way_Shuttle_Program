var static = require('node-static');
var http = require('http');
var Person = require('./person.js');
var file = new (static.Server)();
var ConfigDetail = require('./config.js');
var mysql = require("mysql");
var pool = mysql.createPool({
    connectionLimit : ConfigDetail.connectionLimit, //important
    host: ConfigDetail.host,
    user: ConfigDetail.user,
    password: ConfigDetail.password,
    database: ConfigDetail.database,
    debug    :  ConfigDetail.debug
});


var fs = require('fs');
var obj = {trackData: []};
var fileName = "";
var d = new Date();
var n = d.getTime();

var sequence = 1;
var clients = [];
var rooms = {};
var people = [];
var sockets = [];
var insertedLogID = 0;

var _ = require('underscore')._;
var app = http.createServer(function (req, res) {
    file.serve(req, res);
}).listen(process.env.PORT || 3000);

app.listen(3000, function () {
    console.log('port 3000');
});

var io = require('socket.io').listen(app);
io.sockets.on('connection', function (socket) {
    console.log('connect');
    socket.on('joinRoom', function (room_ids, userid, type) {
        var splitRoom = room_ids.split(',');
		if(type == 'Driver' || type == 'driver' || type == 'DRIVER') {
			for (var i = 0; i < splitRoom.length; i++) {
				
					InsertDriverLog(splitRoom[i], userid, type, function (err, insertedLogID) {
						socket.join(splitRoom[i]);
					});
			}
			sockets.push(socket);
			var person = new Person(socket.id, userid, room_ids, type, insertedLogID);
			people.push(person);
		} else {
			for (var i = 0; i < splitRoom.length; i++) {
				socket.join(splitRoom[i]);				
			}
			sockets.push(socket);
			var person = new Person(socket.id, userid, room_ids, type, insertedLogID);
			people.push(person);
		}		
    });
//              socket.on('message', function (message) {
//                        console.log('Message: ' + message);
//                        io.sockets.in(socket.room).emit('message', message);
//                });
    socket.on('track', function (message) {
        console.log('Tracked: ' + JSON.stringify(message));
        //var person = _.findWhere(people, {'id': socket.id});
        //console.log('Tracked1: ' + JSON.stringify(person));
        var splitRoom = message.room.split(',');

        for (var i = 0; i < splitRoom.length; i++) {
            io.sockets.in(splitRoom[i]).emit('tracked', message);
        }
		obj.trackData.push(message);
    });
//              socket.on('chat', function (message) {
//                        console.log('Chat: ' + message);
//                        io.sockets.in(socket.room).emit('tracked', message);
//              });
    socket.on('disconnect', function () {
        var o = _.findWhere(sockets, {'id': socket.id});
        var person = _.findWhere(people, {'id': socket.id});
        if (typeof person !== 'undefined') {
            var splitRoom = person.name.split(',');
            for (var i = 0; i < splitRoom.length; i++) {
                io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: person.userid, type: person.type});
                socket.leave(splitRoom[i]);
                
                var getinfobyuser = getObjects(obj.trackData, 'user', person.userid);
                var jsonDetail = JSON.stringify(getinfobyuser);
                
				if(person.type == 'Driver' || person.type == 'driver' || person.type == 'DRIVER'){
					UpdateDriverLog(person.userid, person.type, person.insertedLogID, splitRoom[i], jsonDetail);
				}
                console.log('There is someone left the room ' + splitRoom[i]);
            }
            people = _.without(people, person);
        }
        sockets = _.without(sockets, o);
    });
    socket.on('leave', function (room, userid, type) {
        var o = _.findWhere(sockets, {'id': socket.id});
        var person = _.findWhere(people, {'id': socket.id});
        if (typeof person !== 'undefined') {
            people = _.without(people, person);
        }
        var splitRoom = person.name.split(',');
        for (var i = 0; i < splitRoom.length; i++) {
            io.sockets.in(splitRoom[i]).emit('left', {room: splitRoom[i], userid: person.userid, type: person.type});
            socket.emit('left', {room: splitRoom[i], userid: userid, type: type});
            socket.leave(splitRoom[i]);
            console.log('There is someone left the room ' + splitRoom[i]);
            var getinfobyuser = getObjects(obj.trackData, 'user', userid);
            var jsonDetail = JSON.stringify(getinfobyuser);
			if(person.type == 'Driver' || person.type == 'driver' || person.type == 'DRIVER'){
				UpdateDriverLog(person.userid, person.type, person.insertedLogID, splitRoom[i], jsonDetail);
			}
        }

        //socket.emit('left', {room: room, userid : userid});
        sockets = _.without(sockets, o);
    });

});
function InsertDriverLog(room_id, userid, type, callback) {
    var deferred = '';
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1; //months from 1-12
        var day = dateObj.getUTCDate();
        var year = dateObj.getUTCFullYear();
        var hours = dateObj.getHours();
        var minutes = dateObj.getMinutes();
        var seconds = dateObj.getSeconds();

        created = year + "-" + month + "-" + day;
        start_datetime = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
        pool.getConnection(function(err,con){
            if (err) {
                con.release();
                callback.json({"code" : 100, "status" : "Error in connection database"});
                return;
            }  
            con.query('SELECT * FROM tbl_shuttle_driver_log where SDL_DriverID = ? and SDL_ParkingID = ? and SDL_CreatedDateTime = ? limit 1', [userid, room_id, created], function (err, rows) {
                if (err) {
                    throw err;
                } else {
                    if (rows == '') {
                        con.query('insert into tbl_shuttle_driver_log set SDL_DriverID = "' + userid + '", SDL_ParkingID = "' + room_id + '", SDL_JSONData = "", SDL_StatrtDateTime = "' + start_datetime + '", SDL_CreatedDateTime = "' + created + '"', function (err, rows) {
                            if (err)
                                throw err;
                            if (rows) {
                                //rows = JSON.stringify(rows);
                                deferred = rows.insertId;
                            }
                        });
                    } else {
                        deferred = rows[0]['SDL_LogID'];
                    }
                    callback(null, deferred);
                }

            });
        });
    } else {
        callback(null, 0);
    }
}
function UpdateDriverLog(SDL_DriverID, type, insertedLogID, room_id, jsonDetail) {
    if (type == 'Driver' || type == 'driver' || type == 'DRIVER') {
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1; //months from 1-12
        var day = dateObj.getUTCDate();
        var year = dateObj.getUTCFullYear();
        var hours = dateObj.getHours();
        var minutes = dateObj.getMinutes();
        var seconds = dateObj.getSeconds();

        created = year + "-" + month + "-" + day;
        end_datetime = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
        pool.getConnection(function(err,con){
        if (err) {
            con.release();
//            callback.json({"code" : 100, "status" : "Error in connection database"});
            return;
        }
            con.query('SELECT * FROM tbl_shuttle_driver_log where SDL_DriverID = "' + SDL_DriverID + '" and SDL_ParkingID = "'+ room_id +'"  and SDL_LogID = "' + insertedLogID + '" limit 1', function (err, rows) {
                con.release();
                if (err)
                    throw err;
                console.log('Records found');
                if (rows != '') {
                    con.query('update tbl_shuttle_driver_log set SDL_JSONData = "' + mysql_real_escape_string(jsonDetail) + '", SDL_EndDateTime = "' + end_datetime + '" where SDL_DriverID = "' + SDL_DriverID + '" and SDL_ParkingID = "'+ room_id +'" and SDL_LogID = "' + insertedLogID + '"', function (err, rows) {
                        if (err)
                            throw err;
                        if (rows) {
                            console.log('updated');
                        }
                    });
                }
            });
        });
    }
}

function mysql_real_escape_string(str) {
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
