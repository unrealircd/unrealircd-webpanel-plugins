<?php

require_once "../../inc/common.php";
require_once "../../inc/header.php";
require_once "../../inc/connection.php";
global $rpc;
?>
<link rel="stylesheet" href="./leaflet.css" />
<script src="./leaflet.js"></script>
<h2>Live Map</h2>
<hr>
<style>
    #map {
        height: 400px;
        width: 600px;
    }

    .new-number {
        animation: fadeToWhite 15s forwards;
    }
    .number-icon {
        background-color: white;
        border: 2px solid black;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        max-height: 20px;
        max-width: 20px;
        font-size: 11px;
        font-weight: bold;
    }
    @keyframes fadeToWhite {
        0% { background-color: red; color: white; } /* Start with red */
        100% { background-color: white; color: black; } /* End with white */
    }
</style>

<div id="map"></div>
<script>
const countryCenters = {
    "AF": { "lat": 33.93911, "lng": 67.709953 },
    "AL": { "lat": 41.153332, "lng": 20.168331 },
    "DZ": { "lat": 28.033886, "lng": 1.659626 },
    "AO": { "lat": -11.202692, "lng": 17.873887 },
    "AR": { "lat": -38.416097, "lng": -63.616672 },
    "AM": { "lat": 40.069099, "lng": 45.038189 },
    "AU": { "lat": -25.274398, "lng": 133.775136 },
    "AT": { "lat": 47.516231, "lng": 14.550072 },
    "AZ": { "lat": 40.143105, "lng": 47.576927 },
    "BH": { "lat": 25.930414, "lng": 50.637772 },
    "BD": { "lat": 23.684994, "lng": 90.356331 },
    "BY": { "lat": 53.709807, "lng": 27.953389 },
    "BE": { "lat": 50.503887, "lng": 4.469936 },
    "BZ": { "lat": 17.189877, "lng": -88.49765 },
    "BJ": { "lat": 9.30769, "lng": 2.315834 },
    "BT": { "lat": 27.514162, "lng": 90.433601 },
    "BO": { "lat": -16.290154, "lng": -63.588653 },
    "BA": { "lat": 43.915886, "lng": 17.679076 },
    "BW": { "lat": -22.328474, "lng": 24.684866 },
    "BR": { "lat": -14.235004, "lng": -51.92528 },
    "BN": { "lat": 4.535277, "lng": 114.727669 },
    "BG": { "lat": 42.733883, "lng": 25.48583 },
    "BF": { "lat": 12.238333, "lng": -1.561593 },
    "BI": { "lat": -3.373056, "lng": 29.918886 },
    "KH": { "lat": 12.565679, "lng": 104.990963 },
    "CM": { "lat": 7.369722, "lng": 12.354722 },
    "CA": { "lat": 56.130366, "lng": -106.346771 },
    "CF": { "lat": 6.611111, "lng": 20.939444 },
    "TD": { "lat": 15.454166, "lng": 18.732207 },
    "CL": { "lat": -35.675147, "lng": -71.542969 },
    "CN": { "lat": 35.86166, "lng": 104.195397 },
    "CO": { "lat": 4.570868, "lng": -74.297333 },
    "KM": { "lat": -11.875001, "lng": 43.872219 },
    "CG": { "lat": -0.228021, "lng": 15.827659 },
    "CR": { "lat": 9.748917, "lng": -83.753428 },
    "HR": { "lat": 45.1, "lng": 15.2 },
    "CU": { "lat": 21.521757, "lng": -77.781167 },
    "CY": { "lat": 35.126413, "lng": 33.429859 },
    "CZ": { "lat": 49.817492, "lng": 15.472962 },
    "DK": { "lat": 56.26392, "lng": 9.501785 },
    "DJ": { "lat": 11.825138, "lng": 42.590275 },
    "DM": { "lat": 15.414999, "lng": -61.370976 },
    "DO": { "lat": 18.735693, "lng": -70.162651 },
    "EC": { "lat": -1.831239, "lng": -78.183406 },
    "EG": { "lat": 26.820553, "lng": 30.802498 },
    "SV": { "lat": 13.794185, "lng": -88.89653 },
    "GQ": { "lat": 1.650801, "lng": 10.267895 },
    "ER": { "lat": 15.179384, "lng": 39.782334 },
    "EE": { "lat": 58.595272, "lng": 25.013607 },
    "ET": { "lat": 9.145, "lng": 40.489673 },
    "FJ": { "lat": -16.578193, "lng": 179.414413 },
    "FI": { "lat": 61.92411, "lng": 25.748151 },
    "FR": { "lat": 46.603354, "lng": 1.888334 },
    "GA": { "lat": -0.803689, "lng": 11.609444 },
    "GM": { "lat": 13.443182, "lng": -15.310139 },
    "GE": { "lat": 42.315407, "lng": 43.356892 },
    "DE": { "lat": 51.165691, "lng": 10.451526 },
    "GH": { "lat": 7.946527, "lng": -1.023194 },
    "GR": { "lat": 39.074208, "lng": 21.824312 },
    "GD": { "lat": 12.262776, "lng": -61.604171 },
    "GT": { "lat": 15.783471, "lng": -90.230759 },
    "GN": { "lat": 9.945587, "lng": -9.696645 },
    "GW": { "lat": 11.803749, "lng": -15.180413 },
    "GY": { "lat": 4.860416, "lng": -58.93018 },
    "HT": { "lat": 18.971187, "lng": -72.285215 },
    "HN": { "lat": 15.199999, "lng": -86.241905 },
    "HU": { "lat": 47.162494, "lng": 19.503304 },
    "IS": { "lat": 64.963051, "lng": -19.020835 },
    "IN": { "lat": 20.593684, "lng": 78.96288 },
    "ID": { "lat": -0.789275, "lng": 113.921327 },
    "IR": { "lat": 32.427908, "lng": 53.688046 },
    "IQ": { "lat": 33.223191, "lng": 43.679291 },
    "IE": { "lat": 53.41291, "lng": -8.24389 },
    "IL": { "lat": 31.046051, "lng": 34.851612 },
    "IT": { "lat": 41.87194, "lng": 12.56738 },
    "JM": { "lat": 18.109581, "lng": -77.297508 },
    "JP": { "lat": 36.204824, "lng": 138.252924 },
    "JO": { "lat": 30.585164, "lng": 36.238414 },
    "KZ": { "lat": 48.019573, "lng": 66.923684 },
    "KE": { "lat": -0.023559, "lng": 37.906193 },
    "KI": { "lat": -3.370417, "lng": -168.734039 },
    "KP": { "lat": 40.339852, "lng": 127.510093 },
    "KR": { "lat": 35.907757, "lng": 127.766922 },
    "XK": { "lat": 42.602636, "lng": 20.902977 },
    "KW": { "lat": 29.31166, "lng": 47.481766 },
    "KG": { "lat": 41.20438, "lng": 74.766098 },
    "LA": { "lat": 19.85627, "lng": 102.495496 },
    "LV": { "lat": 56.879635, "lng": 24.603189 },
    "LB": { "lat": 33.854721, "lng": 35.862285 },
    "LS": { "lat": -29.609988, "lng": 28.233608 },
    "LR": { "lat": 6.428055, "lng": -9.429499 },
    "LY": { "lat": 26.3351, "lng": 17.228331 },
    "LI": { "lat": 47.166, "lng": 9.555373 },
    "LT": { "lat": 55.169438, "lng": 23.881275 },
    "LU": { "lat": 49.815273, "lng": 6.129583 },
    "MK": { "lat": 41.608635, "lng": 21.745275 },
    "MG": { "lat": -18.766947, "lng": 46.869107 },
    "MW": { "lat": -13.254308, "lng": 34.301525 },
    "MY": { "lat": 4.210484, "lng": 101.975766 },
    "MV": { "lat": 3.202778, "lng": 73.22068 },
    "ML": { "lat": 17.570692, "lng": -3.996166 },
    "MT": { "lat": 35.937496, "lng": 14.375416 },
    "MH": { "lat": 7.131474, "lng": 171.184478 },
    "MR": { "lat": 21.00789, "lng": -10.940835 },
    "MU": { "lat": -20.348404, "lng": 57.552152 },
    "MX": { "lat": 23.634501, "lng": -102.552784 },
    "FM": { "lat": 7.425554, "lng": 150.550812 },
    "MD": { "lat": 47.411631, "lng": 28.369885 },
    "MC": { "lat": 43.750298, "lng": 7.412841 },
    "MN": { "lat": 46.862496, "lng": 103.846656 },
    "ME": { "lat": 42.708678, "lng": 19.37439 },
    "MA": { "lat": 31.791702, "lng": -7.09262 },
    "MZ": { "lat": -18.665695, "lng": 35.529562 },
    "MM": { "lat": 21.913965, "lng": 95.956223 },
    "NA": { "lat": -22.95764, "lng": 18.49041 },
    "NR": { "lat": -0.522778, "lng": 166.931503 },
    "NP": { "lat": 28.394857, "lng": 84.124008 },
    "NL": { "lat": 52.132633, "lng": 5.291266 },
    "NZ": { "lat": -40.900557, "lng": 174.885971 },
    "NI": { "lat": 12.865416, "lng": -85.207229 },
    "NE": { "lat": 17.607789, "lng": 8.081666 },
    "NG": { "lat": 9.081999, "lng": 8.675277 },
    "NO": { "lat": 60.472024, "lng": 8.468946 },
    "OM": { "lat": 21.512583, "lng": 55.923255 },
    "PK": { "lat": 30.375321, "lng": 69.345116 },
    "PW": { "lat": 7.51498, "lng": 134.58252 },
    "PA": { "lat": 8.537981, "lng": -80.782127 },
    "PG": { "lat": -6.314993, "lng": 143.95555 },
    "PY": { "lat": -23.442503, "lng": -58.443832 },
    "PE": { "lat": -9.189967, "lng": -75.015152 },
    "PH": { "lat": 12.879721, "lng": 121.774017 },
    "PL": { "lat": 51.919438, "lng": 19.145136 },
    "PT": { "lat": 39.399872, "lng": -8.224454 },
    "QA": { "lat": 25.354826, "lng": 51.183884 },
    "RO": { "lat": 45.943161, "lng": 24.96676 },
    "RU": { "lat": 61.52401, "lng": 105.318756 },
    "RW": { "lat": -1.940278, "lng": 29.873888 },
    "KN": { "lat": 17.357822, "lng": -62.782998 },
    "LC": { "lat": 13.909444, "lng": -60.978893 },
    "VC": { "lat": 13.252817, "lng": -61.287228 },
    "WS": { "lat": -13.759029, "lng": -172.104629 },
    "SM": { "lat": 43.94236, "lng": 12.457777 },
    "ST": { "lat": 0.18636, "lng": 6.613081 },
    "SA": { "lat": 23.885942, "lng": 45.079162 },
    "SN": { "lat": 14.497401, "lng": -14.452362 },
    "RS": { "lat": 44.016521, "lng": 21.005859 },
    "SC": { "lat": -4.679574, "lng": 55.491977 },
    "SL": { "lat": 8.460555, "lng": -11.779889 },
    "SG": { "lat": 1.352083, "lng": 103.819836 },
    "SK": { "lat": 48.669026, "lng": 19.699024 },
    "SI": { "lat": 46.151241, "lng": 14.995463 },
    "SB": { "lat": -9.64571, "lng": 160.156194 },
    "SO": { "lat": 5.152149, "lng": 46.199616 },
    "ZA": { "lat": -30.559482, "lng": 22.937506 },
    "ES": { "lat": 40.463667, "lng": -3.74922 },
    "LK": { "lat": 7.873054, "lng": 80.771797 },
    "SD": { "lat": 12.862807, "lng": 30.217636 },
    "SR": { "lat": 3.919305, "lng": -56.027783 },
    "SZ": { "lat": -26.522503, "lng": 31.465866 },
    "SE": { "lat": 60.128161, "lng": 18.643501 },
    "CH": { "lat": 46.818188, "lng": 8.227512 },
    "SY": { "lat": 34.802075, "lng": 38.996815 },
    "TW": { "lat": 23.69781, "lng": 120.960515 },
    "TJ": { "lat": 38.861034, "lng": 71.276093 },
    "TZ": { "lat": -6.369028, "lng": 34.888822 },
    "TH": { "lat": 15.870032, "lng": 100.992541 },
    "TL": { "lat": -8.874217, "lng": 125.727539 },
    "TG": { "lat": 8.619543, "lng": 0.824782 },
    "TO": { "lat": -21.178986, "lng": -175.198242 },
    "TT": { "lat": 10.691803, "lng": -61.222503 },
    "TN": { "lat": 33.886917, "lng": 9.537499 },
    "TR": { "lat": 38.963745, "lng": 35.243322 },
    "TM": { "lat": 38.969719, "lng": 59.556278 },
    "TV": { "lat": -7.109535, "lng": 177.64933 },
    "UG": { "lat": 1.373333, "lng": 32.290275 },
    "UA": { "lat": 48.379433, "lng": 31.16558 },
    "AE": { "lat": 23.424076, "lng": 53.847818 },
    "GB": { "lat": 55.378051, "lng": -3.435973 },
    "US": { "lat": 37.09024, "lng": -95.712891 },
    "UY": { "lat": -32.522779, "lng": -55.765835 },
    "UZ": { "lat": 41.377491, "lng": 64.585262 },
    "VU": { "lat": -15.376706, "lng": 166.959158 },
    "VE": { "lat": 6.42375, "lng": -66.58973 },
    "VN": { "lat": 14.058324, "lng": 108.277199 },
    "YE": { "lat": 15.552727, "lng": 48.516388 },
    "ZM": { "lat": -13.133897, "lng": 27.849332 },
    "ZW": { "lat": -19.015438, "lng": 29.154857 }
};

var map = L.map('map');
map.setView([-0,-0], 1.4);

var BASE_URL = "<?php echo get_config("base_url"); ?>";
if (!!window.EventSource) {
    var source = new EventSource(BASE_URL+"api/notification.php");
    source.addEventListener('message', GetNotifs, false);
}

countrycounts = {};
function GetNotifs(e)
{
    var event = JSON.parse(e.data);
    if (event.subsystem !== "connect")
        return;

    if (event.event_id === "REMOTE_CLIENT_CONNECT"
    || event.event_id === "LOCAL_CLIENT_CONNECT")
    {
        let cc = event.client.geoip.country_code;
        const co = countryCenters[event.client.geoip.country_code];
        if (countrycounts[cc] != NaN && countrycounts[cc] > 0)
        {
            let v = document.getElementsByClassName('country-'+cc)[0];
            v.innerHTML = ++countrycounts[cc];
            v.classList.add('new-number');
            v.style.animation = 'none';
            v.offsetHeight;
            v.style.animation = null;
        }
        else {
            var numberIcon = L.divIcon({
                className: 'number-icon new-number country-'+cc,
                html: 1,
                iconSize: [30, 30],
                iconAnchor: [15, 15] // Center the icon
            });
            countrycounts[cc] = 1;
            L.marker([co.lat, co.lng], {icon: numberIcon}).addTo(map);
        }        
    }
    if (event.event_id === "REMOTE_CLIENT_DISCONNECT"
    || event.event_id === "LOCAL_CLIENT_DISCONNECT")
    {
        let cc = event.client.geoip.country_code;
        const co = countryCenters[cc];
        if (countrycounts[cc] && countrycounts[cc] > 0)
        {
            let v = document.getElementsByClassName('country-'+cc)[0];
            v.innerHTML = --countrycounts[event.client.geoip.country_code];
        }
        else {
            var numberIcon = L.divIcon({
                className: 'number-icon',
                html: --countrycounts[event.client.geoip.country_code],
                iconSize: [30, 30],
                iconAnchor: [15, 15] // Center the icon
            });
            L.marker([co.lat, co.lng], {icon: numberIcon}).addTo(map);
        }
        if (countrycounts[cc] == undefined || countrycounts[cc] < 1)
        {
            document.getElementsByClassName('country-'+cc)[0].remove();
        }
    }
}

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 10,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);


var users = <?php echo json_encode((object)$rpc->user()->getAll()); ?>;
var count = 0;
for (let i = 0; users[i] != null; i++)
{
    if (!users[i].geoip)
        continue;
    const cc = users[i].geoip.country_code;
    const coords = countryCenters[cc];
    if (countrycounts[cc])
    {
        let v = document.getElementsByClassName('country-'+cc)[0];
        v.innerHTML = ++countrycounts[cc];
    }
    else {
        countrycounts[cc] = 1;
        var numberIcon = L.divIcon({
            className: 'number-icon country-'+cc,
            html: 1,
            iconSize: [30, 30],
            iconAnchor: [15, 15] // Center the icon
        });
        L.marker([coords.lat, coords.lng], {icon: numberIcon}).addTo(map);
    }
}


    

</script>
<?php

require_once "../../inc/footer.php";
