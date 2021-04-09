<?php set_include_path(get_include_path().':'.$_SERVER['DOCUMENT_ROOT'].'/include'); ?>
<?php
/*
CN Calculator
by Curtis Tolan
Copyright 2007
http://c2t.org/

Created on 2007.06.16

=====================
Changelog
=====================
2007.06.25
- First public version
2007.06.27
- Fixed typo on Infra Upkeep calculations
- Fixed bug involving future Tech not applying to Infra Upkeep
2007.07.29
- Updated infra upkeep for all infrastructure levels over 5000
- Fixed bug with Intelligence Agencies
2007.08.05
- Added Environment field for more accuracy
2007.10.20
- Readded improvement/wonder cost
2007.10.30
- Changed all governments to reflect in-game changes.
2008.03.11
- Changed Affulent Population and Fine Jewelry to reflect in-game changes.
- TODO 4 new wonders.
2008.05.07
- Added copy/paste field.
2008.05.15
- Added 4 wonders that were added a few months ago.
- Added Global Radiation Level field
- Fixed bug with Nuclear Position field
- TODO numbers for 15k+ infra
2008.05.19
- Added Environment level indicator
- Changed a few governments to reflect in-game changes.
2008.07.24
- ADD 4 new improvements concerning Navy
- ADD Mining Idustry Consortium wonder
- TODO Land equations
- TODO At least 2 other wonders
2008.08.15
- ADD Numbers for 15k+ infra
2009.03.24
- ADD Military
- ADD ADP and all military Wonders
- ADD Environment calculations
- ADD Nation Strength calculations
- ADD Land Area calculations
- ADD Soldiers affecting income notification
- FIX Sugar and Wheat no longer affect Land Area
- FIX Lead halves Nuke affect on Environment
2009.04.16 (stealth update)
- REVERT Soldier efficiencies
2009.08.31
- ADD Can now copy/paste another nation's status screen to be filled into the calculator.
2009.09.12 (stealth update) (rough date estimate)
- UNHIDE Calculated Happiness from public version.
2009.12.02
- FIX NEO was doing what Mining Industry was supposed to in addition to what it normally does.
- FIX Mining Industry Consortium now calculates correctly.
2010.04.07
- Moved from array-based searches to regex
- FEAT Can now copy/paste source code of nation summary page to fill in form.
2010.05.06
- FIX Copy/paste wouldn't catch the last improvement.
2011.09.21
- FIX Some of the resources changed some time after the last update.
2013.04.10
- Now calculates bulk infra purchases by 100s instead of 10s.
2013.04.11
- Fix Calculation bug with Fur.
2013.04.20
- Code cleanup
- Better copy/paste explaination
2013.04.24
- Add note to top of rendered page about Mars and Moon wonders and resources not accounted for.
2013.05.05 (test build)
- ADD Mars and Moon wonders and resources
- ADD 1 level of optimal taxation
2014.02.16
- ADD 4 wonders new to game
2014.02.24
- Partial merge of 2013.05.05 and latest
2014.03.22
- FIX wonder counting bug with Mars and Moon
- FIX Nuclear Power Plant not applying improvement and wonder upkeep reduction
2014.03.29
- FIX reading happiness from copy/paste of source code of nation page
2014.04.21
- FIX Nuclear Power Plant with nuclear position stuff
2014.06.06
- ADD 7 improvements added to the game recently
- Crime system that will be in-game soon is partially done
2014.06.09
- FIX reading some items that moved around on the nation page
2014.06.
- ADD 5 improvements added to the game dealing with crime
=====================

*/

// Multi-dimensional arrays! Huzzah!

/*
2d key names:
max
cost
Income %
Income $
Soldier Cost
Soldier %
Soldier Upkeep
Population
Happiness
Environment
Missile Cost
Tank Cost
Tank Upkeep
Infra Cost
Infra Upkeep
Aid Slot
Trade Slot
Missile Attack
Missile Defense
Literacy
Tech Cost
Aircraft Cost
Aircraft Upkeep
Land Cost
Misc
Environment
Land Area
Crime Score
Crime Score %
Criminal
Criminal %
Rehab

*/

$cn_improvements = array(
	'Airport' => array('max' => 3,'Aircraft Cost' => -0.02,'Aircraft Upkeep' => -0.02),
	'Bank' => array('max' => 7,/*'cost' => 100000,*/'Income %' => 0.07),
	'Barracks' => array('max' => 5,/*'cost' => 50000,*/'Soldier %' => 0.1,'Soldier Upkeep' => -0.1),
	'Border Fortifications' => array('max' => 3),
	'Border Walls' => array('max' => 5,/*'cost' => 60000,*/'Population' => -0.02,'Happiness' => 2,'Environment' => -1,'Criminal %' => -0.01),
	'Bunker' => array('max' => 3),
	//'Casino' => array('max' => 2,'Happiness' => 1.5,'Income %' => -0.01,'Crime Score' => -50),
	'Church' => array('max' => 5,/*'cost' => 40000,*/'Happiness' => 1),
	'Clinic' => array('max' => 5,/*'cost' => 50000,*/'Population' => 0.02),
	'Drydock' => array('max' => 5,/*'cost' => 100000,*/'Misc' => 'Land ships +1 each'),
	'Factory' => array('max' => 5,/*'cost' => 80000,*/'Missile Cost' => -0.05,'Tank Cost' => -0.1,'Infra Cost' => -0.08),
	'Foreign Ministry' => array('max' => 1,/*'cost' => 120000,*/'Income %' => 0.05,'Aid Slot' => 1),
	'Forward Operating Base' => array('max' => 2),
	'Guerrilla Camp' => array('max' => 5,/*'cost' => 20000,*/'Soldier %' => 0.35,'Soldier Upkeep' => -0.1, 'Income %' => -0.08),
	'Harbor' => array('max' => 1,/*'cost' => 200000,*/'Income %' => 0.01, 'Trade Slot' => 1),
	'Hospital' => array('max' => 1,/*'cost' => 180000,*/'Population' => 0.06),
	'Intelligence Agency' => array('max' => 5,/*'cost' => 38500,*/'Happiness' => 1),
	//'Jail' => array('max' => 5,'Criminal' => -500),
	'Labor Camp' => array('max' => 5,/*'cost' => 50000,*/'Infra Upkeep' => -0.1,'Happiness' => -1,'Criminal' => -200),
	'Missile Defense' => array('max' => 5,/*'cost' => 90000,*/'Missile Defense' => -0.1),
	'Munitions Factory' => array('max' => 5,'Environment' => 0.3),
	'Naval Academy' => array('max' => 2,/*'cost' => 300000,*/'Misc' => 'Ships strength +1 each'),
	'Naval Construction Yard' => array('max' => 3,/*'cost' => 300000,*/'Misc' => 'Ship build limit +1 each'),
	'Office of Propaganda' => array('max' => 2),
	'Police Headquarters' => array('max' => 5,/*'cost' => 75000,*/'Happiness' => 2),
	//'Prison' => array('max' => 5,'Criminal' => -5000),
	'Radiation Containment Chamber' => array('max' => 2),
	//'Red Light District' => array('max' => 2,'Happiness' => 1,'Environment' => 0.5,'Crime Score' => -50),
	//'Rehabilitation Facility' => array('max' => 5,'Rehab' => 500),
	'Satellite' => array('max' => 5,/*'cost' => 90000,*/'Missile Attack' => 0.1),
	'School' => array('max' => 5,/*'cost' => 85000,*/'Income %' => 0.05,'Literacy' => 0.01),
	'Stadium' => array('max' => 5,/*'cost' => 110000,*/'Happiness' => 3),
	'Shipyard' => array('max' => 5,/*'cost' => 100000,*/'Misc' => 'Sea ships +1 each'),
	'University' => array('max' => 2,/*'cost' => 180000,*/'Income %' => 0.08,'Tech Cost' => -0.1,'Literacy' => 0.03)
);
$cn_resources = array(
	'Aluminum' => array('Soldier %' => 0.2,'Infra Cost' => -0.07,'Aircraft Cost' => -0.08),
	'Cattle' => array('Population' => 0.05,'Land Cost' => -0.1),
	'Coal' => array('Land Area' => 0.15,'Soldier %' => 0.08,'Infra Cost' => -0.04,'Environment' => 1),
	'Fish' => array('Population' => 0.08,'Land Cost' => -0.05),
	'Furs' => array('Income $' => 3.5,'Land Growth' => 2),//not 3//land growth is calculated with the multipliers as result*=1+n
	'Gems' => array('Income $' => 1.5,'Happiness' => 2.5),
	'Gold' => array('Income $' => 3,'Tech Cost' => -0.05),
	'Iron' => array('Soldier Cost' => -3,'Infra Upkeep' => -0.1,'Infra Cost' => -0.05,'Tank Upkeep' => -0.05),
	'Lead' => array('Missile Cost' => -0.2,'Missile Upkeep' => -0.2,'Nuclear Cost' => -0.2,'Nuclear Upkeep' => -0.2,'Aircraft Upkeep' => -0.25,'Tank Cost' => -0.08,'Tank Upkeep' => -0.08,'Soldier Upkeep $' => -0.5,'Navy Upkeep' => -0.2),
	'Lumber' => array('Infra Cost' => -0.06,'Infra Upkeep' => -0.08),
	'Marble' => array('Infra Cost' => -0.1),
	'Oil' => array('Soldier Cost' => -3,'Happiness' => 1.5,'Soldier %' => 0.1,'Aircraft Cost' => -0.04,'Environment' => 1),
	'Pigs' => array('Soldier Upkeep $' => -0.5,'Soldier %' => 0.15,'Population' => 0.035),
	'Rubber' => array('Land Area' => 0.2,'Land Cost' => -0.1,'Misc' => 'triple the value of land when selling','Infra Cost' => -0.03,'Aircraft Cost' => -0.04),
	'Silver' => array('Income $' => 2,'Happiness' => 2),
	'Spices' => array('Land Area' => 0.08,'Happiness' => 2),
	'Sugar' => array('Population' => 0.03,'Happiness' => 1),
	'Uranium' => array('Infra Upkeep' => -0.03,'Misc' => 'allow development of nuclear weapons and ton of other stuff; need to fix','Environment' => 1, 'Nuclear Upkeep' => -0.5),
	'Water' => array('Misc' => 'increase number of citizens per mile before unhappiness by 50','Happiness' => 2.5,'Environment' => -1),
	'Wheat' => array('Population' => 0.08),
	'Wine' => array('Happiness' => 3)
);
$cn_resources_bonus = array(
	'Affluent Population' => array('Population' => 0.05),
	'Asphalt' => array('Infra Upkeep' => -0.05),
	'Automobiles' => array('Happiness' => 3),
	'Beer' => array('Happiness' => 2),
	'Construction' => array('Infra Cost' => -0.05,'Aircraft Limit' => 10),
	'Fast Food' => array('Happiness' => 2),
	'Fine Jewelry' => array('Happiness' => 3),
	'Microchips' => array('Happiness' => 2,'Tech Cost' => -0.08),
	'Radiation Cleanup' => array('Environment' => -1,'Global Radiation' => -0.5),
	'Scholars' => array('Income $' => 3),
	'Steel' => array('Infra Cost' => -0.02,'Navy Cost' => -0.15)
);
$cn_wonders = array(
	'Agriculture Development Program' => array(/*'cost' => 30000000,*/'Land Area' => 0.15,'Income $' => 2),
	'Anti-Air Defense Network' => array(/*'cost' => 40000000*/),
	'Central Intelligence Agency' => array(/*'cost' => 40000000*/),
	'Disaster Relief Agency' => array(/*'cost' => 40000000,*/'Population' => 0.03,'Aid Slot' => 1),
	'Fallout Shelter System' => array(/*'cost' => 40000000*/),
	'Federal Aid Commission' => array(/*'cost' => 25000000*/),
	'Federal Reserve' => array(),
	'Foreign Air Force Base' => array(/*'cost' => 35000000,*/'Aircraft Limit' => 20),
	'Foreign Naval Base' => array(),
	'Great Monument' => array(/*'cost' => 35000000,*/'Happiness' => 4),
	'Great Temple' => array(/*'cost' => 35000000,*/'Happiness' => 5),
	'Great University' => array(/*'cost' => 35000000,*/'Tech Cost' => -0.1),
	'Hidden Nuclear Missile Silo' => array(/*'cost' => 30000000,*/'Nuke Limit' => 5),
	'Interceptor Missile System' => array(),
	'Internet' => array(/*'cost' => 35000000,*/'Happiness' => 5),
	'Interstate System' => array(/*'cost' => 45000000,*/'Infra Cost' => -0.08,'Infra Upkeep' => -0.08),
	'Manhattan Project' => array(/*'cost' => 100000000*/),
	'Mining Industry Consortium' => array(/*'cost' => 25000000*/),
	'Movie Industry' => array(/*'cost' => 26000000,*/'Happiness' => 3),
	'National Environment Office' => array(/*'cost' => 100000000,*/'Environment' => -1,'Population' => 0.03,'Infra Upkeep' => -0.03),
	'National Research Lab' => array(/*'cost' => 35000000,*/'Population' => 0.05,'Tech Cost' => -0.03),
	'National War Memorial' => array(/*'cost' => 27000000,*/'Happiness' => 4),
	'Nuclear Power Plant' => array(/*'cost' => 75000000,*/'Infra Upkeep' => -0.05,/*'Wonder Upkeep' => -0.05,'Improvement Upkeep' => -0.05,*/'Improv/Wonder Upkeep' => -0.05),
	'Pentagon' => array(/*'cost' => 30000000*/),
	'Scientific Development Center' => array(/*'cost' => 150000000*/),
	'Social Security System' => array(/*'cost' => 40000000*/),
	'Space Program' => array(/*'cost' => 30000000,*/'Happiness' => 3,'Tech Cost' => -0.03,'Aircraft Cost' => -0.05),
	'Stock Market' => array(/*'cost' => 30000000,*/'Income $' => 10),
	'Strategic Defense Initiative' => array(/*'cost' => 75000000*/),
	'Superior Logistical Support' => array('Aircraft Upkeep' => -0.1,'Naval Upkeep' => -0.1,'Tank Upkeep' => -0.05),
	'Universal Health Care' => array(/*'cost' => 100000000,*/'Population' => 0.03,'Happiness' => 2),
	'Weapons Research Complex' => array(/*'cost' => 150000000,*/'Environment' => 1)
);

$cn_taxrates = array(
	'10% - 16%' => 0,
	'17% - 20%' => 1,
	'21% - 23%' => 3,
	'24% - 25%' => 5,
	'26% - 30%' => 7
);

$cn_arrays = array('cn_improvements','cn_resources','cn_resources_bonus','cn_wonders');

$cn_governments = array(
	'Anarchy' => array(
		'Crime Score' => -50,
		'Environment' => 1
	),
	'Capitalist' => array(
		'Crime Score' => 10,
		'Environment' => -1,
		'Land Area' => 0.05,
		'Infra Cost' => -0.05,
		'Improv/Wonder Upkeep' => -0.05
	),
	'Communist' => array(
		'Crime Score' => 50,
		'Environment' => 1,
		'Land Area' => 0.05,
		'Soldier %' => 0.08,
		'Military Upkeep' => -0.02,
		'Spy Odds' => 0.1
	),
	'Democracy' => array(
		'Crime Score' => 20,
		'Environment' => -1,
		'Happiness' => 1,
		'Soldier %' => 0.08
	),
	'Dictatorship' => array(
		'Crime Score' => 75,
		'Environment' => 1,
		'Soldier %' => 0.08,
		'Infra Cost' => -0.05,
		'Military Upkeep' => -0.02
	),
	'Federal' => array(
		'Crime Score' => 60,
		'Soldier %' => 0.08,
		'Infra Cost' => -0.05,
		'Improv/Wonder Upkeep' => -0.05
	),
	'Monarchy' => array(
		'Crime Score' => 40,
		'Happiness' => 1,
		'Infra Cost' => -0.05,
		'Land Area' => 0.05
	),
	'Republic' => array(
		'Crime Score' => 65,
		'Environment' => -1,
		'Land Area' => 0.05,
		'Infra Cost' => -0.05,
		'Spy Odds' => 0.1
	),
	'Revolutionary' => array(
		'Crime Score' => 50,
		'Happiness' => 1,
		'Infra Cost' => -0.05,
		'Improv/Wonder Upkeep' => -0.05
	),
	'Totalitarian' => array(
		'Crime Score' => 90,
		'Happiness' => 1,
		'Land Area' => 0.05,
		'Military Upkeep' => -0.02
	),
	'Transitional' => array(
		'Crime Score' => 100,
		'Environment' => 1,
		'Land Area' => 0.05,
		'Soldier %' => 0.08,
		'Improv/Wonder Upkeep' => -0.05,
		'Spy Odds' => 0.1
	)
);

$cn_events = array('Event Population %','Event Land %','Event Income','Event Happiness','Event Environment');

$cn_nuclear = array(
	'None' => array(),
	'Power' => array('Happiness' => -1),
	'Weapons' => array('Happiness' => -1)
);

$cn_stats = array('Infrastructure','Technology','Working Citizens','Citizen Income','Nation Strength','Happiness','Land Purchased','Land Growth');

$cn_military = array(
	'Soldiers' => array('upkeep' => 2),
	'Tanks' => array('upkeep' => 40),
	'Aircraft' => array('upkeep' => 200),
	'Aircraft Level' => array(),
	'Cruise Missiles' => array('upkeep' => 200),
	'Nukes' => array()
);

$cn_mm_mars = array('Mars','Moon');
$cn_mm_resource = array(-1=>'None','Basalt','Magnesium','Potasium','Sodium','Calcium','Radon','Silicon','Titanium');
$cn_mm_input = array('Mars','Base Efficiency','Base Expires','Colony Efficiency','Colony Expires','Colony Population','Mine Efficiency','Mine Expires');

$cn_all_input = array('cn_stats','cn_governments','cn_nuclear','cn_events','cn_improvements','cn_resources','cn_wonders','cn_taxrates','cn_military','cn_mm_input');
//$cn_environment = array(1 => 1,2,3,4,5,6,7,8,9,10,11);

// variable arrays
$zomgtwice = array('now','lat');
$zomg_qty = '_qty';
$zomg_mod = '_mod';
$zomg_buy = '_buy';

$now_qty = array();
$lat_qty = array();
$now_mod = array();
$lat_mod = array();
$now_buy = array();
$lat_buy = array();

$qty1 = $zomgtwice[0].$zomg_qty;
$qty2 = $zomgtwice[1].$zomg_qty;
$mod1 = $zomgtwice[0].$zomg_mod;
$mod2 = $zomgtwice[1].$zomg_mod;
$buy1 = $zomgtwice[0].$zomg_buy;
$buy2 = $zomgtwice[1].$zomg_buy;


// cleanse input and calculate modifiers
if ($_POST[$zomgtwice[0].'Government'] || $_POST['copypaste']) {
	foreach ($zomgtwice as $zomg) {
		$qty = $zomg.$zomg_qty;
		$mod = $zomg.$zomg_mod;

		if (!$_POST['copypaste']) {
			// inputs
			foreach ($cn_all_input as $array) {
				foreach (${$array} as $key => $value) {
					switch ($array) {
						case 'cn_governments':
							${$qty}['Government'] = cleanse_option_array($cn_governments,$_POST[$zomg.'Government']);
							break;
						case 'cn_nuclear':
							${$qty}['Nuclear Position'] = cleanse_option_array($cn_nuclear,$_POST[$zomg.'NuclearPosition']);
							break;
						case 'cn_taxrates':
							${$qty}['Tax Rate'] = cleanse_option_array($cn_taxrates,$_POST[$zomg.'TaxRate']);
							//${$qty}['Tax Rate'] = cleanse_number($_POST[$zomg.'TaxRate']);
							break;
						case 'cn_stats':
						case 'cn_events':
						case 'cn_mm_input':
							${$qty}[$value] = cleanse_number($_POST[$zomg.rmsp($value)]);
							break;
						case 'cn_improvements':
							${$qty}[$key] = cleanse_number($_POST[$zomg.rmsp($key)]);
							${$qty}['# Improvements'] += ${$qty}[$key];
							break;
						case 'cn_resources':
							${$qty}[$key] = cleanse_checkbox($_POST[$zomg.rmsp($key)]);
							break;
						case 'cn_wonders':
							${$qty}[$key] = cleanse_checkbox($_POST[$zomg.rmsp($key)]);
							${$qty}['# Wonders'] += ${$qty}[$key];
							break;
						case 'cn_military':
							${$qty}[$key] = cleanse_number($_POST[$zomg.rmsp($key)]);
							break;
					}
				}
			}

			// Mars/Moon input
			${$qty}['Mine Resource'] = cleanse_option_array($cn_mm_resource,$_POST[$zomg.'MineResource'],1);
			// misc input
			//${$qty}['Trade Bonuses'] = cleanse_number($_POST[$zomg.rmsp('Trade Bonuses')]);
			${$qty}['Preferred Government'] = cleanse_checkbox($_POST[$zomg.'PreferredGovernment']);
			${$qty}['90% Literacy'] = cleanse_checkbox($_POST[$zomg.'90%Literacy']);
			${$qty}['Environment'] = cleanse_number($_POST[$zomg.'Environment']);
			${$qty}['Global Radiation'] = cleanse_number($_POST[$zomg.'GlobalRadiation']);
			${$qty}['yesnoyesHardReset'] = cleanse_checkbox($_POST['yesnoyesHardReset']);
		}
		else {
			$temp = 0;
			//copypaste
			foreach ($cn_all_input as $array) {
				foreach (${$array} as $key => $value) {
					switch ($array) {
						case 'cn_governments':
							${$qty}['Government'] = cleanse_option_array($cn_governments,'Anarchy');
							break;
						case 'cn_nuclear':
							${$qty}['Nuclear Position'] = cleanse_option_array($cn_nuclear,'None');
							break;
						case 'cn_taxrates':
							${$qty}['Tax Rate'] = cleanse_option_array($cn_taxrates,'10%-16%');
							//${$qty}['Tax Rate'] = 10;
							break;
						case 'cn_stats':
						case 'cn_events':
							${$qty}[$value] = 0;
							break;
						case 'cn_improvements':
							${$qty}[$key] = 0;
							${$qty}['# Improvements'] = 0;
							break;
						case 'cn_resources':
						case 'cn_military':
							${$qty}[$key] = 0;
							break;
						case 'cn_wonders':
							${$qty}[$key] = 0;
							${$qty}['# Wonders'] = 0;
							break;
					}
				}
			}

			//${$qty}['Trade Bonuses'] = 0;
			${$qty}['Preferred Government'] = 0;
			${$qty}['90% Literacy'] = 0;
			${$qty}['Environment'] = 0;
			${$qty}['Global Radiation'] = 0;
			${$qty}['yesnoyesHardReset'] = 0;

			${$qty}['Mars'] = 1;
			${$qty}['Base Expires'] = -1;
			${$qty}['Colony Expires'] = -1;
			${$qty}['Mine Expires'] = -1;


			// start of regex searching
			// so much better than the old way

			$notags = preg_replace('/<script[^>]*><?[^<]*<\/script>/i','',$_POST['copypaste']);
			$notags = preg_replace('/<style[^>]*><?[^<]*<\/style>/i','',$notags);
			$notags = preg_replace('/<[^>]+?>/','',$notags);

			// get government from copypaste
			if (preg_match('/(?<=Government)\s*Type\s*:\s*(?:\[[^\[]+?\])*\s*-*\s*(?<data>\w+)/',$notags,$temp))
				${$qty}['Government'] = cleanse_option_array($cn_governments,$temp['data']);
			if (strstr($notags,'Your people are happy with this government.'))
				${$qty}['Preferred Government'] = 1;

			// get technology from copypaste
			if (preg_match('/(?<=Technology)\s*:\s*(?P<data>[0-9,\.]+)\./',$notags,$temp))
				${$qty}['Technology'] = cleanse_number($temp['data']);

			// get infrastructure from copypaste
			if (preg_match('/(?<=Infrastructure)(?:\s*:\s*)(?P<data>[0-9,\.]+)/',$notags,$temp))
				${$qty}['Infrastructure'] = cleanse_number($temp['data']);

			// get land from copypaste
			if (preg_match('/(?P<data>[0-9,\.]+)\s*in\s*(?=purchases,)/',$notags,$temp))
				${$qty}['Land Purchased'] = cleanse_number($temp['data']);
			if (preg_match('/(?P<data>[0-9,\.]+)\s*in\s*(?=growth)/',$notags,$temp))
				${$qty}['Land Growth'] = cleanse_number($temp['data']);

			// get tax rate from copypaste
			if (preg_match('/(?<=Tax)\s*Rate\s*:\s*(?P<data>[0-9]+)(?=%)/',$notags,$temp))
				//${$qty}['Tax Rate'] = cleanse_number($temp['data']);
				if ($temp['data'] >= 26)
					${$qty}['Tax Rate'] = '26% - 30%';
				elseif ($temp['data'] >= 24)
					${$qty}['Tax Rate'] = '24% - 25%';
				elseif ($temp['data'] >= 21)
					${$qty}['Tax Rate'] = '21% - 23%';
				elseif ($temp['data'] >= 17)
					${$qty}['Tax Rate'] = '17% - 20%';
				else
					${$qty}['Tax Rate'] = '10% - 16%';
				//*/

			// get resources from copypaste
			foreach ($cn_resources as $key => $value)
				if (preg_match("/$key\s*(-|&\#8211;|&\#8212;)/i",$_POST['copypaste']))
					${$qty}[$key] = 1;

			// get wonders from copypaste
			foreach ($cn_wonders as $key => $value) {
				$temp = str_replace(' ','\s*',$key);
				if (preg_match("/$temp/i",$notags)) {
					${$qty}[$key] = 1;
					${$qty}['# Wonders'] += 1;
				}
			}

			// get improvements from copypaste
			foreach ($cn_improvements as $key => $value) {
				$temp = str_replace(' ','\s*',$key);
				if (preg_match('/'.$temp.'*?(s|es|ies):\s*(?P<data>[1-7]),*/i',$notags,$temp2)) {
					${$qty}[$key] = cleanse_number($temp2['data']);
					${$qty}['# Improvements'] += cleanse_number($temp2['data']);
				}
			}

			// get environment from copypaste
			if (preg_match('/(?<=Environment)\s*:\s*(?:\[[^\[]+?\])*\s*(?P<data>[0-9\.]+)/',$notags,$temp))
				${$qty}['Environment'] = cleanse_number($temp['data']);
			//if (preg_match('/(?<=Global)\s*(?:Radiation)\s*:\s*(?P<data>[0-9\.]+)/',$notags,$temp))
			if (preg_match('/(?<=GRL)\s*:\s*(?P<data>[0-9\.]+)/',$notags,$temp))
				${$qty}['Global Radiation'] = cleanse_number($temp['data']);

			// get nation strength from copypaste
			if (preg_match('/(?<=Nation)\s*Strength\s*:\s*(?P<data>[0-9\.,]+)/',$notags,$temp))
				${$qty}['Nation Strength'] = cleanse_number($temp['data']);

			// get military from copypaste
			if (preg_match('/(?<=Number)\s*of\s*(?:Soldiers)\s*:\s*(?P<data>[0-9,]+)/',$notags,$temp))
				${$qty}['Soldiers'] = cleanse_number($temp['data']);
			if (preg_match('/(?<=Number)\s*of\s*(?:Tanks)\s*:\s*(?P<data>[0-9,]+)/',$notags,$temp))
				${$qty}['Tanks'] = cleanse_number($temp['data']);
			if (preg_match('/(?<=Aircraft)\s*:\s*(?P<data>[0-9,]+)/',$notags,$temp))
				${$qty}['Aircraft'] = cleanse_number($temp['data']);
			if (preg_match('/(?<=Cruise)\s*Missiles\s*:\s*(?P<data>[0-9,]+)/',$notags,$temp))
				${$qty}['Cruise Missiles'] = cleanse_number($temp['data']);
			if (preg_match('/(?<=Nuclear)\s*Weapons\s*:\s*(?P<data>[0-9,]+)/',$notags,$temp))
				${$qty}['Nukes'] = cleanse_number($temp['data']);

			// get happiness from copypaste
			if (preg_match('/(?<=Happiness)\s*:\s*(?:\[[^\[]+?\])*\s*(?P<data>[0-9\.]+)/',$notags,$temp))
				${$qty}['Happiness'] = cleanse_number($temp['data']);
			elseif (preg_match('/(?P<data>[0-9\.]+)\s*-\s*Your\s*Population\s*\w+\s*\w+/',$notags,$temp))
				${$qty}['Happiness'] = cleanse_number($temp['data']);

			// get population from copypaste
			if (preg_match('/(?P<data>[0-9,]+)\s*Working\s*(?=Citizens)/',$notags,$temp))
				${$qty}['Working Citizens'] = cleanse_number($temp['data']);
			elseif (preg_match('/(?P<data>[0-9,]+)\s*(?=Supporters)/',$notags,$temp))
				${$qty}['Working Citizens'] = cleanse_number($temp['data']) - ${$qty}['Soldiers'];

			// get income from copypaste
			if (preg_match('/(?<=Individual)\s*Per\s*Day\s*:\s*(?P<data>[0-9\.,\$]+)/',$notags,$temp))
				${$qty}['Citizen Income'] = cleanse_number($temp['data']);


			// get Mars/Moon wonders and resource from copypaste
			// Base
			if (preg_match('/((Mars|Moon)\sBase),.*(?=Space\sProgram)/',$notags,$temp)) {
				if ($temp[2] === 'Mars')
					${$qty}['Mars'] = 1;
				else
					${$qty}['Mars'] = 0;
				${$qty}['Base Efficiency'] = 100;
				//${$qty}['# Wonders'] += 1;
			}
			else
				${$qty}['Base Efficiency'] = 0;
			// Colony
			if (preg_match('/((Mars|Moon)\sColony),.*(?=Space\sProgram)/',$notags,$temp)) {
				if ($temp[2] === 'Mars')
					${$qty}['Mars'] = 1;
				else
					${$qty}['Mars'] = 0;
				${$qty}['Colony Efficiency'] = 100;
				//${$qty}['# Wonders'] += 1;

			}
			else
				${$qty}['Colony Efficiency'] = 0;
			// Colony Population
			if (preg_match("/(?<=Working\sCitizens)\s*\(([0-9,]+)\sFrom\s(Mars|Moon)\sColony\)/i",$notags,$temp))
				${$qty}['Colony Population'] = cleanse_number($temp[1]);
			else
				${$qty}['Colony Population'] = 0;
			// Mine
			if (preg_match('/((Mars|Moon)\sMine),.*(?=Space\sProgram)/',$notags,$temp)) {
				if ($temp[2] === 'Mars')
					${$qty}['Mars'] = 1;
				else
					${$qty}['Mars'] = 0;
				${$qty}['Mine Efficiency'] = 100;
				//${$qty}['# Wonders'] += 1;
			}
			else
				${$qty}['Mine Efficiency'] = 0;
			// Mine Resource
			if (preg_match("/(Basalt|Magnesium|Potassium|Sodium|Calcium|Radon|Silicon|Titanium)\s*(-|&\#8211;|&\#8212;)/i",$_POST['copypaste'],$temp))
				${$qty}['Mine Resource'] = $temp[1];
			else
				${$qty}['Mine Resource'] = 'None';


			// assumptions stated on info page
			if (${$qty}['Technology'] >= 500)
				${$qty}['Aircraft Level'] = 9;
			if (${$qty}['Technology'] >= 700)
				${$qty}['90% Literacy'] = 1;
		}

		// Mars and Moon initialisation of remainder
		${$qty}['Base Max Days'] = mm_max_days(${$qty}['Mars'],1,-1);
		${$qty}['Base Expires'] =   mm_expires(${$qty}['Mars'],1,(${$qty}['Base Expires']?${$qty}['Base Expires']:-1),${$qty}['Base Max Days']);
		${$qty}['Colony Max Days'] = mm_max_days(${$qty}['Mars'],0,-1);
		${$qty}['Colony Expires'] =   mm_expires(${$qty}['Mars'],0,(${$qty}['Colony Expires']?${$qty}['Colony Expires']:-1),${$qty}['Colony Max Days']);
		${$qty}['Mine Max Days'] = mm_max_days(${$qty}['Mars'],0,-1);
		${$qty}['Mine Expires'] =   mm_expires(${$qty}['Mars'],0,(${$qty}['Mine Expires']?${$qty}['Mine Expires']:-1),${$qty}['Mine Max Days']);
		${$qty}['Base Efficiency'] =   mm_efficiency(${$qty}['Base Efficiency']);
		${$qty}['Colony Efficiency'] = mm_efficiency(${$qty}['Colony Efficiency']);
		${$qty}['Mine Efficiency'] =   mm_efficiency(${$qty}['Mine Efficiency']);

		if (${$qty}['Base Efficiency'])
			${$qty}['# Wonders'] += 1;
		if (${$qty}['Colony Efficiency'])
			${$qty}['# Wonders'] += 1;
		if (${$qty}['Mine Efficiency'])
			${$qty}['# Wonders'] += 1;

		// set bonus resources
		if (${$qty}['Coal'] && ${$qty}['Iron'])
			${$qty}['Steel'] = 1;
		if (${$qty}['Cattle'] && ${$qty}['Sugar'] && ${$qty}['Spices'] && ${$qty}['Pigs'])
			${$qty}['Fast Food'] = 1;
		if (${$qty}['Aluminum'] && ${$qty}['Wheat'] && ${$qty}['Water'] && ${$qty}['Lumber'])
			${$qty}['Beer'] = 1;
		if (${$qty}['Gold'] && ${$qty}['Silver'] && ${$qty}['Gems'] && ${$qty}['Coal'])
			${$qty}['Fine Jewelry'] = 1;
		if (${$qty}['Fine Jewelry'] && ${$qty}['Fish'] && ${$qty}['Furs'] && ${$qty}['Wine'])
			${$qty}['Affluent Population'] = 1;
		if (${$qty}['Lumber'] && ${$qty}['Iron'] && ${$qty}['Marble'] && ${$qty}['Aluminum'] && ${$qty}['Technology'] > 5)
			${$qty}['Construction'] = 1;
		if (${$qty}['Construction'] && ${$qty}['Oil'] && ${$qty}['Rubber'])
			${$qty}['Asphalt'] = 1;
		if (${$qty}['Asphalt'] && ${$qty}['Steel'])
			${$qty}['Automobiles'] = 1;
		if (${$qty}['Gold'] && ${$qty}['Oil'] && ${$qty}['Lead'] && ${$qty}['Technology'] > 10)
			${$qty}['Microchips'] = 1;
		if (${$qty}['Construction'] && ${$qty}['Microchips'] && ${$qty}['Steel'] && ${$qty}['Technology'] > 15)
			${$qty}['Radiation Cleanup'] = 1;
		if (${$qty}['Lumber'] && ${$qty}['Lead'] && ${$qty}['90% Literacy']) // needs literacy
			${$qty}['Scholars'] = 1;

		// environment
		if (${$qty}['Global Radiation'] > 5 || ${$qty}['Global Radiation'] < 0) ${$qty}['Global Radiation'] = 0;
		if ((${$qty}['Environment'] - ${$qty}['Global Radiation']) <= 0.99999) {
			${$qty}['Environment'] = 1;
			${$qty}['Global Radiation'] = 0;
		}
		elseif (${$qty}['Environment'] > 20) {
			${$qty}['Environment'] = 20;
		}

		//Bank check for Federal Reserve Wonder
		if (!${$qty}['Federal Reserve'] && ${$qty}['Bank'] > 5)
			${$qty}['Bank'] = 5;

		// initialize modifiers
		${$mod} = array(
			'Population' => 1,
			'Infra Cost' => 1,
			'Infra Upkeep' => 1,
			'Happiness' => 0,
			'Income $' => 0,
			'Income %' => 1,
			'----1----' => -100,
			'Tech Cost' => 1,
			'Land Cost' => 1,
			'Land Area' => 1,
			'Land Growth' => 1,
			'Environment' => 0,
			'Global Radiation' => 1,
			'Literacy' => 1,
			'Crime Score' => 0,
			'Crime Score %' => 1,
			'Criminal' => 0,
			'Criminal %' => 1,
			'Rehab' => 0,
			'----2----' => -100,
			'Soldier Cost' => 0,
			'Soldier Upkeep' => 1,
			'Soldier Upkeep $' => 0,
			'Soldier %' => 1,
			'Tank Cost' => 1,
			'Tank Upkeep' => 1,
			'Aircraft Cost' => 1,
			'Aircraft Upkeep' => 1,
			'Missile Cost' => 1,
			'Missile Upkeep' => 1,
			'Nuclear Cost' => 1,
			'Nuclear Upkeep' => 1,
			'Navy Cost' => 1,
			'Navy Upkeep' => 1,
//			'Missile Attack' => 1,
//			'Missile Defense' => 1,
			'----3----' => -100,
			'Improv/Wonder Upkeep' => 1,
			'Military Upkeep' => 1,
//			'Spy Odds' => 1,
			'----4----' => -100
//			'Misc' => NULL
		);

		// set modifiers
		foreach (${$mod} as $modkey => $modvalue) {
			if ($modvalue == 1) {
				${$mod}[$modkey] *= 1 + $cn_governments[${$qty}['Government']][$modkey];
				foreach ($cn_arrays as $array)
					foreach (${$array} as $itemkey => $itemvalue)
						${$mod}[$modkey] *= 1 + ${$qty}[$itemkey] * ${$array}[$itemkey][$modkey];
			}
			elseif ($modvalue == 0) {
				${$mod}[$modkey] += $cn_governments[${$qty}['Government']][$modkey];
				foreach ($cn_arrays as $array)
					foreach (${$array} as $itemkey => $itemvalue)
						${$mod}[$modkey] += ${$qty}[$itemkey] * ${$array}[$itemkey][$modkey];
			}
		}

		// nukes
		if (${$qty}['Nukes']) {
			${$mod}['Environment'] += (${$qty}['Lead'] ? ${$qty}['Nukes'] * 0.05 : ${$qty}['Nukes'] * 0.1);
			${$qty}['Nuclear Position'] = 'Weapons';
		}

		// uranium and Nuclear Power Plant
		switch (${$qty}['Nuclear Position']) {
			case 'None':
				break;
			case 'Weapons':
				${$mod}['Environment'] += 1;
				if (!${$qty}['Nuclear Power Plant'])
					break;
				if (${$qty}['Uranium'])
					${$mod}['Happiness'] += 1; //NPP on Weapons position negates the happiness penalty of uranium
			case 'Power':
				if (${$qty}['Uranium']) {
					${$mod}['Happiness'] -= 1;
					if (${$qty}['Technology'] < 30)
						${$mod}['Income $'] += 3 + 0.15 * ${$qty}['Technology'];
					else
						${$mod}['Income $'] += 3 + 0.15 * 30;
				}
				elseif (!${$qty}['Uranium'] && (${$qty}['Nuclear Position'] !== 'Weapons')) {
					${$mod}['Environment'] += 1;
				}
				break;
		}

		// events
		${$mod}['Population'] *= 1 + ${$qty}['Event Population %']/100;
		${$mod}['Income $'] += ${$qty}['Event Income'];
/**/	${$mod}['Happiness'] += ${$qty}['Event Happiness']/* + ${$qty}['Trade Bonuses']*/ + ${$qty}['Preferred Government'];
		${$mod}['Land Area'] *= 1 + ${$qty}['Event Land %']/100;
		${$mod}['Environment'] += ${$qty}['Event Environment'];

		// 'Scientific Development Center'
		if (${$qty}['Scientific Development Center']) {
			${$mod}['Infra Cost'] *= (1 + ${$qty}['Factory'] * -0.1)/(1 + ${$qty}['Factory'] * $cn_improvements['Factory']['Infra Cost']);
			${$mod}['Income %'] *= (1 + ${$qty}['University'] * 0.1)/(1 + ${$qty}['University'] * $cn_improvements['University']['Income %']);
			$temp = 5000; // for tech happiness limit for Great University
		}
		else
			$temp = 3000; // for tech happiness limit for Great University

		// tech happiness bonus, with Great University and variable for Scientific Development Center
		if (${$qty}['Technology'] == 0)
			${$mod}['Happiness'] += -1;
		elseif (${$qty}['Technology'] > 0 && ${$qty}['Technology'] <= 0.5)
			${$mod}['Happiness'] += 0;
		elseif (${$qty}['Technology'] > 0.5 && ${$qty}['Technology'] <= 1)
			${$mod}['Happiness'] += 1;
		elseif (${$qty}['Technology'] > 1 && ${$qty}['Technology'] <= 3)
			${$mod}['Happiness'] += 2;
		elseif (${$qty}['Technology'] > 3 && ${$qty}['Technology'] <= 6)
			${$mod}['Happiness'] += 3;
		elseif (${$qty}['Technology'] > 6 && ${$qty}['Technology'] <= 10)
			${$mod}['Happiness'] += 4;
		elseif (${$qty}['Technology'] > 10)
			${$mod}['Happiness'] += 5;
		if (${$qty}['Technology'] > 15) {
			if (${$qty}['Technology'] <= 200)
				${$mod}['Happiness'] += ${$qty}['Technology'] * 0.02;
			else
				${$mod}['Happiness'] += 4; // same as += 200 * 0.02;
		}
		if (${$qty}['Technology'] > 200 && ${$qty}['Great University']) {
			if (${$qty}['Technology'] <= $temp)
				${$mod}['Happiness'] += (${$qty}['Technology'] - 200) * 0.002;
			else
				${$mod}['Happiness'] += ($temp - 200) * 0.002;
		}

		// 'National Environment Office'
		if (${$qty}['National Environment Office'])
			${$mod}['Environment'] -= ${$qty}['Coal'] + ${$qty}['Oil'] + ${$qty}['Uranium'];

		// 'Mining Industry Consortium'
		if (${$qty}['Mining Industry Consortium'])
			${$mod}['Income $'] += (${$qty}['Coal'] + ${$qty}['Oil'] + ${$qty}['Uranium'] + ${$qty}['Lead']) * 2;

		// tax happiness
		if (${$qty1}['Tax Rate'] != '26% - 30%' && ${$qty1}['Tax Rate'] != '24% - 25%')
		//if (${$qty1}['Tax Rate'] < 24)
			${$mod}['Happiness'] -= ${$qty}['Intelligence Agency'];

		// Great Monument Wonder
		if (${$qty}['Great Monument'] && !${$qty}['Preferred Government'] && (${$qty}['Government'] != 'Anarchy'))
			${$mod}['Happiness'] += 1;

		// Mars/Moon Wonders mod
		if (${$qty}['Base Efficiency']) {
			${$mod}['Happiness'] += mm_happiness(${$qty}['Mars'],1,${$qty}['Base Expires'],${$qty}['Base Max Days']);
			${$mod}['Infra Cost'] *= mm_base_infra_reduce(${$qty}['Mars'],${$qty}['Base Efficiency']);
			${$mod}['Infra Upkeep'] *= mm_base_infra_reduce(${$qty}['Mars'],${$qty}['Base Efficiency']);
		}
		if (${$qty}['Colony Efficiency']) {
			${$mod}['Happiness'] += mm_happiness(${$qty}['Mars'],0,${$qty}['Colony Expires'],${$qty}['Colony Max Days']);
		}
		if (${$qty}['Mine Efficiency']) {
			${$mod}['Happiness'] += mm_happiness(${$qty}['Mars'],0,${$qty}['Mine Expires'],${$qty}['Mine Max Days']) + mm_resource_happiness(${$qty});
			${$mod}['Income $'] += mm_resource_income(${$qty});
			${$mod}['Infra Cost'] *= mm_resource_infra_cost(${$qty});
			${$mod}['Infra Upkeep'] *= mm_resource_infra_upkeep(${$qty});
			${$mod}['Global Radiation'] *= mm_resource_grl(${$qty});
		}

		// Radiation Containment Chamber
		if (${$qty}['Radiation Cleanup'])
			${$mod}['Global Radiation'] *= 1 + (${$qty}['Radiation Containment Chamber'] * -0.2);

		// Rehabilitation Facility ?
		// null
	}
}
else { // if not in post, make Mars 1 for consistency
	${$qty1}['Mars'] = ${$qty2}['Mars'] = 1;
}

// finding base values and bulk infra purchase
if ($_POST[$zomgtwice[0].'Government']) {
	$qty = $qty1;
	$mod = $mod1;

	// base values
	$basecalc = array();
	$basecalc['Land Growth'] = ${$qty1}['Land Growth'] / ${$mod1}['Land Growth'];
	$basecalc['Land Area 1'] = ${$qty1}['Land Purchased'] * ${$mod1}['Land Area'] * (1 + ${$qty1}['Event Land %']/100) + $basecalc['Land Growth'] * ${$mod1}['Land Growth'];
	$basecalc['Land Area 2'] = ${$qty2}['Land Purchased'] * ${$mod2}['Land Area'] * (1 + ${$qty2}['Event Land %']/100) + $basecalc['Land Growth'] * ${$mod2}['Land Growth'];

	$basecalc['Colony Population'] = ${$qty1}['Colony Efficiency'] ? ${$qty1}['Colony Population'] : 0;
//mm //doublecheck
//	$basecalc['Working Citizens 1'] = citizens_to_unmodcit(${$qty}['Working Citizens'],${$qty}['Soldiers'],${$qty}['Environment'],${$mod}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty1}['Mars']);
	$basecalc['Working Citizens 1'] = citizens_to_unmodcit(${$qty}['Working Citizens'],${$qty}['Soldiers'],${$qty}['Environment'],${$mod}['Population']);
	$basecalc['Working Citizens 2'] = $basecalc['Working Citizens 1'] - $basecalc['Land Area 1'] * mans_per_land(${$qty1}['Agriculture Development Program']) + $basecalc['Land Area 2'] * mans_per_land(${$qty2}['Agriculture Development Program']);
	$basecalc['Infra Cost'] = infra_cost(${$qty}['Infrastructure']);
	$basecalc['Infra Upkeep'] = infra_upkeep(${$qty}['Infrastructure']);
	$basecalc['Environment'] = ${$qty}['Environment'] - ${$mod}['Environment'] - soldier_environment(${$qty}['Soldiers'],${$qty}['Working Citizens'],${$mod1}['Soldier %']) - ${$qty}['Global Radiation'] - infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 1']);
	$basecalc['Global Radiation'] = ${$qty}['Global Radiation']/${$mod}['Global Radiation'];
	$basecalc['Happiness'] = ${$qty}['Happiness']/(1-${$qty}['Environment']/100) - ${$mod}['Happiness'] - soldier_happiness(${$qty1}['Soldiers'],${$qty1}['Working Citizens'],${$mod1}['Soldier %']);
	$basecalc['Citizen Income'] = ${$qty}['Citizen Income']/${$mod}['Income %'] - ${$mod}['Income $'] - 2*(${$qty}['Happiness']-$basecalc['Happiness']);

	$basecalc['Nation Strength'] = ${$qty}['Nation Strength'] - nation_strength(
			${$qty}['Land Purchased'],
			${$qty}['Tanks'],
			${$qty}['Cruise Missiles'],
			${$qty}['Nukes'],
			${$qty}['Technology'],
			${$qty}['Infrastructure'],
			${$qty}['Soldiers'],
			${$qty}['Aircraft'],
			${$qty}['Aircraft Level']
		);
	${$qty2}['Nation Strength'] = $basecalc['Nation Strength'] + nation_strength(
			${$qty2}['Land Purchased'],
			${$qty2}['Tanks'],
			${$qty2}['Cruise Missiles'],
			${$qty2}['Nukes'],
			${$qty2}['Technology'],
			${$qty1}['Infrastructure'],
			${$qty2}['Soldiers'],
			${$qty2}['Aircraft'],
			${$qty2}['Aircraft Level']
		);



	// bulk infra purchase
	if (cleanse_number($_POST['yesnoyesPurchase']) <= 50000 && cleanse_number($_POST['yesnoyesPurchase']) >= -50000) { // 50000 is an arbitrary max for buy/sell; 500 loops max
		foreach ($zomgtwice as $zomg) {
			$qty = $zomg.$zomg_qty;
			$mod = $zomg.$zomg_mod;
			$buy = $zomg.$zomg_buy;
			$chunkSize = 100; //max number of infra that can be bought at a time
			${$buy}['need'] = cleanse_number($_POST['yesnoyesPurchase']); // how much infra to buy
			${$buy}['pop+'] = ${$buy}['need'] * 7.5; // base population gained from purchase
//			${$buy}['$$$$'] = ${$buy}['need'] * 0.001; // income skew due to infra gain? cannot pinpoint value or equation at low infra levels; does not affect mid to high infra levels; might not even exist anymore
			${$buy}['cost'] = 0;
			$temp = ${$buy}['need'] - (${$buy}['need'] % $chunkSize); // temp is amount of infra to buy without remainder

			for ($i = 0;$i < $temp;$i += $chunkSize)
				${$buy}['cost'] += $chunkSize * infra_cost(${$qty1}['Infrastructure'] + $i) * ${$mod}['Infra Cost'];
			${$buy}['cost'] += (${$buy}['need'] - $temp) * infra_cost(${$qty1}['Infrastructure'] + $temp) * ${$mod}['Infra Cost'];
		}
	}
}


// header
$ztitle = 'Cybernations Calculator';
require_once('header.php');
?>
<div id="header">
<h1>.: <?=$ztitle;?> ::.:.</h1>
<?/*<p><b>Update for 2008.05.15:</b></p><ul>
<li>ADD: 4 wonders that were new to the game 2 months ago.</li>
<li>ADD: Global Radiation field.</li>
<li>FIX: Bugs with the Nuclear Position field.</li>
<li>DEL: Trade Bonuses field.</li>
<li>NOTE: The copy/paste field will still not fill out the following fields: Perferred Gov?, 90% Literacy, Nuclear Position, and all Event fields.</li>
</ul>
<p><b>Update for 2008.08.15:</b></p><ul>
<li>ADD: Numbers for 15k+ infra</li>
</ul><p><b>Update for 2008.07.24:</b></p><ul>
<li>ADD: 4 new improvements concerning Navy</li>
<li>ADD: Mining Idustry Consortium wonder</li>
<li>TODO: Land equations</li>
<li>TODO: At least 2 other wonders (ADP and WRC)</li>
</ul><p>WARNING: If the Environment level changes from Current to Future, the Working Citizen count on the Future side will NOT be correct until a formula can be figured out.</p>
<p>For comments, questions, and bug reports, use this thread: <a href="http://c2t.org/read.php?1,31">http://c2t.org/read.php?1,31</a>
</p>
<p><b>Update for 2009.03.24:</b></p><ul>
<li>ADD ADP and all military Wonders</li>
<li>ADD Environment calculations</li>
<li>ADD Nation Strength calculations</li>
<li>ADD Land Area calculations</li>
<li>ADD Military fields</li>
<li>ADD Military upkeep</li>
<li>ADD Soldiers affecting income notification</li>
<li>FIX Sugar and Wheat no longer affect Land Area</li>
<li>FIX Lead halves Nuke affect on Environment</li>
</ul><p>Note: There have been a ton of changes since the last time I really updated this on 2008.08.15. Despite all my troubleshooting, I'm sure there are still some latent bugs in there, so let me know if you find any.</p>
<p><a href="http://c2t.org/read.php?2,65,78#msg-78">2009.12.02</a>: FIX Mining Industry Consortium and National Environment Office calculations.
</p>
<p><a href="http://c2t.org/read.php?2,65,72#msg-72">2009.08.31</a>: Can now copy/paste another nation's status screen to fill in the calculator fields.
</p>

<li><a href="http://c2t.org/read.php?2,65,65#msg-65">2009.03.24</a> was the last time this calculator was up to date with the game. As of 2010.04.07, this only really means it's missing Mars' and Moon's wonders and resources.</li>
<li><a href="http://c2t.org/read.php?2,65,87#msg-87">2010.04.07</a>: It is now possible to copy/paste the source code of the nation summary page to fill out the form. This should work in any browser.</li>
<li>2011.09.21: updated some of the resources. <s>Currently a bug with Fur: it's multiplying its natural land growth bonus by 4 instead of 3.</s></li>
<li><a href="http://c2t.org/read.php?2,65,122#msg-122">2013.04.10</a>: Now calculates bulk infrastructure purchases by chunks of 100 (was 10), as per the 2013.03.10 game update.</li>
<ul class="lastlist"><li>2014/03/22:
<ul><li>FIX wonder counting bug involving Mars and Moon</li>
<li>FIX Nuclear Power Plant not applying its improvement and wonder upkeep reduction</li>
</ul></li>
<li>2014/02/24: Partial merge with an old Mars/Moon test build:
<ul><li>colony influences only happiness and bills in the moment while its population does nothing; "Reset Colony" is also disabled</li>
<li>an efficiency of 0 counts as not having the wonder, while the valid range for efficiency is from 50 to 100</li>
</ul></li>
<li>2014/02/16: Added 4 wonders new to the game.</li>
<li>2014/03/29: FIX reading happiness from the copy/paste of the source code of a nation page</li>
</ul>



<li>2014/06/  : ADD 5 improvements slated to be released in game, though the crime aspects are not yet implemented</li>
 
 
 
*/?>
<p>For information about the calculator, or for comments, questions, and bug reports, use this thread: <a href="/forum/read.php?2,65">Cybernations Calculator Information</a>.
</p>
</div>
<div class="article">
<h2>Last Updates</h2>
<ul class="lastlist">
<li>2014/06/09: FIX reading a few items from copy/paste that moved around on the nation page; crime system stuff might be soon</li>
<li>2014/06/06: ADD 7 improvements recently added to the game</li>
<li>2014/04/21: FIX Nuclear Power Plant interactions with nuclear position</li>
</ul>
</div>
<?php

if ($_POST['yesnoyesShowMods'] || $_POST[$zomgtwice[0].'Government']) { // todo: remove the yesnoyesShowMods check? Government should always exist in POST. verify
?>
<div class="article">
<h2>Calculations and Modifiers</h2>
<?php
}


// display calculations
if ($_POST) {
	$calc1 = array();
	$calc2 = array();

	echo '<table class="calcdisplay">';
	write_table_header('Calculations','Current','Future','Difference');

	// environment
	$calc1['Environment'] = ${$qty1}['Environment'];
	$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']),$basecalc['Global Radiation'],${$mod2}['Global Radiation']);

	/* start of colony stuff
	// colony population -- this is kind of gross
	if (${$qty2}['Colony Efficiency'] && ${$qty}['yesnoyesHardReset']) {
		$calc1['Colony Population'] = $basecalc['Colony Population'];
		// working citizens
		$calc1['Working Citizens'] = ${$qty1}['Working Citizens'];
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty2}['Mars']);
		}
		$calc2['Colony Population'] = $calc2['Working Citizens'] * (0.06 - 0.01 * ${$qty2}['Mars']) * ${$qty2}['Colony Efficiency']/100;
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod1}['Soldier %']))
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
		else
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']),$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		write_table_tr('Colony Population',$calc1['Colony Population'],$calc2['Colony Population']);
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	}
	elseif ($basecalc['Colony Population']) {
		$calc1['Colony Population'] = $basecalc['Colony Population'];
		$calc2['Colony Population'] = $basecalc['Colony Population'] / ${$qty1}['Colony Efficiency'] * ${$qty2}['Colony Efficiency'];
		write_table_tr('Colony Population',$calc1['Colony Population'],$calc2['Colony Population']);
		// working citizens
		$calc1['Working Citizens'] = ${$qty1}['Working Citizens'];
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		}
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	}
	else { // else of colony stuff */
		// working citizens -- original part of code without colony
		$calc1['Working Citizens'] = ${$qty1}['Working Citizens'];
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population']);
		}
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	//} // end of colony stuff

	// happiness
	$calc1['Happiness'] = ($basecalc['Happiness'] + ${$mod1}['Happiness'] + soldier_happiness(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %']))*(1-$calc1['Environment']/100);
	$calc2['Happiness'] = ($basecalc['Happiness'] + ${$mod2}['Happiness'] + soldier_happiness(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %']))*(1-$calc2['Environment']/100);

	// citizen income

	// tax rate and citizen income
	foreach ($zomgtwice as $key => $zomg) {
		$qty = $zomg.$zomg_qty;
		$mod = $zomg.$zomg_mod;
		$calc = 'calc'.($key + 1);

		if (${$qty}['Social Security System'])
			$temp1 = 30;
		else
			$temp1 = 28;
		$temp = array(
			$temp1 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 7)),
			25 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 5)),
			23 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 3)),
			20 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 1))
		);
		if (${$qty1}['Tax Rate'] != '26% - 30%' && ${$qty1}['Tax Rate'] != '24% - 25%') {
			$temp[$temp1]['Happiness'] += ${$qty}['Intelligence Agency'];
			$temp[25]['Happiness'] += ${$qty}['Intelligence Agency'];
		}

		foreach ($temp as $rate => $meh) {
			$temp[$rate]['Income'] = ${$mod}['Income %'] * ($basecalc['Citizen Income'] + ${$mod}['Income $'] + 2 * (${$calc}['Happiness']/(1-${$calc}['Environment']/100) + $temp[$rate]['Happiness']) * (1-${$calc}['Environment']/100) - 2*$basecalc['Happiness']);
			//$temp[$rate]['Income'] = ${$mod}['Income %'] * ($basecalc['Citizen Income'] + ${$mod}['Income $'] + 2 * ($basecalc['Happiness'] + ${$mod}['Happiness'] + $temp[$rate]['Happiness']) * (1-${$qty}['Environment']/100));
			if ($temp[$rate]['Income'] < 10)
				$temp[$rate]['Income'] = 10;
			$temp[$rate]['Taxed'] = $temp[$rate]['Income'] * $rate / 100;
		}

		if ($temp[20]['Taxed'] > $temp[23]['Taxed'] && $temp[20]['Taxed'] > $temp[25]['Taxed'] && $temp[20]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[20]['Income'];
			${$calc}['Tax Rate'] = 20;
		}
		elseif ($temp[23]['Taxed'] > $temp[25]['Taxed'] && $temp[23]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[23]['Income'];
			${$calc}['Tax Rate'] = 23;
		}
		elseif ($temp[25]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[25]['Income'];
			${$calc}['Tax Rate'] = 25;
		}
		else {
			${$calc}['Citizen Income'] = $temp[$temp1]['Income'];
			${$calc}['Tax Rate'] = $temp1;
		}
	}
	write_table_tr('Citizen Income',$calc1['Citizen Income'],$calc2['Citizen Income']);
	write_table_tr_raw('Optimal Tax Rate',$calc1['Tax Rate'].'%',$calc2['Tax Rate'].'%','-');

	// total income
	$calc1['Total Income'] = $calc1['Working Citizens'] * $calc1['Citizen Income'] * $calc1['Tax Rate'] / 100;
	$calc2['Total Income'] = $calc2['Working Citizens'] * $calc2['Citizen Income'] * $calc2['Tax Rate'] / 100;
	write_table_tr('Total Income',$calc1['Total Income'],$calc2['Total Income']);

	// total infra upkeep
	$calc1['Total Infra Upkeep'] = ${$qty1}['Infrastructure'] * ${$mod1}['Infra Upkeep'] * $basecalc['Infra Upkeep'] * tech_infra_upkeep_reduction(${$qty1}['Technology'],${$qty1}['Nation Strength']);
	$calc2['Total Infra Upkeep'] = ${$qty1}['Infrastructure'] * ${$mod2}['Infra Upkeep'] * $basecalc['Infra Upkeep'] * tech_infra_upkeep_reduction(${$qty2}['Technology'],${$qty2}['Nation Strength']);
	write_table_tr('Total Infra Upkeep',$calc1['Total Infra Upkeep'],$calc2['Total Infra Upkeep']);

	// total Improvement/Wonder Upkeep
	$calc1['I/W Upkeep'] = (improvement_upkeep(${$qty1}['# Improvements']) + ${$qty1}['# Wonders'] * 5000) * ${$mod1}['Improv/Wonder Upkeep'];
	$calc2['I/W Upkeep'] = (improvement_upkeep(${$qty2}['# Improvements']) + ${$qty2}['# Wonders'] * 5000) * ${$mod2}['Improv/Wonder Upkeep'];
	write_table_tr('Improv/Wonder Upkeep',$calc1['I/W Upkeep'],$calc2['I/W Upkeep']);

	// military upkeep -- oh god
	$calc1['Military Upkeep'] = (
		${$qty1}['Soldiers'] * ($cn_military['Soldiers']['upkeep'] + ${$mod1}['Soldier Upkeep $']) * ${$mod1}['Soldier Upkeep'] +
		${$qty1}['Tanks'] * $cn_military['Tanks']['upkeep'] * ${$mod1}['Tank Upkeep'] +
		${$qty1}['Aircraft'] * $cn_military['Aircraft']['upkeep'] * ${$mod1}['Aircraft Upkeep'] +
		${$qty1}['Cruise Missiles'] * $cn_military['Cruise Missiles']['upkeep'] * ${$mod1}['Missile Upkeep'] +
		${$qty1}['Nukes'] * nuke_upkeep(${$qty1}['Nukes'],${$qty1}['Uranium']) * ${$mod1}['Missile Upkeep']
		) * ${$mod1}['Military Upkeep'];
	$calc2['Military Upkeep'] = (
		${$qty2}['Soldiers'] * ($cn_military['Soldiers']['upkeep'] + ${$mod2}['Soldier Upkeep $']) * ${$mod2}['Soldier Upkeep'] +
		${$qty2}['Tanks'] * $cn_military['Tanks']['upkeep'] * ${$mod2}['Tank Upkeep'] +
		${$qty2}['Aircraft'] * $cn_military['Aircraft']['upkeep'] * ${$mod2}['Aircraft Upkeep'] +
		${$qty2}['Cruise Missiles'] * $cn_military['Cruise Missiles']['upkeep'] * ${$mod2}['Missile Upkeep'] +
		${$qty2}['Nukes'] * nuke_upkeep(${$qty2}['Nukes'],${$qty2}['Uranium']) * ${$mod2}['Missile Upkeep']
		) * ${$mod2}['Military Upkeep'];
	write_table_tr('Military Upkeep',$calc1['Military Upkeep'],$calc2['Military Upkeep']);

	// net income
	$calc1['Net Income'] = $calc1['Total Income'] - $calc1['Total Infra Upkeep'] - $calc1['I/W Upkeep'] - $calc1['Military Upkeep'];
	$calc2['Net Income'] = $calc2['Total Income'] - $calc2['Total Infra Upkeep'] - $calc2['I/W Upkeep'] - $calc2['Military Upkeep'];
	write_table_tr('Net Income',$calc1['Net Income'],$calc2['Net Income']);

	// hr
	write_table_tr_null();

	// infra cost
	$calc1['Infra Cost'] = $basecalc['Infra Cost'] * ${$mod1}['Infra Cost'];
	$calc2['Infra Cost'] = $basecalc['Infra Cost'] * ${$mod2}['Infra Cost'];
	write_table_tr('Infra Cost Per 1',$calc1['Infra Cost'],$calc2['Infra Cost']);

	// infra upkeep
	$calc1['Infra Upkeep'] = $basecalc['Infra Upkeep'] * ${$mod1}['Infra Upkeep'] * tech_infra_upkeep_reduction(${$qty1}['Technology'],${$qty1}['Nation Strength']);
	$calc2['Infra Upkeep'] = $basecalc['Infra Upkeep'] * ${$mod2}['Infra Upkeep'] * tech_infra_upkeep_reduction(${$qty2}['Technology'],${$qty2}['Nation Strength']);
	write_table_tr('Infra Upkeep Per 1',$calc1['Infra Upkeep'],$calc2['Infra Upkeep']);

	// hr
	write_table_tr_null();

	// nation strength
	write_table_tr('Nation Strength',${$qty1}['Nation Strength'],${$qty2}['Nation Strength']);

	// land for fun
	write_table_tr('Land Area',$basecalc['Land Area 1'],$basecalc['Land Area 2']);

	// happiness for fun
	write_table_tr('Happiness',$calc1['Happiness'],$calc2['Happiness']);

	// display environment
	write_table_tr('Environment',$calc1['Environment'],$calc2['Environment']);

	// soldier warnings
	soldier_warnings(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %'],${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %']);

	// end calculations
	echo '</table>';
}

// display bulk purchase calculations
if (cleanse_number($_POST['yesnoyesPurchase'])) {
	echo '<br /><table class="calcdisplay">';
	write_table_header('Bulk Infra Results','Current','Future','Difference');

	// bulk infra cost
	write_table_tr(${$buy1}['need'].' Infra Costs',${$buy1}['cost'],${$buy2}['cost']);

	// hr
	write_table_tr_null();

	//mm //doublecheck changed to ${$mod1}['Environment'] correct?
	$calc1['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 1']),$basecalc['Global Radiation'],${$mod1}['Global Radiation']);
	$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']),$basecalc['Global Radiation'],${$mod2}['Global Radiation']);

	/* start of colony stuff
	// colony population -- this is kind of gross
	if (${$qty2}['Colony Efficiency'] && ${$qty}['yesnoyesHardReset']) { //todo:all of this; throw calc1 under bus as well
		$calc1['Colony Population'] = $basecalc['Colony Population'];
		// working citizens
		$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty1}['Mars']);
		if (soldier_environment(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %'])) {
			$calc1['Environment'] = environment_calc($basecalc['Environment'],${$mod1}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 1']) + 1,$basecalc['Global Radiation'],${$mod1}['Global Radiation']);
			$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty1}['Mars']);
		} //calc1 is done! stop thinking about it!
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$basecalc['Colony Population'],${$qty1}['Colony Efficiency'],${$qty2}['Mars']);
		}
		$calc2['Colony Population'] = $calc2['Working Citizens'] * (0.06 - 0.01 * ${$qty2}['Mars']) * ${$qty2}['Colony Efficiency']/100;
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod1}['Soldier %']))
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
		else
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']),$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		write_table_tr('Colony Population',$calc1['Colony Population'],$calc2['Colony Population']);
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	}
	elseif ($basecalc['Colony Population']) {
		$calc1['Colony Population'] = $basecalc['Colony Population'];
		$calc2['Colony Population'] = $basecalc['Colony Population'] / ${$qty1}['Colony Efficiency'] * ${$qty2}['Colony Efficiency'];
		write_table_tr('Colony Population',$calc1['Colony Population'],$calc2['Colony Population']);
		// working citizens
		$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population'],$calc1['Colony Population'],${$qty1}['Colony Efficiency'],${$qty1}['Mars']);
		if (soldier_environment(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %'])) {
			$calc1['Environment'] = environment_calc($basecalc['Environment'],${$mod1}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 1']) + 1,$basecalc['Global Radiation'],${$mod1}['Global Radiation']);
			$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population'],$calc1['Colony Population'],${$qty1}['Colony Efficiency'],${$qty1}['Mars']);
		}
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population'],$calc2['Colony Population'],${$qty2}['Colony Efficiency'],${$qty2}['Mars']);
		}
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	}
	else { // else of colony stuff */
		// working citizens -- original part of code without colony
		$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population']);
		if (soldier_environment(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %'])) {
			$calc1['Environment'] = environment_calc($basecalc['Environment'],${$mod1}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 1']) + 1,$basecalc['Global Radiation'],${$mod1}['Global Radiation']);
			$calc1['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 1'] + ${$buy1}['pop+'],${$qty1}['Soldiers'],$calc1['Environment'],${$mod1}['Population']);
		}
		$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population']);
		if (soldier_environment(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %'])) {
			$calc2['Environment'] = environment_calc($basecalc['Environment'],${$mod2}['Environment'] + infra_land_ratio(${$qty1}['Infrastructure'] + ${$buy1}['need'],$basecalc['Land Area 2']) + 1,$basecalc['Global Radiation'],${$mod2}['Global Radiation']);
			$calc2['Working Citizens'] = unmodcit_to_citizens($basecalc['Working Citizens 2'] + ${$buy1}['pop+'],${$qty2}['Soldiers'],$calc2['Environment'],${$mod2}['Population']);
		}
		write_table_tr('Working Citizens',$calc1['Working Citizens'],$calc2['Working Citizens']);
	//} // end of colony stuff

	// happiness
	$calc1['Happiness'] = ($basecalc['Happiness'] + ${$mod1}['Happiness'] + soldier_happiness(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %']))*(1-$calc1['Environment']/100);
	$calc2['Happiness'] = ($basecalc['Happiness'] + ${$mod2}['Happiness'] + soldier_happiness(${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %']))*(1-$calc2['Environment']/100);


	// tax rate and citizen income
	foreach ($zomgtwice as $key => $zomg) {
		$qty = $zomg.$zomg_qty;
		$mod = $zomg.$zomg_mod;
		$calc = 'calc'.($key + 1);

		if (${$qty}['Social Security System'])
			$temp1 = 30;
		else
			$temp1 = 28;
		$temp = array(
			$temp1 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 7)),
			25 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 5)),
			23 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 3)),
			20 => array('Happiness' => ($cn_taxrates[${$qty1}['Tax Rate']] - 1))
		);
		if (${$qty1}['Tax Rate'] != '26% - 30%' && ${$qty1}['Tax Rate'] != '24% - 25%') {
			$temp[$temp1]['Happiness'] += ${$qty}['Intelligence Agency'];
			$temp[25]['Happiness'] += ${$qty}['Intelligence Agency'];
		}

		foreach ($temp as $rate => $meh) {
			$temp[$rate]['Income'] = ${$mod}['Income %'] * ($basecalc['Citizen Income'] + ${$mod}['Income $'] + 2 * (${$calc}['Happiness']/(1-${$calc}['Environment']/100) + $temp[$rate]['Happiness']) * (1-${$calc}['Environment']/100) - 2*$basecalc['Happiness']);
			//$temp[$rate]['Income'] = ${$mod}['Income %'] * ($basecalc['Citizen Income'] + ${$mod}['Income $'] + 2 * ($basecalc['Happiness'] + ${$mod}['Happiness'] + $temp[$rate]['Happiness']) * (1-${$qty}['Environment']/100));
			if ($temp[$rate]['Income'] < 10)
				$temp[$rate]['Income'] = 10;
			$temp[$rate]['Taxed'] = $temp[$rate]['Income'] * $rate / 100;
		}

		if ($temp[20]['Taxed'] > $temp[23]['Taxed'] && $temp[20]['Taxed'] > $temp[25]['Taxed'] && $temp[20]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[20]['Income'];
			${$calc}['Tax Rate'] = 20;
		}
		elseif ($temp[23]['Taxed'] > $temp[25]['Taxed'] && $temp[23]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[23]['Income'];
			${$calc}['Tax Rate'] = 23;
		}
		elseif ($temp[25]['Taxed'] > $temp[$temp1]['Taxed']) {
			${$calc}['Citizen Income'] = $temp[25]['Income'];
			${$calc}['Tax Rate'] = 25;
		}
		else {
			${$calc}['Citizen Income'] = $temp[$temp1]['Income'];
			${$calc}['Tax Rate'] = $temp1;
		}
	}
	write_table_tr('Citizen Income',$calc1['Citizen Income'],$calc2['Citizen Income']);
	write_table_tr_raw('Optimal Tax Rate',$calc1['Tax Rate'].'%',$calc2['Tax Rate'].'%','-');

	// total income
	$calc1['Total Income'] = $calc1['Working Citizens'] * $calc1['Citizen Income'] * $calc1['Tax Rate'] / 100;
	$calc2['Total Income'] = $calc2['Working Citizens'] * $calc2['Citizen Income'] * $calc2['Tax Rate'] / 100;
	write_table_tr('Total Income',$calc1['Total Income'],$calc2['Total Income']);

	// total infra upkeep
	$calc1['Total Infra Upkeep'] = infra_upkeep(${$qty1}['Infrastructure'] + ${$buy1}['need']);
	$calc2['Total Infra Upkeep'] = $calc1['Total Infra Upkeep'] * ${$mod2}['Infra Upkeep'] * (${$qty1}['Infrastructure'] + ${$buy2}['need']) * tech_infra_upkeep_reduction(${$qty1}['Technology'],${$qty2}['Nation Strength'] + ${$buy2}['need'] * 3);
	$calc1['Total Infra Upkeep'] *= ${$mod1}['Infra Upkeep'] * (${$qty1}['Infrastructure'] + ${$buy1}['need']) * tech_infra_upkeep_reduction(${$qty1}['Technology'],${$qty1}['Nation Strength'] + ${$buy1}['need'] * 3);
	write_table_tr('Total Infra Upkeep',$calc1['Total Infra Upkeep'],$calc2['Total Infra Upkeep']);

	// total Improvement/Wonder Upkeep
	write_table_tr('Improv/Wonder Upkeep',$calc1['I/W Upkeep'],$calc2['I/W Upkeep']);

	// military upkeep
	write_table_tr('Military Upkeep',$calc1['Military Upkeep'],$calc2['Military Upkeep']);

	// net income
	$calc1['Net Income'] = $calc1['Total Income'] - $calc1['Total Infra Upkeep'] - $calc1['I/W Upkeep'] - $calc1['Military Upkeep'];
	$calc2['Net Income'] = $calc2['Total Income'] - $calc2['Total Infra Upkeep'] - $calc2['I/W Upkeep'] - $calc2['Military Upkeep'];
	write_table_tr('Net Income',$calc1['Net Income'],$calc2['Net Income']);

	// hr
	write_table_tr_null();

	// infra cost
	$calc1['Infra Cost'] = infra_cost(${$qty1}['Infrastructure'] + ${$buy1}['need']) * ${$mod1}['Infra Cost'];
	$calc2['Infra Cost'] = infra_cost(${$qty1}['Infrastructure'] + ${$buy2}['need']) * ${$mod2}['Infra Cost'];
	write_table_tr('Infra Cost Per 1 ',$calc1['Infra Cost'],$calc2['Infra Cost']);

	// infra upkeep
	$calc1['Infra Upkeep'] = infra_upkeep(${$qty1}['Infrastructure'] + ${$buy1}['need']) * ${$mod1}['Infra Upkeep'];
	$calc2['Infra Upkeep'] = infra_upkeep(${$qty1}['Infrastructure'] + ${$buy2}['need']) * ${$mod2}['Infra Upkeep'];
	write_table_tr('Infra Upkeep Per 1',$calc1['Infra Upkeep'],$calc2['Infra Upkeep']);

	// hr
	write_table_tr_null();

	$calc1['Nation Strength'] = ${$qty1}['Nation Strength'] + ${$buy1}['need'] * 3;
	$calc2['Nation Strength'] = ${$qty2}['Nation Strength'] + ${$buy2}['need'] * 3;

	// nation strength
	write_table_tr('Nation Strength',$calc1['Nation Strength'],$calc2['Nation Strength']);

	// land for fun
	write_table_tr('Land Area',$basecalc['Land Area 1'],$basecalc['Land Area 2']);

	// happiness for fun

	if ($_SERVER['PHP_SELF'] == '/tesuto/cncalc.php')
		write_table_tr('Happiness',$calc1['Happiness'],$calc2['Happiness']);

	// display environment
	write_table_tr('Environment',$calc1['Environment'],$calc2['Environment']);

	// soldier warnings
	soldier_warnings(${$qty1}['Soldiers'],$calc1['Working Citizens'],${$mod1}['Soldier %'],${$qty2}['Soldiers'],$calc2['Working Citizens'],${$mod2}['Soldier %']);

	// end calculations
	echo '</table>';
}

// br
if ($_POST['yesnoyesShowMods'] && $_POST[$zomgtwice[0].'Government']) { echo '<br />'; }


// display modifications
if ($_POST['yesnoyesShowMods']) {
	echo '<table class="calcdisplay">';
	write_table_header('Relative Modifiers','Current','Future','Difference');

	// show all modifications
	foreach (${$mod1} as $key => $value) {
		if ($value == -100)
			write_table_tr_null();
		else
			write_table_tr_4($key,${$mod1}[$key],${$mod2}[$key]);
	}

	// start bonus resource display
	echo '<tr><td class="left">'.nbsp('Bonus Resources ');
	foreach ($zomgtwice as $zomg) {
		echo ' </td><td class="left"> ';
		$temp = 0;
		foreach ($cn_resources_bonus as $key => $value) {
			if (${$zomg.$zomg_qty}[$key]) {
				echo nbsp($key).'<br />';
				$temp += 1;
			}
		}
		if (!$temp) { echo 'N/A'; }
	}
	echo '</td><td class="left">N/A</td></tr>';
	// end bonus resource display

	echo '</table>';
}

// hr
if ($_POST['yesnoyesShowMods'] || $_POST[$zomgtwice[0].'Government']) { echo "\n</div>\n"; }


// write the input form
?>
<div class="article">
<h2>Enter Nation Information</h2>
<?php echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">'; ?>
<p>To automatically fill out most of the fields below, copy/paste either the source code or text from your nation summary page into the box directly below, then press the Calculate on the bottom. This will override all input fields except for bulk infra purchase. However, the following will still be missing: Nuclear Position, 90% Literacy, Aircraft Level, Mars/Moon efficiency and expiry, and all Events. It is recommended to use the source code of the nation summary page instead of what the web browser decides to display, otherwise, more input fields may be missing if not using Firefox.
<?php

echo '<br /><textarea name="copypaste" rows="3" cols="40"></textarea></p>';

echo '<table class="calcinput"><tr><td><table><tr><td><b>Current Setup</b></td><td><b>Future Design</b></td></tr><tr>';

foreach ($zomgtwice as $zomg) {
	$qty = $zomg.$zomg_qty;
	$mod = $zomg.$zomg_mod;

	echo '<td><table><tr><td><table>';

/*
	$government;
	$technology;
	$infrastructure;
	$taxRate;
	$landPurchased;
	$landModifiers;
	$landGrowth;
	$resources;
	$improvements;
	$wonders;
	$environment;
	$globalRadiationLevel;
	$nationStrength;
	$defconLevel;
	$threatLevel;
	$soldiers;
	$tanks;
	$aircraft;
	$aircraftLevel;
	$cruiseMissiles;
	$navyVessels;
	$nuclearWeapons;
	$populationHappiness;
	$workingCitizens;
	$avgGrossIncomePerIndividualPerDay;
	*/

	// government
	echo '<tr><td><label for="'.$zomg.'Government'.'">'.nbsp('Government: ').'</label></td><td>';
	write_option_array($zomg,'Government',$cn_governments,${$qty}['Government']);
	echo '</td></tr>';

	// preferred government
	echo '<tr><td><label for="'.$zomg.rmsp('Preferred Government').'">'.nbsp('Preferred Gov?: ').'</label></td><td>';
	write_checkbox($zomg,'Preferred Government',${$qty}['Preferred Government']);
	echo '</td></tr>';

	// technology
	echo '<tr><td><label for="'.$zomg.rmsp('Technology').'">'.nbsp('Technology: ').'</label></td><td>';
	write_input($zomg,'Technology',${$qty}['Technology'],0);
	echo '</td></tr>';

	// infrastructure
	if ($zomg != $zomgtwice[1]) {		
		echo '<tr><td><label for="'.$zomg.rmsp('Infrastructure').'">'.nbsp('Infrastructure: ').'</label></td><td>';
		write_input($zomg,'Infrastructure',${$qty}['Infrastructure'],0);
		echo '</td></tr>';
	}

	// tax rate
	if ($zomgtwice[0] == $zomg) {
		echo '<tr><td><label for="'.$zomg.rmsp('Tax Rate').'">'.nbsp('Current Tax Rate: ').'</label></td><td>';
		write_option_array($zomg,'Tax Rate',$cn_taxrates,${$qty}['Tax Rate']);
		echo '</td></tr>';
	}	

	// land
		echo '<tr><td><label for="'.$zomg.rmsp('Land Purchased').'">'.nbsp('Land Purchased: ').'</label></td><td>';
		write_input($zomg,'Land Purchased',${$qty}['Land Purchased'],0);
		if ($zomgtwice[0] == $zomg) {
			echo '</td></tr>';
			echo '<tr><td><label for="'.$zomg.rmsp('Land Growth').'">'.nbsp('Land Growth: ').'</label></td><td>';
		write_input($zomg,'Land Growth',${$qty}['Land Growth'],0);
		}	
		echo '</td></tr>';


	// environment
	if ($zomgtwice[0] == $zomg) {
		echo '<tr><td><label for="'.$zomg.rmsp('Environment').'">'.nbsp('Environment: ').'</label></td><td>';
		write_input($zomg,'Environment',${$qty}['Environment'],0);
		echo '</td></tr>';

	// GRL
		echo '<tr><td><label for="'.$zomg.rmsp('Global Radiation').'">'.nbsp('Global Radiation: ').'</label></td><td>';
		write_input($zomg,'Global Radiation',${$qty}['Global Radiation'],0);
		echo '</td></tr>';

	// Nation Strength
		echo '<tr><td><label for="'.$zomg.rmsp('Nation Strength').'">'.nbsp('Nation Strength: ').'</label></td><td>';
		write_input($zomg,'Nation Strength',${$qty}['Nation Strength'],0);
		echo '</td></tr>';
	}

	// 90% literacy
	echo '<tr><td><label for="'.$zomg.rmsp('90% Literacy').'">'.nbsp('90% Literacy: ').'</label></td><td>';
	write_checkbox($zomg,'90% Literacy',${$qty}['90% Literacy']);
	echo '</td></tr>';

	// nuclear position
	echo '<tr><td><label for="'.$zomg.rmsp('Nuclear Position').'">'.nbsp('Nuclear Position: ').'</label></td><td>';
	write_option_array($zomg,'Nuclear Position',$cn_nuclear,${$qty}['Nuclear Position']);
	echo '</td></tr>';

	echo '</table></td><td><table>';

	// military
	foreach ($cn_military as $key => $value) {
		echo '<tr><td><label for="'.$zomg.rmsp($key).'">'.nbsp($key.': ').'</label></td><td>';
		write_input($zomg,$key,${$qty}[$key],0);
		echo '</td></tr>';
	}

	// Happiness
	if ($zomgtwice[0] == $zomg) {
		echo '<tr><td><label for="'.$zomg.rmsp('Happiness').'">'.nbsp('Happiness: ').'</label></td><td>';
		write_input($zomg,'Happiness',${$qty}['Happiness'],0);
		echo '</td></tr>';

	// Working citizens
		echo '<tr><td><label for="'.$zomg.rmsp('Working Citizens').'">'.nbsp('Working Citizens: ').'</label></td><td>';
		write_input($zomg,'Working Citizens',${$qty}['Working Citizens'],0);
		echo '</td></tr>';

	// Citizen income
		echo '<tr><td><label for="'.$zomg.rmsp('Citizen Income').'">'.nbsp('Citizen Income: ').'</label></td><td>';
		write_input($zomg,'Citizen Income',${$qty}['Citizen Income'],0);
		echo '</td></tr>';
	}

	// events
	foreach ($cn_events as $key => $value) {
		echo '<tr><td><label for="'.$zomg.rmsp($value).'">'.nbsp($value.': ').'</label></td><td>';
		write_input($zomg,$value,${$qty}[$value],0);
		echo '</td></tr>';
	}

/*	$soldiers;
	$tanks;
	$aircraft;
	$aircraftLevel;
	$cruiseMissiles;
	$navyVessels;
	$nuclearWeapons;
	$populationHappiness;
	$workingCitizens;
	$avgGrossIncomePerIndividualPerDay;
*/

	echo '</table></td></tr></table></td>';
}

echo '</tr><tr>';

foreach ($zomgtwice as $zomg) {
	$qty = $zomg.$zomg_qty;
	$mod = $zomg.$zomg_mod;

	echo '<td><table><tr><td><b>Resources</b></td><td><b>Improvements</b></td><td><b>Wonders</b></td></tr><tr><td><pre>';

	//resources
	foreach ($cn_resources as $key => $value) {
		write_checkbox($zomg,$key,${$qty}[$key]);
		echo '<label for="'.$zomg.rmsp($key).'">'.nbsp(" $key ").'</label><br />';
	}
	echo '</pre></td><td><pre>';

	// improvements
	foreach ($cn_improvements as $key => $value) {
		write_option_number($zomg,$key,$value['max'],${$qty}[$key]);
		echo '<label for="'.$zomg.rmsp($key).'">'.nbsp(" $key ").'</label><br />';
	}
	echo '</pre></td><td><pre>';

	// events
/*	foreach ($cn_events as $key => $value) {
		echo '<label for="'.$zomg.rmsp($value).'">'.nbsp($value).'</label><br />';
		write_input($zomg,$value,${$qty}[$value],0);
		echo '<br />';
	}*/
//	echo '<br />';

	// wonders
	foreach ($cn_wonders as $key => $value) {
		write_checkbox($zomg,$key,${$qty}[$key]);
		echo '<label for="'.$zomg.rmsp($key).'">'.nbsp(" $key ").'</label><br />';
	}
	echo '</pre></td></tr></table></td>';
}

// Mars/Moon form
echo '</tr><tr>';
foreach ($zomgtwice as $zomg) {
	$qty = $zomg.$zomg_qty;
	$mod = $zomg.$zomg_mod;

	echo '<td><table><tr><td>';
	echo '<select name="'.$zomg.'Mars" id="'.$zomg.'Mars">';
	echo '<option value="1"';
	if (${$qty}['Mars'])
		echo ' selected="selected"';
	echo '>Mars</option>';
	echo '<option value="0"';
	if (!${$qty}['Mars'])
		echo ' selected="selected"';
	echo '>Moon</option>';
	echo '</select>';
	echo '</td><td>Efficiency</td><td>Expires</td><td></td></tr>';
	echo '<tr><td>Base:&nbsp;</td><td>';
	write_input($zomg,'Base Efficiency',${$qty}['Base Efficiency']);
	echo '</td><td>';
	write_input($zomg,'Base Expires',${$qty}['Base Expires']);
	echo '</td><td>';
	if ($zomgtwice[0] == $zomg)
		echo 'Population:';
	echo '</td></tr><tr><td>Colony:&nbsp;</td><td>';
	write_input($zomg,'Colony Efficiency',${$qty}['Colony Efficiency']);
	echo '</td><td>';
	write_input($zomg,'Colony Expires',${$qty}['Colony Expires']);
	echo '</td><td>';
	if ($zomgtwice[0] == $zomg)
		write_input($zomg,'Colony Population',${$qty}['Colony Population']);
	else {
		write_checkbox('yesnoyes','HardReset',${$qty}['yesnoyesHardReset'],1); //disabled checkbox for colony reset
		echo '&nbsp;<label for="yesnoyesHardReset">Reset&nbsp;Colony</label>';
	}
	echo '</td></tr><tr><td>Mine:&nbsp;</td><td>';
	write_input($zomg,'Mine Efficiency',${$qty}['Mine Efficiency']);
	echo '</td><td>';
	write_input($zomg,'Mine Expires',${$qty}['Mine Expires']);
	echo '</td><td>';
	write_option_array($zomg,'Mine Resource',$cn_mm_resource,${$qty}['Mine Resource'],1);
	echo '</td></tr></table></td>';
}

// end tables/forms and cleanup
echo '</tr></table><center><label for="yesnoyesPurchase">Bulk Infra Purchase:</label> ';
write_input('yesnoyes','Purchase',cleanse_number($_POST['yesnoyesPurchase']),0);
echo '<br /><input type="checkbox" name="yesnoyesShowMods" id="yesnoyesShowMods"';
if ($_POST['yesnoyesShowMods'])
	echo ' checked="checked"';
echo ' /> <label for="yesnoyesShowMods">Show Mods</label> <input type="submit" value="Calculate" /></center></td></tr></table></form>'."\n".'</div>';


// footer
require_once('footer.php');


// cleanse input
function cleanse_number($cleanse,$max=10000000) { // arbitrary 10M max/min for all input
	$cleanse = str_replace('%','',str_replace('$','',str_replace(',','',$cleanse)));
	if (is_numeric($cleanse) && abs($cleanse) <= $max)
		return $cleanse;
	else
		return 0;
}
function cleanse_checkbox($cleanse) {
	if ($cleanse)
		return 1;
	else
		return 0;
}
function cleanse_option_array($array,$cleanse,$asValue=0) {
	if ($asValue)
		foreach ($array as $key => $value) {
			$temp1 = rmsp($value);
			if ($temp1 == $cleanse)
				return $value;
		}
	else
		foreach ($array as $key => $value) {
			$temp1 = rmsp($key);
			if ($temp1 == $cleanse)
				return $key;
		}
	return 0;
}

// write input
function write_checkbox($now,$field,$value,$disabled=0) {
	echo '<input type="checkbox" name="'.$now.rmsp($field).'" id="'.$now.rmsp($field).'" ';
	if ($value)
		echo 'checked="checked" ';
	if ($disabled)
		echo 'disabled="disabled" ';
	echo '/>';
}
function write_input($now,$field,$value,$disabled=0) {
	echo '<input name="'.$now.rmsp($field).'" id="'.$now.rmsp($field).'" type="text" size="10" maxlength="10" value="'.$value.'" ';
	if ($disabled)
		echo 'disabled="disabled" ';
	echo '/>';
}
function write_option_number($now,$field,$max,$default) {
	echo '<select name="'.$now.rmsp($field).'" id="'.$now.rmsp($field).'">';
	for ($i = 0;$i <= $max;$i += 1) {
		echo "<option value=\"$i\"";
		if ($default == $i)
			echo ' selected="selected"';
		echo ">$i</option>";
	}
	echo '</select>';
}
function write_option_array($now,$field,$array,$default,$asValue=0) {
	echo "<select name=\"".$now.rmsp($field)."\" id=\"".$now.rmsp($field).'">';
	if ($asValue)
		foreach ($array as $key => $value) {
			echo '<option value="'.rmsp($value).'"';
			if ($default == $value)
				echo ' selected="selected"';
			echo ">$value</option>";
		}
	else
		foreach ($array as $key => $value) {
			echo '<option value="'.rmsp($key).'"';
			if ($default == $key)
				echo ' selected="selected"';
			echo ">$key</option>";
		}
	echo '</select>';
}

// lazy calc table rows
function write_table_header($field1,$field2,$field3,$field4) {
	echo '<tr>';
	echo '<td class="leftleft"><b>'.nbsp($field1).'</b></td>';
	echo '<td class="left"><b>'.nbsp($field2).'</b></td>';
	echo '<td class="left"><b>'.nbsp($field3).'</b></td>';
	echo '<td class="left"><b>'.nbsp($field4).'</b></td>';
	echo '</tr>';
}
function write_table_tr_words($field1,$field2,$field3,$field4) {
	echo '<tr>';
	echo '<td class="left">'.nbsp($field1).'</td>';
	echo '<td class="left">'.nbsp($field2).'</td>';
	echo '<td class="left">'.nbsp($field3).'</td>';
	echo '<td class="left">'.nbsp($field4).'</td>';
	echo '</tr>';
}
function write_table_tr_raw($field1,$field2,$field3,$field4) {
	echo '<tr>';
	echo '<td class="leftleft">'.nbsp($field1).'</td>';
	echo '<td>'.nbsp($field2).'</td>';
	echo '<td>'.nbsp($field3).'</td>';
	echo '<td>'.nbsp($field4).'</td>';
	echo '</tr>';
}

function write_table_tr($field,$value1,$value2) {
	$print1 = number_format($value1,2);
	$print2 = number_format($value2,2);
	$print3 = number_format($value2 - $value1,2);
	echo '<tr><td class="leftleft">'.nbsp($field)."</td><td>$print1</td><td>$print2</td><td>$print3</td></tr>";
}
function write_table_tr_4($field,$value1,$value2) {
	$print1 = number_format($value1,4);
	$print2 = number_format($value2,4);
	$print3 = number_format($value2 - $value1,4);
	echo '<tr><td class="leftleft">'.nbsp($field)."</td><td>$print1</td><td>$print2</td><td>$print3</td></tr>";
}
function write_table_tr_null() {
	write_table_tr_words('--------','--------','--------','--------');
}

// non-breaking space
function nbsp($string) {
	return str_replace(' ','&nbsp;',$string);
}

// remove space
function rmsp($string) {
	return str_replace(' ','',$string);
}

function soldier_happiness($soldiers,$citizens,$efficiency) {
	if ($soldiers >= ($citizens * 0.8))
		return -6;
	elseif (($soldiers * $efficiency) >= ($citizens * 0.6))
		return -1;
	elseif (($soldiers * $efficiency) >= ($citizens * 0.2))
		return 0;
	elseif (($soldiers * $efficiency) >= ($citizens * 0.1))
		return -5;
	elseif (($soldiers * $efficiency) > 0)
		return -15;
	else
		return -16;
}

function soldier_environment($soldiers,$citizens,$efficiency) {
	return (($soldiers * $efficiency) > ($citizens * 0.6)) ? 1 : 0;
}

function infra_cost($infra) {
	$x = 0;
	// todo: 25k infra
	/*if ($infra >= 25000)
		$x = 90; //90 is a guess
	else*/
	if ($infra >= 15000)
		$x = 80;
	elseif ($infra >= 8000)
		$x = 70;
	elseif ($infra >= 5000)
		$x = 60;
	elseif ($infra >= 4000)
		$x = 40;
	elseif ($infra >= 3000)
		$x = 30;
	elseif ($infra >= 1000)
		$x = 25;
	elseif ($infra >= 200)
		$x = 20;
	elseif ($infra >= 100)
		$x = 15;
	elseif ($infra >= 20)
		$x = 12;
	return $x * $infra + 500;
}

function infra_upkeep($infra) {
	$x = 0;
	// todo: 25k infra
	/*if ($infra >= 25000)
		$x = 0.1755; //could be anything, even a boat
	else*/
	if ($infra >= 15000)
		$x = 0.1755;
	elseif ($infra >= 8000)
		$x = 0.175;
	elseif ($infra >= 5000)
		$x = 0.1725;
	elseif ($infra >= 4000)
		$x = 0.17;
	elseif ($infra >= 3000)
		$x = 0.15;
	elseif ($infra >= 2000)
		$x = 0.13;
	elseif ($infra >= 1000)
		$x = 0.11;
	elseif ($infra >= 700)
		$x = 0.09;
	elseif ($infra >= 500)
		$x = 0.08;
	elseif ($infra >= 300)
		$x = 0.07;
	elseif ($infra >= 200)
		$x = 0.06;
	elseif ($infra >= 100)
		$x = 0.05;
	elseif ($infra >= 20)
		$x = 0.04;
	return $x * $infra + 20;
}

function improvement_upkeep($improvements) {
	$x = 0;
	if ($improvements >= 50)
		$x = $improvements * 3000;
	elseif ($improvements >= 40)
		$x = $improvements * 2000;
	elseif ($improvements >= 30)
		$x = $improvements * 1500;
	elseif ($improvements >= 20)
		$x = $improvements * 1200;
	elseif ($improvements >= 15)
		$x = $improvements * 950;
	elseif ($improvements >= 10)
		$x = $improvements * 750;
	elseif ($improvements >= 5)
		$x = $improvements * 600;
	else
		$x = $improvements * 500;
	return $x;
}

function nuke_upkeep($nukes,$uranium) {
	$upkeep = 5000*(1+0.1*($nukes-1));
	return $uranium ? $upkeep : $upkeep * 2;
}
function nuke_strength($nukes) {
	return $nukes*$nukes*10;
}

//Land Purchased * 1.5 + Tanks Deployed * .15 + Tanks Defending * .20 + Cruise Missiles * 10 + ((Nuclear Purchased^2)*10) + Technology Purchased * 5 + Infrastructure Purchased * 3 + Actual Military * .02 + Aircraft Rating Totals * 5 + Navy Rating Totals * 10
function nation_strength($land,$tanks,$missiles,$nukes,$tech,$infra,$soldiers,$aircraft,$aircraftlevels) {
	return $land * 1.5 + $tanks * 0.2 + $missiles * 10 + nuke_strength($nukes) + $tech * 5 + $infra * 3 + $soldiers * 0.02 + $aircraft * $aircraftlevels * 5;
}

// tech infra upkeep reduction
function tech_infra_upkeep_reduction($tech,$strength) {
	if ($strength)
		return ((2 * $tech / $strength) > 0.1) ? 0.9 : 1-(2 * $tech / $strength);
	else
		return 1;
}

function infra_land_ratio($infra,$land) {
	return ($infra >= $land * 2) ? 1 : 0;
}

/*	//mm version of functions
function citizens_to_unmodcit($citizens,$soldiers,$environment,$modifier,$colony=0,$efficiency=0,$isMars=0){
	$envMod = ($environment - 1)/100;
	if ($colony && $efficiency) {
		$efficiency /= 100;
		$mmMod = 1 + (0.06 - 0.01 * $isMars) * $efficiency;
		return ($citizens + $soldiers * $envMod)/$modifier/(1 - $envMod)/$mmMod - $colony/$efficiency;
	}
	return ($citizens + $soldiers * $envMod)/$modifier/(1-$envMod);
}
function unmodcit_to_citizens($unmodcit,$soldiers,$environment,$modifier,$colony=0,$efficiency=0,$isMars=0){
	$envMod = ($environment - 1)/100;
	if ($colony && $efficiency) {
		$efficiency /= 100;
		$mmMod = 1 + (0.06 - 0.01 * $isMars) * $efficiency;
		return ($unmodcit + $colony/$efficiency) * (1 - $envMod) * $modifier * $mmMod - $soldiers * $envMod;
	}
	return $unmodcit * (1 - $envMod) * $modifier - $soldiers * $envMod;
}
*/

function citizens_to_unmodcit($citizens,$soldiers,$environment,$modifier){
	return ($citizens + $soldiers * ($environment - 1)/100)/$modifier/(1 - ($environment-1)/100);
}
function unmodcit_to_citizens($unmodcit,$soldiers,$environment,$modifier){
	return $unmodcit * (1 - ($environment - 1)/100) * $modifier - $soldiers * ($environment - 1)/100;
}
function citizen_income($base,$dollar,$percent,$happy,$environment) {
	return ($base + $dollar + $happy * 2 * (1-$environment)/100) * $percent;
}
//$temp[$rate]['Income'] = ${$mod}['Income %'] * ($basecalc['Citizen Income'] + ${$mod}['Income $'] + 2 * ($basecalc['Happiness'] + ${$mod}['Happiness'] + $temp[$rate]['Happiness']) * (1-${$qty}['Environment']/100))
function environment_calc($base,$basemod,$grl,$grlmod) {
	return (($base + $basemod) < 1) ? 1 + $grl * $grlmod : $base + $basemod + $grl * $grlmod;
}

function mans_per_land($adp) {
	return $adp ? 0.5 : 0.2;
}

function soldier_warnings($soldiers1,$citizens1,$efficiency1,$soldiers2,$citizens2,$efficiency2) {
	$nowHap = soldier_happiness($soldiers1,$citizens1,$efficiency1);
	$latHap = soldier_happiness($soldiers2,$citizens2,$efficiency2);
	$nowEnv = soldier_environment($soldiers1,$citizens1,$efficiency1);
	$latEnv = soldier_environment($soldiers2,$citizens2,$efficiency2);
	if ($nowHap || $latHap || $nowEnv || $latEnv) {
		write_table_tr_null();
		echo nbsp('<tr><td>Soldier warning: </td><td>');
		if ($nowEnv)
			echo nbsp('soldier eff > 60% citizen');
		elseif ($nowHap)
			echo nbsp('soldier eff < 20% citizen');
		else echo '-';
		echo '</td><td>';
		if ($latEnv)
			echo nbsp('soldier eff > 60% citizen');
		elseif ($latHap)
			echo nbsp('soldier eff < 20% citizen');
		else echo '-';
		echo '</td><td>-</td></tr>';
	}
}

// Mars/Moon stuff
function mm_max_days($is_mars,$is_base,$expires) {
	return (450 * (3 + $is_base)/3) * (1 + abs($is_mars));
}

function mm_expires($is_mars,$is_base,$expires,$max) {
	return ($expires >= 0 && $expires <= $max) ? $expires : $max;
}

function mm_happiness($is_mars,$is_base,$expires,$max) {
	return (3 + $is_mars + 2 * $is_base) * ($is_mars ? $max - $expires : $expires) / $max;
}

function mm_efficiency($x) {
	return ($x <= 100 && $x >= 50) ? $x : 0;
}

function mm_base_infra_reduce($is_mars,$eff) {
	return 1 - (0.04 - 0.01 * $is_mars) * $eff/100;
}

function mm_resource($resource) {
	return (preg_match("/(Basalt|Magnesium|Potassium|Sodium|Calcium|Radon|Silicon|Titanium)/",$resource)) ? $resource : 'None';
}

function mm_resource_happiness($qty) {
	if ($qty['Mine Resource'] === 'Basalt')
		return 3 * $qty['Automobiles'] * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Magnesium')
		return 4 * $qty['Microchips'] * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Potassium')
		return 3 * $qty['Fine Jewelry'] * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Sodium')
		return 2 * ($qty['Fast Food'] + $qty['Beer']) * $qty['Mine Efficiency']/100;
	return 0;
}

function mm_resource_infra_cost($qty) {
	if ($qty['Mine Resource'] === 'Basalt')
		return 1 + ($qty['Construction'] * -0.05) * $qty['Mine Efficiency']/100;
	return 1;
}

function mm_resource_infra_upkeep($qty) {
	if ($qty['Mine Resource'] === 'Basalt')
		return 1 + ($qty['Asphalt'] * -0.05) * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Magnesium')
		return 1 + ($qty['Steel'] * -0.04) * $qty['Mine Efficiency']/100;
	return 1;
}

function mm_resource_grl($qty) {
	if ($qty['Mine Resource'] === 'Sodium')
		return 1 + ($qty['Radiation Cleanup'] * -0.5) * $qty['Mine Efficiency']/100;
	return 1;
}

function mm_resource_income($qty) {
	if ($qty['Mine Resource'] === 'Potassium')
		return 3 * ($qty['Scholars'] + $qty['Affluent Population']) * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Calcium')
		return 3 * ($qty['Rubber'] + $qty['Furs'] + $qty['Spices'] + $qty['Wine']) * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Radon')
		return 3 * ($qty['Lead'] + $qty['Gold'] + $qty['Water'] + $qty['Uranium']) * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Silicon')
		return 3 * ($qty['Rubber'] + $qty['Furs'] + $qty['Gems'] + $qty['Silver']) * $qty['Mine Efficiency']/100;
	elseif ($qty['Mine Resource'] === 'Titanium')
		return 3 * ($qty['Gold'] + $qty['Lead'] + $qty['Coal'] + $qty['Oil']) * $qty['Mine Efficiency']/100;
	return 0;
}


/*
echo '<pre>';
print_r(${$qty1});
print_r(${$qty2});
print_r(${$mod1});
print_r(${$mod2});
print_r($basecalc);
print_r($calc1);
print_r($calc2);
echo '</pre>';
*/

?>
