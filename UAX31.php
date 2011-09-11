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

function isIdentifier( $name ) {
	$valid = false;
	$valid  = checkIDStart(mb_substr($name,0,1,"UTF-8"));
	if(!$valid){
		return false;
	}
	return checkIDContinue($name);

}

function checkIDContinue( $name ) {
	$codepoints = array();
	$fcCharacterFound = false;
	$lang= NULL;
	//List of recommended scripts as per Table 5a. Recommended Scripts of UAX 31
	$allowedScripts = array("Common",
		"Arabic",
		"Armenian",
		"Bengali",
		"Bopomofo",
		"Cyrillic",
		"Devanagari",
		"Ethiopic",
		"Georgian",
		"Greek",
		"Gujarati",
		"Gurmukhi",
		"Han",
		"Hangul",
		"Hebrew",
		"Hiragana",
		"Kannada",
		"Katakana",
		"Khmer",
		"Lao",
		"Latin",
		"Malayalam",
		"Myanmar",
		"Oriya",
		"Sinhala",
		"Tamil",
		"Telugu",
		"Thaana",
		"Thai",
		"Tibetan",
		"Canadian_Aboriginal",
		"Mongolian",
		"Tifinagh",
		"Yi");
	
	
	for($i = 0; $i < sizeof($allowedScripts); $i++){       
	    if( preg_match( '/\p{'.$allowedScripts{$i}.'}+/u', $name ) ) {    
		$lang = $allowedScripts{$i};
		break;
	    }
	}
	if($lang == NULL){
	    //script does not belong to the allowed scripts
	    //TODO:Should we use Table 5b. Limited Use Scripts also?
	    return false;
	}
	$codepoints = getCodePoints($name);
	
	for($i = 0; $i < sizeof($codepoints); $i++){       
		//check whether the script contains mixed scripts
		if(!preg_match( '/\p{'.$lang.'}+/u', mb_substr($name,$i,1,"UTF-8") ) ) {    
		    if(!preg_match( '/[\x{200C}-\x{200D}]/u', mb_substr($name,$i,1,"UTF-8") ) ) {
			return false;
		    }
		}
		//UAX 31 Section 2.3 section B
		if($codepoints{$i}==8205){ //ZWJ
			if($fcCharacterFound){
				//format control characters should not be consecuitive.
				return false;
			}
			$fcCharacterFound = true;
			if(!in_array ($lang , array("Malayalam","Sinhala"))){
			    return false;
			}
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
			if(!in_array ($lang , array("Malayalam"))){
			    return false;
			}
			if($i>0 && ( $codepoints{$i-1}==3405) && $i+1< sizeof($codepoints)){ //Malayalam VIRAMA
				continue;
			}else{
				return false;
			}	
		}
		$fcCharacterFound=false;
	}
	return true;
}

function checkIDStart($character){
	$idStartList = '/[' .
		'\p{Ll}' .
		'\p{Lu}' .
		'\p{Lt}'.
		'\p{Lo}'.
		'\p{Lm}'.
		'\p{Nl}'.
		']/u';
	if( preg_match( $idStartList, $character ) ) {
		return true;
	}

	return false;
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


?>

