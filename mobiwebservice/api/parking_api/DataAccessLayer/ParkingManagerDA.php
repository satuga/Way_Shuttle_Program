<?php

/* * **********************************************************
  FILE :	ParkingManagerDA
  PURPOSE: Parking Detail page data acess with prepared statement
  AUTHOR: Jeba
  DATE  : 09 SEP 2016
 * ************************************************************ */

class ParkingManagerDA {

    function GetParkingInfo($ParkingID, $ct) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            if ($ct == 'api')
                $StrSql = "SELECT *,Lattitude AS lat,Longitude AS lon,P_ServiceArea AS Airport_Near_Address FROM tbl_parkingwebservice WHERE P_ID=:ParkingID";
            else
                $StrSql = "SELECT tbl_parking.*,tbl_parking.P_Parkingattributes AS Parking_Attributes,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parkinglocations.*,tbl_registeration.* FROM tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		WHERE tbl_parking.P_Status=1 AND tbl_parking.Deleted=0 AND tbl_parking.P_ID=:ParkingID";
            //$StrSql="select * from tbl_parking where p_id=:ParkingID and p_userid=:UserID";
            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':ParkingID', $ParkingID, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

    function GetEventParkingInfo($ParkingID) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query

            $StrSql = "SELECT * FROM tbl_parkingevents WHERE PE_ID=:ParkingID";
            //$StrSql="select * from tbl_parking where p_id=:ParkingID and p_userid=:UserID";
            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':ParkingID', $ParkingID, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

    function GetParkingGalleryPhotosFromParkingLot($id, $limit = 0, $size = 0) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $StrSql = "SELECT IMG_ImageID,IMG_ImageName,IMG_IMM_ModuleID,IMG_ModueReferenceID FROM tbl_imagegallery WHERE IMG_IMM_ModuleID=1 AND IMG_ModueReferenceID=:id AND IMG_image_upload_size =:size LIMIT 0,5";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':size', $size, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo "error:".$e->getMessage();
        }
    }

    function GetParkingGalleryPhotosFromAirport($Airport, $limit = 0, $size = 0) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $StrSql = "select IMG_ImageID,IMG_ImageName,IMG_IMM_ModuleID,IMG_ModueReferenceID FROM tbl_imagegallery
	INNER JOIN tbl_airports ON tbl_airports.A_ID=tbl_imagegallery.IMG_ModueReferenceID
	WHERE IMG_IMM_ModuleID=2 AND  tbl_airports.A_Code=:Airport And IMG_image_upload_size =:size ORDER BY RAND() LIMIT 5";


            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':Airport', $Airport, PDO::PARAM_STR);
            $stmt->bindParam(':size', $size, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo "error:".$e->getMessage();
        }
    }

    function GetParkingGalleryPhotosFromCity($City, $limit = 0, $size = 0) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $StrSql = "SELECT IMG_ImageID,IMG_ImageName,IMG_IMM_ModuleID,IMG_ModueReferenceID FROM tbl_imagegallery
	INNER JOIN tbl_statecities ON tbl_statecities.CTY_CityID=tbl_imagegallery.IMG_ModueReferenceID
	WHERE IMG_IMM_ModuleID=3 AND  tbl_statecities.CTY_CityName=:City AND IMG_image_upload_size =:size ORDER BY RAND() LIMIT 5";


            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':City', $City, PDO::PARAM_STR);
            $stmt->bindParam(':size', $size, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo "error:".$e->getMessage();
        }
    }

    function GetLocationParking($id, $Location) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            $StrSql = "SELECT tbl_parking.P_Lot_Type,P_ID,
		
		case when p_id=:id then 0 else 1 end as SortOrder,
		
		P_Pricingtype,P_Daily_Price_Type,P_Weeklyprice,P_Monthlyprice,P_FAmt,Event_price FROM tbl_parking 
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Location=:Location ORDER BY SortOrder";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':Location', $Location, PDO::PARAM_STR);
            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

    function GetWebServiceDesc($id) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            $StrSql = "Select * from tbl_pnftrandesc WHERE PND_PNF_ID=:id GROUP BY PND_PropertyName";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }

    function GetAParkingWeekdayLowPrice($id) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $Yesterday = date("Y-m-d");
            $StrSql = "SELECT MIN(PA_P_Dailyprice) AS PRICE FROM tbl_parkingweekdayavailability WHERE P_fromDate>=:Yesterday AND P_ID=:id";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':Yesterday', $Yesterday, PDO::PARAM_STR);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();
            $objUMDO = new ActivityManagerDO();

            $objUMDO->PRICE;
            foreach ($result as $Gr => $GO) {
                $objUMDO->PRICE = $GO['PRICE'];
            }
            //Free memory
            $stmt = null;
            $dbh = null;

            return $objUMDO->PRICE;
        } catch (PDOException $e) {
            //echo "error:".$e->getMessage();
        }
    }

    function GetParkingDailyPrice($id) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $start = date("Y-m-d");
            $StrSql = "SELECT PA_P_Dailyprice FROM tbl_parkingavailability WHERE (:start BETWEEN P_fromDate AND PA_toDate) AND P_ID=:id";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':start', $start, PDO::PARAM_STR);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();
            $objUMDO = new ActivityManagerDO();

            $objUMDO->PA_P_Dailyprice;
            foreach ($result as $Gr => $GO) {
                $objUMDO->PA_P_Dailyprice = $GO['PA_P_Dailyprice'];
            }
            //Free memory
            $stmt = null;
            $dbh = null;

            return $objUMDO->PA_P_Dailyprice;
        } catch (PDOException $e) {
            //echo "error:".$e->getMessage();
        }
    }

    function GetNearestRestaurants($lat, $lon) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query

            $StrSql = "SELECT merchant.*,(3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(merchant.geoLat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(merchant.geoLat) * pi()/180) * POWER(SIN((:lon-merchant.geoLong) * pi()/180 / 2), 2) ))) AS distance FROM merchant
		WHERE merchant.status='Active' AND merchant.Deleted=0 AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(merchant.geoLat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(merchant.geoLat) * pi()/180) * POWER(SIN((:lon-merchant.geoLong) * pi()/180 / 2), 2) )))<=10";
            $StrSql.=" GROUP BY merchant.id ORDER BY distance ASC LIMIT 10";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':lat', $lat, PDO::PARAM_INT);
            $stmt->bindParam(':lon', $lon, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function GetNearestEvents($lat, $lon, $strKeyword = '') {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            $dt = date("Y-m-d H:i:s", strtotime("-45 minutes"));
            $keywordSearch = '';
            if(!empty($strKeyword)){
                $keywordSearch = " AND (tevoevents.name REGEXP '$strKeyword' OR tevovenues.name REGEXP '$strKeyword') ";

            }
//            $StrSql = "SELECT tevoevents.eventId,tevoevents.eventName,tevovenues.venueName, tevovenues.latitude as lat, tevovenues.longitude as lon,(3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tevovenues.latitude)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tevovenues.latitude) * pi()/180) * POWER(SIN((:lon-tevovenues.longitude) * pi()/180 / 2), 2) ))) AS distance FROM tevoevents
//		INNER JOIN tevovenues ON tevovenues.venueId=tevoevents.venueId
//		WHERE tevoevents.eventsStatus=1 AND tevovenues.latitude<>'' ".$keywordSearch." AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tevovenues.latitude)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tevovenues.latitude) * pi()/180) * POWER(SIN((:lon-tevovenues.longitude) * pi()/180 / 2), 2) )))<=100 AND eventDate>=:dt";
//            $StrSql.=" GROUP BY tevovenues.venueName ORDER BY distance ASC LIMIT 10";

            $StrSql = "SELECT tevoevents.id as eventId,tevoevents.name as eventName,tevovenues.name as venueName, tevovenues.latitude as lat, tevovenues.longitude as lon FROM tevoevents
                        INNER JOIN tevovenues ON tevovenues.id=tevoevents.venue_id
                        WHERE  occurs_at>=:dt ".$keywordSearch." ";
            $StrSql.=" GROUP BY tevovenues.name LIMIT 10";
            
//            echo $StrSql;exit;
            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
//            $stmt->bindParam(':lat', $lat, PDO::PARAM_INT);
//            $stmt->bindParam(':lon', $lon, PDO::PARAM_INT);
            $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function GetNearestMovies($lat, $lon) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query

            $StrSql = "SELECT tbl_activities.Act_ID,tbl_activitycompany.C_City,tbl_activitycompany.C_State,tbl_activitycompany.C_CompanyName,(3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_activitycompany.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_activitycompany.lat) * pi()/180) * POWER(SIN((:lon-tbl_activitycompany.lon) * pi()/180 / 2), 2) ))) AS distance FROM tbl_activities
		INNER JOIN  tbl_activitycompany ON  tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID
		WHERE tbl_activities.Act_Cat_ID=86 AND tbl_activities.Deleted=0 AND tbl_activities.Act_Status=1 AND tbl_activitycompany.C_CompanyName<>'' AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_activitycompany.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_activitycompany.lat) * pi()/180) * POWER(SIN((:lon-tbl_activitycompany.lon) * pi()/180 / 2), 2) )))<=100";
            $StrSql.=" GROUP BY C_CompanyName ORDER BY tbl_activitycompany.C_CompanyName ASC LIMIT 10";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':lat', $lat, PDO::PARAM_INT);
            $stmt->bindParam(':lon', $lon, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function GetNearestAirports($lat, $lon, $strKeyword) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {
            
            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            $keywordSearch = '';
            if(!empty($strKeyword)){
                $keywordSearch = " AND (Airport_Near_Address REGEXP '$strKeyword' OR Park_City REGEXP '$strKeyword' OR Park_State REGEXP '$strKeyword') ";
            }
            
//            $StrSql = "SELECT Airport_Near_Address,lat,lon,(3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:lon-tbl_parkinglocations.lon) * pi()/180 / 2), 2) ))) AS distance ,Park_City,Park_State  FROM tbl_parking
//		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
//		WHERE tbl_parking.P_Status=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:lon-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=100";
//            $StrSql.=" AND Airport_Near_Address<>'' ".$keywordSearch." GROUP BY Airport_Near_Address ORDER BY distance ASC";
            
            $StrSql = "SELECT Airport_Near_Address,lat,lon,Park_City,Park_State  FROM tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		WHERE tbl_parking.P_Status=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event'";
            $StrSql.=" AND Airport_Near_Address<>'' ".$keywordSearch." GROUP BY Airport_Near_Address ORDER BY Park_City ASC";

            //Prepare the statement
            $stmt = $dbh->prepare($StrSql);

            //Attach data variables - WITH ITS TYPES!	
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':lat', $lat, PDO::PARAM_INT);
            $stmt->bindParam(':lon', $lon, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            //Free memory
            $stmt = null;
            $dbh = null;

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function Query($Page) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            $starttimequery = time();
            GLOBAL $Map_API, $con;
            $desc = explode(",", $_SESSION['parkingdescription']);

            if (count($desc) > 1) {
                if (count($desc) == 3) {
                    $loc = ucwords($desc[0]) . "," . ucwords($desc[1]) . "," . strtoupper(trim($desc[2]));
                    $_SESSION['City'] = $desc[1];
                    $_SESSION['State'] = $desc[2];
                } else {
                    $loc = ucwords($desc[0]) . "," . strtoupper(trim($desc[1]));
                    $_SESSION['City'] = $desc[0];
                    $_SESSION['State'] = $desc[1];
                }
            } else {
                if ($_SESSION['Street'] <> '')
                    $loc = $_SESSION['Street'] . "," . $_SESSION['City'] . "," . $_SESSION['State'];
                else
                    $loc = $_SESSION['City'] . "," . $_SESSION['State'];
                $loc = str_replace(",all", "", $loc);
                //$_SESSION['LOCATION']=$loc;
            }
            if ($lat == '' || $long == '' || $lat == '0' || $long == '0') {
                $where = stripslashes($loc);
                $latlong = getlatandlon($where);
                $lat = $latlong[0];
                $long = $latlong[1];
            }
            if ($lat == '' || $long == '') {
                $latlong = GetLatandLang($_SESSION['State'], $_SESSION['City']);
                $lat = $latlong[0];
                $long = $latlong[1];
            }

            if ($_SESSION['sort_by'] == 'Name')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,Park_Name ASC";
            else if ($_SESSION['sort_by'] == 'Top Results')
                $QueryCommon = " AvailabilityStatus desc,Toppriority DESC,Todaysbookings DESC";
            else if ($_SESSION['sort_by'] == 'Price')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price:lowest first')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price:highest first')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,Average_Price DESC";
            else if ($_SESSION['sort_by'] == 'Highest Rated')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,average_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Most Reviewed')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,total_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Distance:nearest first')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,distance ASC, Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Distance')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,distance ASC, Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Latest')
                $QueryCommon = " Toppriority DESC,AvailabilityStatus desc,P_Created DESC, Average_Price ASC";
            else
                $QueryCommon = " Toppriority DESC,ResidenceBoostStatus desc,AvailabilityStatus desc,Main ASC,distance ASC";
            //$_SESSION['PriceType']='daily';
            // $Price_Type='daily';
            //$Price_Type=$_SESSION['PriceType']!=''?$_SESSION['PriceType']:'daily';
            $Price_Type = $_SESSION['PriceType'];
            $CHECKIN = date("Y-m-d", strtotime($_SESSION['CHECKIN']));
            $CHECKOUT = date("Y-m-d", strtotime($_SESSION['CHECKOUT']));

            if ($_SESSION['CHECKIN'] == '' || $_SESSION['CHECKOUT'] == '') {
                $stravailsql = "1 AvailabilityStatus, 0 ResidenceBoostStatus";
            } else {

                $stravailsql = "IFNULL( CASE WHEN p_Daily_price_type ='Week' THEN (select min(CASE WHEN (IFNULL(p2.PA_No_Spaces,0)-IFNULL(occupiedSpaces,0))<=0 THEN '0' ELSE '1' END )
from tbl_parkingweekdayavailability p2 use index (indexfromdate,indexpid) where p2.P_ID=tbl_parking.P_ID AND p2.p_fromdate between '" . $CHECKIN . "'
AND
    '" . $CHECKOUT . "'
group by p2.Park_ID) ELSE '1' END,0) AvailabilityStatus,
( CASE WHEN tbl_parkinglocations.Park_Typeoflocation='Residence' THEN CASE WHEN tbl_parking.LastOrderID=0 THEN '1' ELSE '0' END ELSE '0' END ) ResidenceBoostStatus";
            }

            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($_SESSION['CHECKIN'] == '' && $_SESSION['CHECKIN'] == '')
                $priority = "IsEvent,Toppriority DESC,";
            else
                $priority = "IsEvent,";
            if ($_SESSION['sort_by'] == 'Name')
                $QueryCommon = $priority . "AvailabilityStatus desc,Park_Name ASC";
            else if ($_SESSION['sort_by'] == 'Top Results')
                $QueryCommon = $priority . "AvailabilityStatus desc,Toppriority DESC,Todaysbookings DESC,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price')
                $QueryCommon = $priority . "AvailabilityStatus desc,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price:lowest first')
                $QueryCommon = $priority . "AvailabilityStatus desc,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price:highest first')
                $QueryCommon = $priority . "AvailabilityStatus desc,Average_Price DESC";
            else if ($_SESSION['sort_by'] == 'Highest Rated')
                $QueryCommon = $priority . "AvailabilityStatus desc,average_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Most Reviewed')
                $QueryCommon = $priority . "AvailabilityStatus desc,total_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Distance:nearest first')
                $QueryCommon = $priority . "AvailabilityStatus desc,distance ASC, Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Distance')
                $QueryCommon = $priority . "AvailabilityStatus desc,distance ASC, Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Latest')
                $QueryCommon = $priority . "AvailabilityStatus desc,P_Created DESC, Average_Price ASC";
            else
                $QueryCommon = $priority . "ResidenceBoostStatus desc,AvailabilityStatus desc,Main ASC,Average_Price ASC,distance ASC";

            if ($_SESSION['PType'] == '' || $_SESSION['PType'] == 'all') {
                //All parking
                $dt2 = date('Y-m-d H:i:s');
                $Query = " select * from(SELECT CASE WHEN P_Pricingtype ='event' THEN 1 else 0 end IsEvent," . $stravailsql . ",

	Park_Typeoflocation,tbl_parkinglocations.P_Parkingattributes,LastOrderID,CASE WHEN 3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(($long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) >10 THEN 0 else Toppriority end Toppriority,tbl_parkinglocations.lat AS Lattitude, tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type, tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews, tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo, tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip, tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address, tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_AirportVenue,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance, 2 AS Main,tbl_parking.min_reservation,tbl_parking.Event_price,P_Weeklyprice,P_Monthlyprice, PE_ID,PE_ParkID,PE_EventID,PE_Eventdate,PE_Start,PE_End,PE_Venue,PE_EventName,PE_VenueName,PE_VenueAddress,PE_VenueCity,PE_VenueState,PE_VenueZip,PE_Created,PE_EventPrice,PE_EventSpots,PE_EventStart,PE_EventEnd,tbl_parkinglocations.smart_lot smart_valid
	 FROM tbl_parking USE INDEX(indexstatus)
	 INNER JOIN tbl_parkinglocations ON tbl_parking.P_Location=tbl_parkinglocations.Park_ID
	 INNER JOIN tbl_registeration ON tbl_parking.P_UserID=tbl_registeration.id
	 LEFT JOIN tbl_parkingevents ON tbl_parking.P_ID=tbl_parkingevents.PE_ParkID AND tbl_parkingevents.PE_Start>'" . $dt2 . "'
	 WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1
	 AND (tbl_parking.Average_Price >= '1' AND tbl_parking.Average_Price<='100')
	 AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=20 AND CASE WHEN P_Pricingtype ='event' THEN tbl_parkingevents.PE_EventName<>'' ELSE TRUE END";
                if ($_SESSION['AIRPORT'] <> '')
                    $Query.=" AND Airport_Near_Address='" . $_SESSION['AIRPORT'] . "'";
                if ($_SESSION['PType'] == 'airport')
                    $Query.=" AND Park_AirportVenue=1 ";
                else if ($_SESSION['PType'] == 'city')
                    $Query.=" AND Park_AirportVenue=0 ";
                if ($_SESSION['PType'] <> '' && $_SESSION['PType'] <> 'event' && $Price_Type <> '')
                    $Query.=" AND P_Pricingtype='" . $Price_Type . "'";
                if ($_SESSION['RANGE'] <> '') {
                    $RangeVal = explode("-", $_SESSION['RANGE']);
                    $Min = trim(trim($RangeVal[0]), '$');
                    $Max = trim(trim($RangeVal[1]), '$');
                    $Query.=" AND (tbl_parking.Average_Price >= '" . $Min . "' AND tbl_parking.Average_Price<='" . $Max . "')";
                }
                $Query.=" union
 SELECT 1 IsEvent,1 AvailabilityStatus,
 0 ResidenceBoostStatus,
 'PNF' Park_Typeoflocation,
 '' P_Parkingattributes,
 0 LastOrderID,Toppriority,

 tbl_parkingwebservice.Lattitude,tbl_parkingwebservice.Longitude,tbl_parkingwebservice.P_ID,
 tbl_parkingwebservice.Average_Price,tbl_parkingwebservice.P_Lot_Type,
 tbl_parkingwebservice.P_Parkingextras,tbl_parkingwebservice.P_Pricingtype,0 AS P_Daily_Price_Type,
 tbl_parkingwebservice.clicks,tbl_parkingwebservice.views,tbl_parkingwebservice.Todaysbookings,
 tbl_parkingwebservice.average_reviews,tbl_parkingwebservice.total_reviews,tbl_parkingwebservice.P_Created,
 0 AS P_FAmt,tbl_parkingwebservice.Park_Name,tbl_parkingwebservice.Park_Logo,tbl_parkingwebservice.Park_Address ,
 tbl_parkingwebservice.Park_City,tbl_parkingwebservice.Park_State,tbl_parkingwebservice.Park_Zip,
 tbl_parkingwebservice.P_Airport_Distance,0 AS Park_Image,tbl_parkingwebservice.P_ServiceArea AS Airport_Near_Address,0 AS Airport_Near_Address_ID,
 0 AS Park_AirportVenue,
 0 AS firstname,0 AS display_name,3956 * 2 * ASIN(SQRT(POWER(SIN((37.7749 - abs(tbl_parkingwebservice.Lattitude)) * pi()/180 / 2), 2) + COS(37.7749* pi()/180 ) * COS(abs(tbl_parkingwebservice.Lattitude) * pi()/180) * POWER(SIN((-122.419-tbl_parkingwebservice.Longitude) * pi()/180 / 2), 2) )) as distance,
 0 AS Main,0 as min_reservation ,
  0 Event_price,0 P_Weeklyprice,0 P_Monthlyprice,0 PE_ID,0 PE_ParkID,0 PE_EventID,0 PE_Eventdate,0 PE_Start,0 PE_End,0 PE_Venue,0 PE_EventName,0 PE_VenueName,0 PE_VenueAddress,0 PE_VenueCity,0 PE_VenueState,0 PE_VenueZip,0 PE_Created,0 PE_EventPrice,0 PE_EventSpots,0 PE_EventStart,0 PE_EventEnd,'no' smart_valid
 FROM tbl_parkingwebservice WHERE status=1 AND P_Pricingtype='Day'";
                if ($_SESSION['AIRPORT'] <> '')
                    $Query.=" AND P_ServiceArea='" . $_SESSION['AIRPORT'] . "'";
                $Query.=" AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkingwebservice.Lattitude)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkingwebservice.Lattitude) * pi()/180) * POWER(SIN((:long-tbl_parkingwebservice.Longitude) * pi()/180 / 2), 2) )))<=20";
                //$Query.=" ORDER BY PE_Eventdate,distance ASC";
            }
            else {
                $dt2 = date('Y-m-d H:i:s');
                $Query = " select * from(SELECT CASE WHEN P_Pricingtype ='event' THEN 1 else 0 end IsEvent," . $stravailsql . ",
	Park_Typeoflocation,tbl_parkinglocations.P_Parkingattributes,LastOrderID,CASE WHEN 3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) >10 THEN 0 else Toppriority end Toppriority,tbl_parkinglocations.lat AS Lattitude, tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type, tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews, tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo, tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip, tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address, tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_AirportVenue,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance, 2 AS Main,tbl_parking.min_reservation,tbl_parking.Event_price,P_Weeklyprice,P_Monthlyprice, PE_ID,PE_ParkID,PE_EventID,PE_Eventdate,PE_Start,PE_End,PE_Venue,PE_EventName,PE_VenueName,PE_VenueAddress,PE_VenueCity,PE_VenueState,PE_VenueZip,PE_Created,PE_EventPrice,PE_EventSpots,PE_EventStart,PE_EventEnd,tbl_parkinglocations.smart_lot smart_valid
	 FROM tbl_parking USE INDEX(indexstatus)
	 INNER JOIN tbl_parkinglocations ON tbl_parking.P_Location=tbl_parkinglocations.Park_ID
	 INNER JOIN tbl_registeration ON tbl_parking.P_UserID=tbl_registeration.id
	 LEFT JOIN tbl_parkingevents ON tbl_parking.P_ID=tbl_parkingevents.PE_ParkID AND tbl_parkingevents.PE_Start>'" . $dt2 . "'
	 WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1
	 AND (tbl_parking.Average_Price >= '1' AND tbl_parking.Average_Price<='100')
	 AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=20 AND CASE WHEN P_Pricingtype ='event' THEN tbl_parkingevents.PE_EventName<>'' ELSE TRUE END";
                if ($_SESSION['AIRPORT'] <> '')
                    $Query.=" AND Airport_Near_Address='" . $_SESSION['AIRPORT'] . "'";
                if ($_SESSION['PType'] == 'airport')
                    $Query.=" AND Park_AirportVenue=1 ";
                else if ($_SESSION['PType'] == 'city')
                    $Query.=" AND Park_AirportVenue=0 ";
                if ($_SESSION['PType'] <> '' && $_SESSION['PType'] <> 'event' && $Price_Type <> '')
                    $Query.=" AND P_Pricingtype='" . $Price_Type . "'";
                if ($_SESSION['RANGE'] <> '') {
                    $RangeVal = explode("-", $_SESSION['RANGE']);
                    $Min = trim(trim($RangeVal[0]), '$');
                    $Max = trim(trim($RangeVal[1]), '$');
                    $Query.=" AND (tbl_parking.Average_Price >= '" . $Min . "' AND tbl_parking.Average_Price<='" . $Max . "')";
                }
            }

            $Query.=") Tabl1";
            $Query.= " ORDER BY " . $QueryCommon;
            //Prepare the statement
            $stmt = $dbh->prepare($Query);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
            $stmt->bindParam(':long', $long, PDO::PARAM_STR);
            $stmt->bindParam(':dellat', $dellat, PDO::PARAM_STR);
            $stmt->bindParam(':dellong', $dellong, PDO::PARAM_STR);

            //Execute the statement
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_NUM);
            $TotalRecordCount = $stmt->rowCount();

            // Add Page LIMIT
            $Limits = 24;
            //$Page = $_REQUEST['page'];
            if ($Page == "") {
                $Page = 1;
            }
            $pageCount = ceil($TotalRecordCount / $Limits);
            $StartLimit = (($Page - 1) * $Limits);

            if ($TotalRecordCount > ($Page * $Limits)) {
                $EndLimit = $Page * $Limits;
            } else {
                $EndLimit = $TotalRecordCount;
            }
            $Query.=" LIMIT :StartLimit,:EndLimit";
            //Prepare the statement
            $stmt = $dbh->prepare($Query);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
            $stmt->bindParam(':long', $long, PDO::PARAM_STR);
            $stmt->bindParam(':dellat', $dellat, PDO::PARAM_STR);
            $stmt->bindParam(':dellong', $dellong, PDO::PARAM_STR);

            $stmt->bindParam(':EndLimit', $EndLimit, PDO::PARAM_INT);
            $stmt->bindParam(':StartLimit', $StartLimit, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return array($result, $TotalRecordCount);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function EventQuery($Page) {
        GLOBAL $DBSERVER, $DBUSER, $DBPWD, $DBDATABASE;
        try {

            $dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Define the query
            GLOBAL $Map_API, $con;
            $desc = explode(",", $_SESSION['parkingdescription']);
            //Add code by Bindra shah 27-may-2016 It is important
            $lat = $_SESSION['lat'] != '' ? $_SESSION['lat'] : '';
            $long = $_SESSION['long'] != '' ? $_SESSION['long'] : '';
            // End Bindra Shah 27-may-2016
            if (count($desc) > 1) {
                if (count($desc) == 3) {
                    $loc = ucwords($desc[0]) . "," . ucwords($desc[1]) . "," . strtoupper(trim($desc[2]));
                    $_SESSION['City'] = $desc[1];
                    $_SESSION['State'] = $desc[2];
                } else {
                    $loc = ucwords($desc[0]) . "," . strtoupper(trim($desc[1]));
                    $_SESSION['City'] = $desc[0];
                    $_SESSION['State'] = $desc[1];
                }
            } else {
                if ($_SESSION['Street'] <> '')
                    $loc = $_SESSION['Street'] . "," . $_SESSION['City'] . "," . $_SESSION['State'];
                else
                    $loc = $_SESSION['City'] . "," . $_SESSION['State'];
            }

            if ($lat == '' || $long == '') {
                $where = stripslashes($loc);
                $latlong = getlatandlon($where);
                $lat = $latlong[0];
                $long = $latlong[1];
            }
            if ($lat == '' || $long == '') {
                $latlong = getlatandlon($_SESSION['City'] . "," . $_SESSION['State']);
                $lat = $latlong[0];
                $long = $latlong[1];
            }
            if ($lat == '' || $long == '') {
                $latlong = GetLatandLang($_SESSION['State'], $_SESSION['City']);
                $lat = $latlong[0];
                $long = $latlong[1];
            }

            if ($lat == '')
                $lat = 37.36883;
            if ($long == '')
                $long = -122.0363496;
            $_SESSION['LAT'] = $lat;
            $_SESSION['LON'] = $long;

            // Check with Expire Records
            $dt2 = date('Y-m-d H:i:s');

            $Query1 = "SELECT tbl_parkinglocations.smart_lot AS smart_valid,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_parkingevents.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main  FROM tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		RIGHT JOIN tbl_parkingevents ON tbl_parkingevents.PE_ParkID=tbl_parking.P_ID
		WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND tbl_parkingevents.PE_End>'" . $dt2 . "'";

            if ($_SESSION['Optional_Services'] != "") {
                $Optional_Servicess = explode(",", $_SESSION['Optional_Services']);
                if (count($Optional_Servicess) > 0) {
                    $Query1.= " AND (";
                    $cnn = 1;
                    foreach ($Optional_Servicess as $p => $q) {
                        if (count($Optional_Servicess) == $cnn)
                            $Query1.= " tbl_parkinglocations.Park_Address LIKE '%" . $q . "%' ";
                        else
                            $Query1.= " tbl_parkinglocations.Park_Address LIKE '%" . $q . "%' OR ";
                        $cnn++;
                    }
                    $Query1.= " ) ";
                }
            }
            $Query1.=" AND P_Pricingtype='event'";

            if ($_SESSION['RANGE'] <> '') {
                $RangeVal = explode("-", $_SESSION['RANGE']);
                $Min = trim(trim($RangeVal[0]), '$');
                $Max = trim(trim($RangeVal[1]), '$');
                $Query1.=" AND (tbl_parking.Average_Price >= '" . $Min . "' AND tbl_parking.Average_Price<='" . $Max . "')";
            }
            if ($_SESSION['parkingRev'] != "") {
                $Query1.= " AND tbl_parking.average_reviews>=" . $_SESSION['parkingRev'];
            }
            if ($_SESSION['EVENT'] <> '') {
                $Query1.= " AND tbl_parkingevents.PE_EventName='" . $_SESSION['EVENT'] . "'";
            }
            if ($_SESSION['typeloc'] != '' && $_SESSION['typeloc'] != 'All')
                $Query1.= " AND Park_Typeoflocation='" . $_SESSION['typeloc'] . "'";
            $Query1.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN((:lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS(:lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN((:long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=" . $_SESSION['MILES'];

            //$QueryCommon= " GROUP BY tbl_parkingevents.PE_EventID";
            $QueryCommon = " ";
            //echo $_SESSION['sort_by'];
            //,priority ASC
            if ($_SESSION['sort_by'] == 'Name')
                $QueryCommon.= " ORDER BY Main ASC,Park_Name ASC";
            else if ($_SESSION['sort_by'] == 'Top Results')
                $QueryCommon.= " ORDER BY Main ASC,PE_Eventdate,distance ASC,Todaysbookings DESC";
            else if ($_SESSION['sort_by'] == 'Price:lowest first')
                $QueryCommon.= " ORDER BY Main ASC,Average_Price ASC";
            else if ($_SESSION['sort_by'] == 'Price:highest first')
                $QueryCommon.= " ORDER BY Main ASC,Average_Price DESC";
            else if ($_SESSION['sort_by'] == 'Highest Rated')
                $QueryCommon.= " ORDER BY Main ASC,average_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Most Reviewed')
                $QueryCommon.= " ORDER BY Main ASC,total_reviews DESC";
            else if ($_SESSION['sort_by'] == 'Distance:nearest first')
                $QueryCommon.= " ORDER BY distance ASC,Todaysbookings DESC";
            else if ($_SESSION['sort_by'] == 'Latest')
                $QueryCommon.= " ORDER BY Main ASC, PE_Eventdate,P_Created DESC";
            else
                $QueryCommon.= " ORDER BY Main ASC,PE_Eventdate,ROUND(distance) ASC,Todaysbookings DESC";
            //$QueryCommon.= " ORDER BY Main ASC, distance ASC";

            $QueryJoin = " UNION ";

            // parking Availability Take checking
            if ($_SESSION['CHECKOUT'] <> '' && $_SESSION['CHECKIN'] <> '') {
                $Query = $Query1 . $QueryCommon;
            } else {
                $Query = $Query1 . $QueryCommon;
            }
            //Prepare the statement
            $stmt = $dbh->prepare($Query);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
            $stmt->bindParam(':long', $long, PDO::PARAM_STR);
            $stmt->bindParam(':dellat', $dellat, PDO::PARAM_STR);
            $stmt->bindParam(':dellong', $dellong, PDO::PARAM_STR);

            //Execute the statement
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_NUM);
            $TotalRecordCount = $stmt->rowCount();

            // Add Page LIMIT
            $Limits = 24;
            //$Page = $_REQUEST['page'];
            if ($Page == "") {
                $Page = 1;
            }
            $pageCount = ceil($TotalRecordCount / $Limits);
            $StartLimit = (($Page - 1) * $Limits);

            if ($TotalRecordCount > ($Page * $Limits)) {
                $EndLimit = $Page * $Limits;
            } else {
                $EndLimit = $TotalRecordCount;
            }
            $Query.=" LIMIT :StartLimit,:EndLimit";
            //Prepare the statement
            $stmt = $dbh->prepare($Query);

            //Attach data variables - WITH ITS TYPES!	
            $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
            $stmt->bindParam(':long', $long, PDO::PARAM_STR);
            $stmt->bindParam(':dellat', $dellat, PDO::PARAM_STR);
            $stmt->bindParam(':dellong', $dellong, PDO::PARAM_STR);

            $stmt->bindParam(':EndLimit', $EndLimit, PDO::PARAM_INT);
            $stmt->bindParam(':StartLimit', $StartLimit, PDO::PARAM_INT);

            //Execute the statement
            $stmt->execute();

            //Process the result
            $result = $stmt->fetchAll();

            //Free memory
            $stmt = null;
            $dbh = null;

            return array($result, $TotalRecordCount);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

}

?>
