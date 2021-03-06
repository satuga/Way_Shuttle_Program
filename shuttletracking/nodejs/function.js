var hashMapDrivers = {};
var hashMapstopsList = {};
var hashmapstopsinformation = {};
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
var moment = require("moment");
var pool = mysql.createPool({
    connectionLimit: ConfigDetail.connectionLimit,
    host: ConfigDetail.host,
    user: ConfigDetail.user,
    password: ConfigDetail.password,
    database: ConfigDetail.database,
    debug: ConfigDetail.debug,
	queueLimit:0,
	acquireTimeout: 1000000,
	waitForConnection: true,
	dateStrings: true
});

var addEtatotime = 0;
var querystring = require('querystring');
var http = require('http');
var host = 'api.way.local';
//var host = 'bi.way.com';
//var host = 'www.way.com';
//var host = 'localhost';

var current_datetime = moment().format('YYYY-MM-D h:mm:ss');

function performRequest(endpoint, method, data, success) {
  console.log(endpoint);
  console.log(method);
  console.log(data);
  console.log(success);
  return false;
  var dataString = JSON.stringify(data);
  var headers = {};
  var agent = new http.Agent({
	  keepAlive: true,
	  keepAliveMsecs: 10000
  });

  if (method == 'GET') {
   // endpoint += '?' + querystring.stringify(data);
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
    headers: headers,
	agent: agent
  };

  var req = http.request(options, function(res) {
    res.setEncoding('utf-8');

    var responseString = '';

    res.on('data', function(data) {
      responseString += data;
    });

    res.on('end', function() {
    
    	try{
    	     var responseObject = JSON.parse(responseString);
    	     return success(responseObject);
             console.log('Create http responsive Object datetime: '+current_datetime);
             console.log('Parking ID: '+responseObject['data']['parking_id']);
             console.log('Getting API URL for stop information: '+endpoint);
    	}catch(e){
    	     console.log('Create responsive Object try and catch: '+e);
    	}
    });
  });

  req.write(dataString);
  req.end();
}

function mainCalculateETAForStops(data) {
	try{
	    var userID = data.user;
	    var userType = data.type;
	    var parkingID = data.room;        
	    hashMapDrivers[userID] = data;     
	    var DTP_DLG_DriverTripLogID = data.DTP_DLG_DriverTripLogID;
	    
	   //console.log(hashmapstopsinformation[parkingID]['data']['parking_id']+':parking lot data:'+parkingID);

	   if(typeof hashmapstopsinformation[parkingID] !== 'undefined'){
			console.log("ujash here1");
			console.log(hashmapstopsinformation);
		        stopsInformation = hashmapstopsinformation[parkingID];
		        temp(stopsInformation, userID, userType, parkingID, DTP_DLG_DriverTripLogID, data);
	   }else {
     	  performRequest('/shuttletracking/get_stops_by_parkingid.php?parking_id='+parkingID, 'GET',{
        
          
  		    //performRequest('/responsive/shuttletracking/get_stops_by_parkingid.php?parking_id='+parkingID, 'GET', {

		    }, function(stopsInformation) {
      			console.log("ujash here");
      			console.log(hashmapstopsinformation);
		      	hashmapstopsinformation[parkingID] = stopsInformation;
			     temp(stopsInformation, userID, userType, parkingID, DTP_DLG_DriverTripLogID, data);
		    });
	   }
	}catch(e){
		console.log('Main Calculate ETA For Stops try and catch error: '+e+' date and time: '+current_datetime);
	}
}

function temp(stopsInformation, userID, userType, parkingID, DTP_DLG_DriverTripLogID, data){

      
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
          //currentseconds = moment().unix();          
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
		  console.log("Calculate ETA for route: " + hashDriverEta[data.user]);
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
			console.log("Rqquested Stop for trip try and catch error: " +e+' date and time: '+current_datetime);
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

  try{
      if(ActualMinutes != 'Estimating...'){
             pool.getConnection(function (err, con) {
      if (err) {
	  console.log(err);
	  if (typeof con !== 'undefined') {
	      con.release();
	  }
      return;
      } else {
        console.log("Store ETA Information and ActualMinutes dateTime:" +current_datetime);
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
                console.log("Get shuttle googlecall eta detail from db:" +current_datetime);
                if (typeof con !== 'undefined') {
                    con.release();
                }
                return;
            } else {
                start_datetime = moment().format('YYYY-MM-D h:mm:ss');
                if (rows == '') {
                    con.query('insert into tbl_shuttle_googlecall_eta set SGE_Drivertrip_LogID = "' + DTP_DLG_DriverTripLogID + '", SGE_DriverID = "'+user_db_id+'", SGE_TripStopID = "' + StopsDTP_TripStopID +'" , SGE_lng	= "' + lng + '", SGE_lat = "' + lat + '", SGE_room = "' + room + '", SGE_eta = "'+ActualMinutes+'"', function (err, rows) {
                        if (err) {
                            console.log("Insert shuttle googlecall eta detail from db:" +current_datetime);
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
                            console.log("Update shuttle googlecall eta detail from db:" +current_datetime);
                            if (typeof con !== 'undefined') {
                                con.release();
                            }
                            return;
                        } else {
                            if (rows) {
                                console.log('Update shuttle googlecall eta detail from db detail: ' + rows);
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
}catch(e){
  console.log(e);
}

}

function getDistanceDetail(originLat, originLong, destinationLat, destinationLong, addEtatotime, data, StopsDTP_TripStopID) {
	var origins = ['"'+originLat+','+originLong+'"'];
  var destinations = ['"'+destinationLat+','+destinationLong+'"'];
  distanceGoogle.key('AIzaSyC4qAOVVDqiYcRQpdBhr2rpWbKQoFb7uWM');
  var distance = '';
  distanceGoogleData(origins, destinations, function (response) {
      //console.log(response);
	  console.log("Distance Google Data datetime: "+current_datetime);
          console.log("Distance Google Data: "+response);
      if(response.status == 1){
		if(response.data > 0){
        googleDistance = parseInt(response.data);
        addEtatotime = parseInt(googleDistance) + addEtatotime;
        ActualMinutes = String(addEtatotime);
        hashDriverEta[data.user] = googleDistance;
        console.log("Distance Google Data for user: " +hashDriverEta[data.user]);
        console.log("Distance Google Data for user to origin: " +origins);
        console.log("Distance Google Data for user to destinations: " +destinations);
        storeETAInformation(ActualMinutes, data.DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data);
		}
      } else {
        ActualMinutes = String(addEtatotime);
        storeETAInformation(ActualMinutes, data.DTP_DLG_DriverTripLogID, StopsDTP_TripStopID, data);
      }
  });
}

function distanceGoogleData(origins, destinations, callback){
  return callback({'status':1,"message":"success","data":30});
  distanceGoogle.key('AIzaSyC4qAOVVDqiYcRQpdBhr2rpWbKQoFb7uWM');
  var distance = '';
  distanceGoogle.matrix(origins, destinations, function (err, distances) {
      if (err) {
          return callback({'status':0,"message":err});
      }
      if(!distances) {
          return callback({'status':0,"message":"There is no distace."})
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
        return callback({'status':0,"message":"there is some problem in calculating distance from google data."})
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
        /* if(parseInt(seconds) > 20){
          return parseInt(minutes)+1 + 'm';
        } else {
          return parseInt(minutes) + 'm';
        }*/
        
        if(parseInt(seconds) > 20){
            $viewminute = parseInt(minutes)+1;
        } else {
            $viewminute = parseInt(minutes);
        }
                    
        if($viewminute <= 0){
            return "1m";
        }else{
            return $viewminute + 'm';
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
