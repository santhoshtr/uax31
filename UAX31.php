<?php
/**
* UAX 31 Implementation http://www.unicode.org/reports/tr31/ for usename validation
* Copyright 2011 Santhosh Thottingal <santhosh.thottingal@gmail.com>
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

function isValidUserName( $name ) {
	// Check an additional blacklist of troublemaker characters.
	// Should these be merged into the title char list?
	$unicodeBlacklist = '/[' .
		'\x{0080}-\x{009f}' . # iso-8859-1 control chars
		'\x{00a0}' .          # non-breaking space
		'\x{2000}-\x{200f}' . # various whitespace
		'\x{2028}-\x{202f}' . # breaks and control chars
		'\x{3000}' .          # ideographic space
		'\x{e000}-\x{f8ff}' . # private use
		']/u';
	if( preg_match( $unicodeBlacklist, $name ) ) {
		//does the string contains format control charactes ZWJ and ZWNJ?
		if( preg_match( '/[\x{200C}-\x{200D}]/u', $name ) ) {
			UAXSection203Check($name);
		}
	}
	return true;
}

function UAXSection203Check( $name ) {
	$codepoints = array();
	$fcCharacterFound = false;
	if( preg_match( '/\p{Malayalam}+/u', $name ) ) {
		$lang= "Malayalam";
	}
	if( preg_match( '/\p{Sinhala}+/u', $name ) ) {
		$lang= "Sinhala";
	}
	if( preg_match( '/\p{Arabic}+/u', $name ) ) {
		$lang= "Arabic";
	}
	$codepoints = getCodePoints($name);
	
	for($i = 0; $i < sizeof($codepoints); $i++){       
		//UAX 31 Section 2.3 section B
		if($codepoints{$i}==8205){ //ZWJ
			if($fcCharacterFound){
				//format control characters should not be consecuitive.
				return false;
			}
			$fcCharacterFound = true;
			if($i>0 && ( $codepoints{$i-1}==3405)//Malayalam VIRAMA
				|| ( $codepoints{$i-1}==3530)//Sinhala Sign Al-lakuna or VIRAMA
				){
				//TODO: the next character should be a consonant
				continue;
			}else{
				return false;
			}
		}
		//UAX 31 Section 2.3 section A2
		if($codepoints{$i}==8204){ //ZWNJ
			if($fcCharacterFound){
				//format control characters should not be consecuitive.
				return false;
			}
			$fcCharacterFound = true;
			if($i>0 && ( $codepoints{$i-1}==3405) && $i+1< sizeof($codepoints)){ //Malayalam VIRAMA
				continue;
			}else{
				return false;
			}	
		}
		$fcCharacterFound=false;
	}
	print($lang);
	return true;
}

function getCodePoints($unistr, $encoding = 'UTF-8'){       
	$codepoints = array();    
	$unistr = mb_convert_encoding($unistr,"UCS-4BE",$encoding);
	for($i = 0; $i < mb_strlen($unistr,"UCS-4BE"); $i++){       
		$unichar = mb_substr($unistr,$i,1,"UCS-4BE");                   
		$val = unpack("N",$unichar);           
		$codepoints[] = $val[1];               
	}       
	return($codepoints);
}

// example use
foreach (array('නන්දිමිත්‍ර','සසීන්ද්‍ර', 'തോട്ടിങ്ങല്‍' ,'തോട്ടിങ്ങല്') as $name) {
	echo $name,': ',isValidUserName($name),"\n";
}

?>

