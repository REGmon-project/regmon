<?php // Results Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function get_Results_Period_Selection(string $date_from, string $date_to, bool $submit_button = false):string {
	global $LANG;

	$html = ''.
	'<div class="form-group" style="margin-bottom:0px;">'.
		'<span style="float:left; margin-left:45px;">'.$LANG->FROM.'</span>'.
		'<span style="font-size:18px;"><b>'.$LANG->RESULTS_TAB_PERIOD.'</b></span>'.
		(
			$submit_button 
			? '<button id="Button__DATE__Submit" class="forward" title="'.$LANG->RESULTS_BUTTON_APPLY_CHANGES.'" style="vertical-align:text-bottom; margin-right:-35px; margin-left: 5px;"></button>' 
			: ''
		).
		'<span style="float:right; margin-right:45px;">'.$LANG->TO.'</span>'.
		'<div class="input-group" style="width:100%; float:right;">'.
			// not want the whitespace here --keep all in one line
			'<div class="input-group date" id="datetimepicker_from" style="display:inline-table; width:53%;">'.
				'<span class="input-group-addon"><span class="fa fa-calendar"></span></span>'.
				'<input type="text" id="t_date_from" name="t_date_from" class="required form-control" value="'.$date_from.'" placeholder="'.$LANG->DATE_FROM.'" style="height:32px; font-weight:bold; font-size:14px;" autocomplete="off"/>'.
				'<span class="input-group-addon" style="padding:6px 3px; border-top-right-radius:0; border-bottom-right-radius:0;"><span class="fa fa-long-arrow-right"></span></span>'.
			'</div>'.
			'<div class="input-group date" id="datetimepicker_to" style="display:inline-table; width:47%; margin-left:-1px;">'.
				'<input type="text" id="t_date_to" name="t_date_to" class="required form-control" value="'.$date_to.'" placeholder="'.$LANG->DATE_TO.'" style="height:32px; text-align:right; font-weight:bold; font-size:14px; border-top-left-radius:0; border-bottom-left-radius:0;" autocomplete="off"/>'.
				'<span class="input-group-addon"><span class="fa fa-calendar"></span></span>'.
			'</div>'.
		'</div>'.
	'</div>';

	return $html;
}

function get_info_lines_color_formula():string {
	global $LANG;

	return ''.
	"<span class=\"help_colors\" title=\"<img src='img/highcharts_colors.png' class='img_colors'/>\">".
		'<span class="help_question">?</span> '.$LANG->RESULTS_INFO_STANDARD_COLORS.
	'</span> &nbsp; &nbsp; '.
	"<span class=\"help_lines\" title=\"<img src='img/chart_lines_".$LANG->LANG_CURRENT.".png' class='img_lines'/>\">".
		'<span class="help_question">?</span> '.$LANG->RESULTS_INFO_LINES.
	"</span> &nbsp; &nbsp; ".
	//Formula info
	'<span class="help_formula" title="'.$LANG->CLICK.'">'.
		'<span class="help_question">?</span> '.$LANG->RESULTS_INFO_FORMULA.
	'</span>'.
	'<div class="formula_info_div" style="padding:5px; background:#f6f6f6; text-align:left; display:none;">'.
		'<table class="data_table" style="font-family:tahoma;">'.
			'<tr><th style="width:1%;"></th><th style="width:25%;">Excel Name</th><th style="width:25%;">Excel Function</th><th style="width:25%;">Dynamic Line Function (Copy/Paste)</th><th style="width:24%;">Dynamic DATE</th></tr>'.
			'<tr><td>1</td><td>sRPE - var1*var2</td><td>=D7*E7</td><td>{RA}*{RB}</td><td>{RA}*{RB}</td></tr>'.
			'<tr><td>2</td><td>gleitender MW - var 1 (7 Tage)</td><td>=AVERAGE(D7:D13)</td><td>AVERAGE({RA-3}:{RA+3})</td><td>AVERAGE({RA-3}:{RA+3d})</td></tr>'.
			'<tr><td>3</td><td>gleitender MW - var1 (14 Tage)</td><td>=AVERAGE(D7:D20)</td><td>AVERAGE({RA-7}:{RA+6})</td><td>AVERAGE({RA-7d}:{RA+6d})</td></tr>'.
			'<tr><td>4</td><td>gleitender MW - var1 (21 Tage)</td><td>=AVERAGE(D7:D27)</td><td>AVERAGE({RA-10}:{RA+10})</td><td>AVERAGE({RA-10d}:{RA+10d})</td></tr>'.
			'<tr><td>5</td><td>Verhältnis a:c - bere2:bere3</td><td>=I17/K17</td><td>{BB}/{BD}</td><td>{BB}/{BD}</td></tr>'.
			'<tr><td>6</td><td>aggregierte Daten - var1 (7 Tage)</td><td>=SUM(D7:D13)</td><td>SUM({RA}:{RA+6})</td><td>SUM({RA}:{RA+6d})</td></tr>'.
			'<tr><td>7</td><td>aggregierte Daten - var1 (14 Tage)</td><td>=SUM(D7:D20)</td><td>SUM({RA}:{RA+13})</td><td>SUM({RA}:{RA+13d})</td></tr>'.
			'<tr><td>8</td><td>Minimum - var1 (7 Tage)</td><td>=MIN(D7:D13)</td><td>MIN({RA}:{RA+6})</td><td>MIN({RA}:{RA+6d})</td></tr>'.
			'<tr><td>9</td><td>Maximum - var1 (7 Tage)</td><td>=MAX(D7:D13)</td><td>MAX({RA}:{RA+6})</td><td>MAX({RA}:{RA+6d})</td></tr>'.
			'<tr><td>10</td><td>Mittelwert - var1 (7 Tage)</td><td>=AVERAGEIF(D7:D13;">0";D7:D13)</td><td>AVERAGEIF({RA}:{RA+6},\'>0\',{RA}:{RA+6}) or <br>AVERAGEIF({RA}:{RA+6},\'>0\')</td><td>AVERAGEIF({RA}:{RA+6d},\'>0\')</td></tr>'.
			'<tr><td>11</td><td>Standardabweichung - var1 (7 Tage)</td><td>=STDEV.P(IF(D7:D13>0;D7:D13))</td><td>STDEVP({RA}:{RA+6}) <br> STDEVPN0({RA}:{RA+6}) = no zero/false values</td><td>STDEVP({RA}:{RA+6d})</td></tr>'.
			'<tr><td>12</td><td>bedingte aggregierte Daten - if var3=1 -> sum var1 (7 Tage)</td><td>=SUMIF(F7:F13;1;D7:D13) </td><td>SUMIF({RC}:{RC+6},\'=1\',{RA}:{RA+6})</td><td>SUMIF({RC}:{RC+6d},\'=1\',{RA}:{RA+6d})</td></tr>'.
			'<tr><td>13</td><td>bedingte aggregierte Daten - if var3=3 -> sum var1 (14 Tage)</td><td>=SUMIF(F7:F20;3;D7:D20)</td><td>SUMIF({RC}:{RC+13},\'=3\',{RA}:{RA+13})</td><td>SUMIF({RC}:{RC+13d},\'=3\',{RA}:{RA+13d})</td></tr>'.
			'<tr><td colspan="5" style="text-align:left;">'.
				'<b><u>Available Excel Functions</u></b><br>'.
				'<b>date -></b> DATE/DATUM, DAYNAME/TAGNAME, EDATE/EDATUM <br>'.
				'<b>math -></b> SUM/SUMME, SUMIF/SUMMEWENN, SUMIFS/SUMMEWENNS <br>'.
				'<b>statistic -></b> AVERAGE/MITTELWERT, AVERAGEIF/MITTELWERTWENN, MIN, MAX, STDEVA/STABWA, STDEVP/STABWN, STDEVPN0/STABWNN0, STDEVS/STABWS <br>'.
				'<b>logical -></b> AND/UND, IF/WENN, NOT/NICHT, OR/ODER <br>'.
				'<b>text -></b> CONCAT/TEXTKETTE, JOIN/JOIN, LEFT/LINKS, LEN/LÄNGE, LOWER/KLEIN, MID/TEIL, RIGHT/RECHTS, UPPER/GROSS <br>'.
				'<hr style="margin:10px 0;">'.
				'<b><u>helper cells (hidden)</u></b><br>'.
				'<b>DZ</b>=2018-04-15 11:30:00 - <b>DA</b>=2018 - <b>DB</b>=4 - <b>DC</b>=15 - <b>ZA</b>=11 - <b>ZB</b>=30 - <b>ZC</b>=0'.
				'<br style="line-height:30px;">'.
				'<b><u>Some Examples</u></b><br>'.
				'DAYNAME({DD}) = Montag'.
				'<br style="line-height:30px;">'.
				"RIGHT({DD},2) & '.' &  MID([DD},5,2) & '.' & LEFT({DD},4) = 15.04.2018<br>".
				"{DC} &'.'& {DB} &'.'& {DA} = 15.4.2018".
				'<br style="line-height:30px;">'.
				'DATE(2018,4,15) = 2018-04-15<br>'.
				'DATE({DA},{DB},{DC}) = 2018-04-15<br>'.
				'DATE({DA}+1,{DB}+1,{DC}+1) = 2019-05-16'.
				'<br style="line-height:30px;">'.
				"EDATE('2018-04-15','.1d') = 2018-04-16<br>".
				"EDATE({DD},'-15d') = 2018-03-31<br>".
				"EDATE({DD},'-4m') = 2017-12-15".
				'<br style="line-height:30px;">'.
				'<b>valid intervals</b> = d,day,days, m,month,months, y,year,years / t,tag,tage, m,monat,monate, j,jahr,jahre (Lowercase,Uppercase or combination)'.
			'</td></tr>'.
		'</table>'.
	'</div>';
}

function get_Select__Groups__Options(int $user_id, int $group_id, string $results_page):string {
	global $db, $GROUP_ADMIN, $GROUP_ADMIN_2, $TRAINER, $ATHLETE, $Show_Only_Group_name;

	//Groups Select in Locations ####################
	//User2Group - get user groups
	$user_2_groups = array();
	$rows = $db->fetch("SELECT group_id, status FROM users2groups WHERE user_id = ? ", array($user_id)); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$user_2_groups[$row['group_id']]['status'] = $row['status'];
		}
	}

	//get Locations
	$locations = array();
	$rows = $db->fetch("SELECT id, name, admin_id FROM locations WHERE status = 1 ORDER BY id", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$locations[$row['id']] = array($row['name'], $row['admin_id']);
		}
	}
	//Groups Select Options
	$Select__Groups__Options = '';
	$Show_Only_Group_name = '';
	$rows = $db->fetch("SELECT id, location_id, name, status, admins_id 
		FROM `groups` 
		WHERE status > 0 
		ORDER BY location_id, name", array()); 
	if ($db->numberRows() > 0)  {
		$GP_open_group = false;
		$GP_group = '';
		$GP_group_tmp = '';
		foreach ($rows as $row) {
			
			$location_id = $row['location_id'];
			$location_name = '';
			$location_admin = '';
			if (isset($locations[$location_id]) AND isset($locations[$location_id][0])) {
				$location_name = $locations[$location_id][0];
				$location_admin = $locations[$location_id][1];
			}

			$GP_group = $location_name;
			$group_admins = $row['admins_id']??'';
			$group_name = $row['name'];
			$group__id = $row['id'];
			
			$t_selected = '';
			//we not have auto selected in RESULTS
			if ($results_page == 'FORMS_RESULTS') {
				if ($group__id == $group_id) {
					$Show_Only_Group_name = $group_name;
					$t_selected = ' selected'; //mark selected
				}
			}
			
			$gr_admins = explode(',', $group_admins);

			//Group optgroup
			if ($GP_group <> $GP_group_tmp) {
				if ($GP_open_group) {
					$Select__Groups__Options .= '</optgroup>';
				}
				$Select__Groups__Options .= '<optgroup label="'.$GP_group.'">';
				$GP_open_group = true;
			}


			//option
			$Private = ($row['status'] == 3 ? ' (privat)' : '');
			$option = '<option value="'.$group__id.'"'.$t_selected.'>'.$group_name . $Private.'</option>';


			//filter out what each level account cannot see
			if ($GROUP_ADMIN OR $GROUP_ADMIN_2) {
				//THIS_GROUP_ADMIN
				if (in_array($user_id, $gr_admins)) {
					$Select__Groups__Options .= $option;
				}
				//Group User
				elseif (isset($user_2_groups[$group__id]['status']) AND $user_2_groups[$group__id]['status'] == 1) {
					$Select__Groups__Options .= $option;
				}
			}
			elseif ($TRAINER OR $ATHLETE) {
				//Group User
				if (isset($user_2_groups[$group__id]['status']) AND $user_2_groups[$group__id]['status'] == 1) {
					$Select__Groups__Options .= $option;
				}
			}
			else {
				//Location-Admin, Admin
				$Select__Groups__Options .= $option;
			}
			

			$GP_group_tmp = $GP_group;
		}
		
		if ($GP_open_group) {
			$Select__Groups__Options .= '</optgroup>';
		}
	}
	return $Select__Groups__Options;
}


//Formula__Get_ALPHA_id
function Formula__Get_ALPHA_id(string $data_or_calc, int $num, bool $second_pass):string {
	$prefix = '';
	if (!$second_pass) {
		$num = ($data_or_calc == 'data' ? $num : $num - 1); //adjust num  only on first pass
		$prefix = ($data_or_calc == 'data' ? 'R' : 'B'); //put R or B only on first pass
	}
	//in case ALPHA_id is bigger than 26 letters (English Alphabet)
	$extra_ALPHA_id = ($num >= 26 ? Formula__Get_ALPHA_id($data_or_calc, (int) ($num / 26) - 1, true) : '');
	$ALPHA_id = substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $num % 26, 1);
	return $prefix . $extra_ALPHA_id . $ALPHA_id;
}


function get_No_Data_Error():string {
	global $LANG;
	
	return ''.
		'<div class="Error_No_Data">'.
			$LANG->RESULTS_NO_DATA_CHANGE_SELECTION.
		'</div>'.
		'<script>'.
			//change Loading to NoData
			'Highcharts.setOptions({'.
				'lang: { noData: "'.$LANG->RESULTS_NO_DATA_SELECTED.'" }, '.
				'noData: { style: { fontSize:"15px" } }'.
			'});'.
			'Chart__Update();'.
		'</script>';
}
