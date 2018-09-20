var hashMapDrivers = {};
var hashMapstopsList = {};
var hashMapStopsMarker = {};
var hashMapStopsETA = {};
var hashDriverGoogleCallTime = {};
var hashDriverGoogleEtaTime = {};
var hashDriverPreviousStops = {};
var hashArrivedStatus = {};
var hashDriverEta = {};
var distanceGoogle = require('google-distance-matrix');
var ConfigDetail = require('./config.js');
var mysql = require("mysql");
var pool = mysql.createPool({
    connectionLimit: ConfigDetail.connectionLimit,
    host: ConfigDetail.host,
    user: ConfigDetail.user,
    password: ConfigDetail.password,
    database: ConfigDetail.database,
    debug: ConfigDetail.debug
});

var addEtatotime = 0;
var querystring = require('querystring');
var http = require('http');
//var host = 'api.way.local';
//var host = 'api.way.local';
var host = 'bi.way.com';
//var host = 'www.way.com';

function performRequest(endpoint, method, data, success) {
  var dataString = JSON.stringify(data);
  var headers = {};

  if (method == 'GET') {
    endpoint += '?' + querystring.stringify(data);
  }
  else {
    headers = {
      'Content-Type': 'application/json',
      'Content-Length': dataString.length
    };
  }
  var options = {
    host: host,
    path: endpoint,
    method: method,
    headers: headers
  };

  var req = http.request(options, function(res) {
    res.setEncoding('utf-8');

    var responseString = '';

    res.on('data', function(data) {
      responseString += data;
    });

    res.on('end', function() {
//      console.log(responseString);
	try{
		var responseObject = JSON.parse(responseString);
		success(responseObject);
	}catch(e){
		console.log(e);
	}
    });
  });

  req.write(dataString);
  req.end();
}

function mainCalculateETAForStops(data) {
    var userID = data.user;
    var userType = data.type;
    var parkingID = data.room;
    var dateObj = new Date();
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    var day = dateObj.getUTCDate();
    var year = dateObj.getUTCFullYear();
    var hours = dateObj.getHours();
    var minutes = dateObj.getMinutes();
    var seconds = dateObj.getSeconds();

    hashMapDrivers[userID] = data;

    var DTP_DLG_DriverTripLogID = data.DTP_DLG_DriverTripLogID;

	  performRequest('/shuttletracking/get_stops_by_parkingid.php?parking_id='+parkingID, 'GET', {
    //performRequest('/responsive/shuttletracking/get_stops_by_parkingid.php?parking_id='+parkingID, 'GET', {

    }, function(stopsInformation) {
      setStopsAsMarkers(stopsInformation);
      var hashDriverGoogleCallTimeStatusForArrived = 0;
      if(typeof hashDriverPreviousStops[userID] !== 'undefined'){
          if(hashDriverPreviousStops[userID] !== data.Previous_DTP_TripStopID){
              var hashDriverGoogleCallTimeStatusForArrived = 1;
          }
      }
      hashDriverPreviousStops[userID] = data.Previous_DTP_TripStopID;

      if (hashMapstopsList[DTP_DLG_DriverTripLogID]) {
          hashMapstopsListTripDetail = hashMapstopsList[DTP_DLG_DriverTripLogID];
          var currentDate = new Date();
          currentseconds = currentDate.getTime();
          var hashDriverGoogleCallTimeStatus = 0;
          if(typeof hashDriverGoogleCallTime[userID] === 'undefined'){
             var hashDriverGoogleCallTimeStatus = 1;
          }
      if(typeof hashMapDrivers[userID] !== 'undefined' && (currentseconds-hashDriverGoogleCallTime[userID]) >= 60000 || hashDriverGoogleCallTimeStatus === 1 || hashDriverGoogleCallTimeStatusForArrived === 1){
          if(typeof hashDriverEta[data.user] !== 'undefined'){
            delete hashDriverEta[data.user];
          }
          hashMapstopsListTripDetail.forEach(function (hashMapstopsListTripDetailObj) {
              var StopsTrip_Duration = hashMapstopsListTripDetailObj.DTP_DriverTrip_Duration;
              var StopsDTP_TripStopID = hashMapstopsListTripDetailObj.DTP_TripStopID;
              var StopsDTP_TripPreviousStopID = hashMapstopsListTripDetailObj.DTP_TripPreviousStopID;
              var StopsDTP_TripNextStopID = hashMapstopsListTripDetailObj.DTP_TripNextStopID;

                  hashMapDriversObj = hashMapDrivers[userID];
                  var DriverDTP_TripPreviousStopID = hashMapDriversObj.Previous_DTP_TripStopID;
                  var DTP_DLG_DriverTripLogID = hashMapDriversObj.DTP_DLG_DriverTripLogID;
                  addEtatotime = 0;
                  calculateETAforRequestStop(StopsDTP_TripPreviousStopID, DriverDTP_TripPreviousStopID, StopsDTP_TripStopID, userID, data);
                  hashDriverGoogleCallTime[userID] = nextGoogleCallTime();
          });
        }
      }
   });
}

function calculateETAforRequestStop(requestedStopID, shuttlePreviousStopID, StopsDTP_TripStopID, userID, data){
    hashMapDriversObj = hashMapDrivers[userID];
    var requestedStopIDObj =  hashMapStopsMarker[requestedStopID];
    if(requestedStopIDObj.DTP_TripStopID == shuttlePreviousStopID){
        var originLat = hashMapDriversObj.lat;
        var originLong = hashMapDriversObj.lng;

        //next stop id and then get destination
        var StopsDTP_TripStopIDObj =  hashMapStopsMarker[requestedStopIDObj.DTP_TripNextStopID];
        var destinationLat = StopsDTP_TripStopIDObj.DTP_Lat;
        var destinationLong = StopsDTP_TripStopIDObj.DTP_Lon;
        if(typeof hashDriverEta[data.user] === 'undefined'){
          getDistanceDetail(originLat, originLong, destinationLat, destinationLong, addEtatotime, data, StopsDTP_TripStopID);
        } else {
		  if(hashDriverEta[data.user] > 0){
          console.log("Here Here Here Here " + hashDriverEta[data.user]);
          addEtatotime = parseInt(hashDriverEta[data.user]) + addEtatotime;
          ActualMinutes = String(addEtatotime);
          storeETAInformation(ActualMinutes, data.DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data);
		  }
        }
    } else {
		try{
		if(requestedStopIDObj.DTP_DriverTrip_Duration > 0){
        googleDistance = parseInt(requestedStopIDObj.DTP_DriverTrip_Duration);
        addEtatotime = parseInt(googleDistance) + addEtatotime;
        calculateETAforRequestStop(requestedStopIDObj.DTP_TripPreviousStopID, shuttlePreviousStopID, StopsDTP_TripStopID, userID, data);
		}
		}catch(e){
			console.log(e);
		}
    }
    //console.log(requestedStopIDObj);
}

function nextGoogleCallTime(){
        var currentDate = new Date();
        currentseconds = currentDate.getTime();
        return currentseconds;
}

function setStopsAsMarkers(stopsInformation) {
    allStopsInformation = stopsInformation.data.stops;
    allStopsInformation.forEach(function (allStopsInformationObj) {
      hashMapstopsList[allStopsInformationObj.SPD_DLG_DriverTripLogID] = allStopsInformationObj.StopDetails;
      var allStopsDetailInformation = allStopsInformationObj.StopDetails;
      allStopsDetailInformation.forEach(function (allStopsDetailInformationObj) {
          hashMapStopsMarker[allStopsDetailInformationObj.DTP_TripStopID] = allStopsDetailInformationObj;
      });
    });
}

function storeETAInformation(ActualMinutes, DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data){
	var dateObj = new Date();
  var month = dateObj.getUTCMonth() + 1; //months from 1-12
  var day = dateObj.getUTCDate();
  var year = dateObj.getUTCFullYear();
  var hours = dateObj.getHours();
  var minutes = dateObj.getMinutes();
  var seconds = dateObj.getSeconds();
	if(ActualMinutes != 'Estimating...'){
  pool.getConnection(function (err, con) {
      if (err) {
          console.log(err);
          if (typeof con !== 'undefined') {
              con.release();
          }
          return;
      } else {
        console.log("ActualMinutes");
        console.log(ActualMinutes+" "+StopsDTP_TripStopID+" "+DTP_DLG_DriverTripLogID+" "+data.user);
        //console.log(DTP_DLG_DriverTripLogID);
        //console.log(StopsDTP_TripStopID);
        //console.log(StopsDTP_TripStopID1);
        var room = data.room;
        var lat = data.lat;
        var lng = data.lng;
        var user_db_id = data.user;
        con.query('SELECT * FROM tbl_shuttle_googlecall_eta where SGE_Drivertrip_LogID = ? and SGE_DriverID = ? and SGE_TripStopID = ? limit 1', [DTP_DLG_DriverTripLogID, user_db_id, StopsDTP_TripStopID], function (err, rows) {
            if (err) {
                console.log(err);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                start_datetime = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
                if (rows == '') {
                    con.query('insert into tbl_shuttle_googlecall_eta set SGE_Drivertrip_LogID = "' + DTP_DLG_DriverTripLogID + '", SGE_DriverID = "'+user_db_id+'", SGE_TripStopID = "' + StopsDTP_TripStopID +'" , SGE_lng	= "' + lng + '", SGE_lat = "' + lat + '", SGE_room = "' + room + '", SGE_eta = "'+ActualMinutes+'"', function (err, rows) {
                        if (err) {
                            console.log(err);
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                            return;
                        } else {
                            if (rows) {
                                deferred = rows.insertId;
                                if (typeof con !== 'undefined') {
                                    con.release();
                                }
                            }
                        }
                    });
                } else {
                    deferred = rows[0]['SGE_ID'];
                    con.query('update tbl_shuttle_googlecall_eta set SGE_lng	= "' + lng + '", SGE_lat = "' + lat + '", SGE_eta = "'+ActualMinutes+'" where SGE_ID = "' + deferred + '"', function (err, rows) {
                        if (err) {
                            console.log(err);
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                            return;
                        } else {
                            if (rows) {
                                //console.log('updated' + rows);
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
}

function getDistanceDetail(originLat, originLong, destinationLat, destinationLong, addEtatotime, data, StopsDTP_TripStopID) {
	var origins = ['"'+originLat+','+originLong+'"'];
  var destinations = ['"'+destinationLat+','+destinationLong+'"'];
  distanceGoogle.key('AIzaSyC4qAOVVDqiYcRQpdBhr2rpWbKQoFb7uWM');
  var distance = '';
  distanceGoogleData(origins, destinations, function (response) {
      //console.log(response);
	  console.log("distanceGoogleData : ");
    console.log(response);
      if(response.status == 1){
		if(response.data > 0){
        googleDistance = parseInt(response.data);
        addEtatotime = parseInt(googleDistance) + addEtatotime;
        ActualMinutes = String(addEtatotime);
        hashDriverEta[data.user] = googleDistance;
        console.log("Here Here Here Here Here Here Here Here " + hashDriverEta[data.user]);
        console.log(origins);
        console.log(destinations);
        storeETAInformation(ActualMinutes, data.DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data);
		}
      } else {
        ActualMinutes = String(addEtatotime);
        storeETAInformation(ActualMinutes, data.DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data);
      }
  });
}

function distanceGoogleData(origins, destinations, callback){
  //return callback({'status':1,"message":"success","data":30});
  distanceGoogle.key('AIzaSyC4qAOVVDqiYcRQpdBhr2rpWbKQoFb7uWM');
  var distance = '';
  distanceGoogle.matrix(origins, destinations, function (err, distances) {
      if (err) {
          return callback({'status':0,"message":err});
      }
      if(!distances) {
          return callback({'status':0,"message":"no distace"})
      }
      if (distances.status == 'OK') {
          for (var i=0; i < origins.length; i++) {
              for (var j = 0; j < destinations.length; j++) {
                  var origin = distances.origin_addresses[i];
                  var destination = distances.destination_addresses[j];
                  if (distances.rows[0].elements[j].status == 'OK') {
                      distance = distances.rows[i].elements[j].duration.value;
                      return callback({'status':1,"message":"success","data":distance})
                  } else {
                      return callback({'status':0,"message":"success","data":0})
                  }
              }
          }
      }else {
        return callback({'status':0,"message":"there is some problem"})
      }
  });
}


/**Convert second to minute **/
String.prototype.ssTOMM = function () {

    var sec_num = parseInt(this); // don't forget the second param

    var year = sec_num / 31556926;
    var week = sec_num / 604800;
    var day = sec_num / 86400;
    var hours = sec_num / 3600;
    var minutes = sec_num / 60;
    var seconds = sec_num % 60;

    if (isNaN(minutes)) {
        return 'Estimating...';
    } else if (isNaN(seconds)) {
        return 'Estimating...';
    } else {
        if(parseInt(seconds) > 20){
          return parseInt(minutes)+1 + 'm';
        } else {
          return parseInt(minutes) + 'm';
        }
    }
}



function leaveDriverInformation(DriverID){
  if (typeof hashMapDrivers[DriverID] !== 'undefined') {
      delete hashMapDrivers[DriverID];
  }
}
//module.exports = mainCalculateETAForStops;
module.exports = {
    mainCalculateETAForStops,
    leaveDriverInformation
}
