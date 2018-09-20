var ConfigDetail = require('./config.js');
var mySQL = require('mysql');
var pool = mySQL.createPool({
    connectionLimit: ConfigDetail.connectionLimit,
    host: ConfigDetail.host,
    user: ConfigDetail.user,
    password: ConfigDetail.password,
    database: ConfigDetail.database,
    debug: ConfigDetail.debug,
    connectTimeout: ConfigDetail.connectTimeout,
});
var getConnection = function (cb) {
    pool.getConnection(function (err, connection) {
        //if(err) throw err;
        //pass the error to the cb instead of throwing it
        if(err) {
          return cb(err);
        }
        cb(null, connection);
    });
};
module.exports = getConnection;