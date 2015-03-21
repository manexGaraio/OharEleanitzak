<?php

	/*
	 * 
	  *	 OharItzultzailea: WordPress plugin that carries out machine translations (Google Translate), TTS (VoiceRSS) and QR code creation (QRickit) for enabling the creation of multilingual content.
	  *
	  *  Copyright (C) 2015  Manex Garaio Mendizabal
	  *
	  *  This program is free software: you can redistribute it and/or modify
	  *  it under the terms of the GNU General Public License as published by
	  *  the Free Software Foundation, either version 3 of the License, or
	  *  (at your option) any later version.
	  *
	  *  This program is distributed in the hope that it will be useful,
	  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  *  GNU General Public License for more details.
	  *
	  *  You should have received a copy of the GNU General Public License
	  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	  *  
	  *  You may contact the author by e-mail at the following address: manex.garaio@gmail.com
	  **/

	/*
	*	Fitxategi honetan WordPress gehigarriaren php funtzioak daude, WordPress-en gertaera eta iragazkiekin lotzen ez direnak.
	*	Aurkibidea:
	*		1 - Itzulpenak
	*			1.1- oi_gTItzulpenaEgin
	*		2 - Audioaren sortzea:
	*			2.1- oi_convertTextToMP3
	*			2.2- oi_hizkuntzKodeaLortu
	*			2.3- oi_aholab
	*			2.4- oi_fitxategiakBateratu
	*/
	
	// GetID3 liburutegia kargatu
	include_once OI_PLUGIN_KOKAPENA . 'liburutegiak/getid3/getid3.php';
	
	
	//
	//	1 - Itzulpenak
	//
	
	/*
	*	1.1: Funtzio honek Google Translate zerbitzuari deia egiten dio, parametro gisa, testua, eta jatorri eta helburu hizkuntzak jasota
	*		Itzultzen dituen balioak:
	*			(String): Dena ondo joan da, itzulpena itzuliko da
	*			1: Deia ezin izan da ireki
	*			2: GT-ri deia konfiguratzean errorea
	*			3: Erantzunak errore kodea 
	*		1,2 eta 3 kodeen kasuan $errorea aldagaiak du errorearen xehetasunen berri
	*/
	function oi_gTItzulpenaEgin( $testua, $helburuHizkuntza, $jatorriHizkuntza, &$errorea ) {
		// HTTP bidez atzitu beharrekoa. rawurlencode bide bidali behar da testua
		$helbidea = 'https://www.googleapis.com/language/translate/v2?q=' . rawurlencode( $testua ) . '&target=' . $helburuHizkuntza . '&source=' . $jatorriHizkuntza . '&format=text&key=' . GT_GAKOA;
		
		// Google Translate zerbitzuari egingo zaion deia zehaztu: helbidea, erantzuna inprimatu ordez aldagai batean gordetzea
		$ch = curl_init( $helbidea );
		if ( false === $ch) {
			$errorea = __( 'ERROREA: Ezin izan da Google Translateri egin beharreko deia ireki, itzulpena egitea eskatzean.', 'ohar-itzultzailea' );
			return 1;
		}
		// Honen bidez zehazten da erantzuna aldagai moduan gordeko dela, alegia, ez du inprimatuko
		$arrakasta = curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		if ( false === $arrakasta ) {
			$errorea = __( 'ERROREA: Ezin izan da Google Translateri egin beharreko deiaren konfigurazioa ezarri, itzulpena egitea eskatzean.', 'ohar-itzultzailea' );
			return 2;
		}
		// Deiaren exekuzioa, erantzuna JSON formatuan izango da
		$emaitzaGordina = curl_exec( $ch );
		// Egindako deiaren HTTP kodea lortu, dena ondo joan al den ikusteko
		$erantzunKodea = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		// Curl konexioa itxi
		curl_close( $ch );	
		// JSON erantzuna bektore asoziatibo batera pasatu
		$emaitza = json_decode( $emaitzaGordina, true );
		if ( 200 === $erantzunKodea ) { // Dena ondo joan bada
			// wp_send_json( $emaitza['data']['translations'][0]['translatedText'] );
			return $emaitza['data']['translations'][0]['translatedText'];
		} else { //Errorea gertatu da. Kasu horretan, errorearen kodea itzuli, Google Translatetik jasotako mezuarekin batera
			$errorea = sprintf( __( '%s kodedun errorea: %s.', 'ohar-itzultzailea' ), $erantzunKodea, $emaitza['error']['errors'][0]['message']  );
			return 3;
		}
	}
	

	
	//
	//	2 - Audioaren sortzea:
	//
	
	
	/*
	*	2.1: Funtzio honek VoiceRss eta Aholab zerbitzuei deia egiten die, testuaren audioa sortzeko. Aholab eu eta gl hizkuntzetarako erabiltzen da soilik
	*		Itzultzen dituen kodeak:
	*			0: ondo joan da
	*			1: Testua hutsik zegoen
	*			VoiceRSS:
	*				2: Ezin izan da curl paraleloa ireki
	*				3: Ezin izan da MP3 fitxategia jaitsi
	*				4: Ezin izan da MP3 fitxategia idazteko ireki
	*				5: Ezin izan da OGG fitxategia jaitsi
	*				6: Ezin izan da OGG fitxategia idazteko ireki
	*			Aholab:
	*				7: Esaldiaren audioa sortzean erroreren bat gertatu bada
	*				8: Esaldien audioak bateratzean erroreren bat gertatu bada
	*			9: Hizkuntzak ez dauka audio aukerarik
	*/
	function oi_convertTextToAudio( $str, $lang, $outfile ) {
		if ( '' === $str ) { // Jasotako testua hutsik badago, 1 kodea itzuli
			return 1;
		}
		
		if ( "eu" === $lang || "gl" === $lang ) { // Hizkuntza euskara edota galiziera bada
			// Aholab zerbitzuari deia egin
			return oi_aholab($str, $lang, $outfile);
		} else {
			$hizkuntza = oi_hizkuntzKodeaLortu( $lang );
			if ( '' === $hizkuntza ) {
				return 9;
			}
			// VoiceRss zerbitzuaren APIa erabiltzeko behar den gakoa
			$url = 'https://api.voicerss.org';
			
			// cURL hasieratu
			$ch1 = curl_init(); 
			$ch2 = curl_init(); 
			
			// cURL bidez egindako POST deien parametroak ezarri
			curl_setopt( $ch1, CURLOPT_URL, $url );
			curl_setopt( $ch1, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch1, CURLOPT_POST, true );
			curl_setopt( $ch1, CURLOPT_POSTFIELDS, array( 'key' => VR_GAKOA,
														'hl' => $hizkuntza,
														'f' => '16khz_16bit_stereo',
														'r' => -2,
														'c' => 'MP3',
														'src' => $str,
												)
			);
			
			curl_setopt( $ch2, CURLOPT_URL, $url );
			curl_setopt( $ch2, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch2, CURLOPT_POST, true );
			curl_setopt( $ch2, CURLOPT_POSTFIELDS, array( 'key' => VR_GAKOA,
														'hl' => $hizkuntza,
														'f' => '16khz_16bit_stereo',
														'r' => -2,
														'c' => 'OGG',
														'src' => $str,
												)
			);
			
			// cURL dei bat baino gehiago egiteko
			$mh = curl_multi_init();
			if ( false !== $mh ) {
				curl_multi_add_handle( $mh, $ch1 );
				curl_multi_add_handle( $mh, $ch2 );
				
				$active = null;
				//execute the handles
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);

				while ($active && $mrc == CURLM_OK) {
					if (curl_multi_select($mh) != -1) {
						do {
							$mrc = curl_multi_exec($mh, $active);
						} while ($mrc == CURLM_CALL_MULTI_PERFORM);
					}
				}
				// Jasotako erantzunak aldagaietara pasa
				$outputMP3 = curl_multi_getcontent( $ch1 );
				$outputOGG = curl_multi_getcontent( $ch2 );
				
				//close the handles
				curl_multi_remove_handle( $mh, $ch1 );
				curl_multi_remove_handle( $mh, $ch2 );
				curl_multi_close( $mh );
				// Deiek errorea itzuli badute, errore mezua itzuli
				if ( false !== strpos( $outputMP3, 'ERROR:' ) && false !== strpos( $outputOGG, 'ERROR:' ) ) {
					return __( 'MP3 eta OGG fitxategiak sortzean.', 'ohar-itzultzailea' ) . ' MP3: ' . $outputMP3 . '. OGG: ' . $outputOGG;
				} else if ( false !== strpos( $outputMP3, 'ERROR:' ) ) {
					return __( 'MP3 fitxategia sortzean.', 'ohar-itzultzailea' ) . ' MP3: ' . $outputMP3;
				} else if ( false !== strpos( $outputOGG, 'ERROR:' ) ) {
					return __( 'OGG fitxategia sortzean.', 'ohar-itzultzailea' ) . ' OGG: ' . $outputOGG;
				}
			} else { // cURL deiak itxi
				curl_close( $ch1 );
				curl_close( $ch2 );
				return 2;
			}

			if ( $outputMP3 == false ) { // Deiak ezer itzuli ez badu
				return 3;
			}
			$fp = fopen( $outfile . '.mp3', "wb" );
			if ( false !== $fp ) { //Fitxategia irekitzean errorerik gertatu ez  bada
				fwrite( $fp, $outputMP3 );
				fclose( $fp );
			} else {
				return 4;
			}
			
			if ( $outputOGG == false ) { // Deiak ezer itzuli ez badu
				return 5;
			}
			$fp = fopen( $outfile . '.ogg', "wb" );
			if ( false !== $fp ) { //Fitxategia irekitzean errorerik gertatu ez bada
				fwrite( $fp, $outputOGG );
				fclose( $fp );
			} else {
				return 6;
			}
		}
		return 0;
	}
	
	/*
	*	2.2: Funtzio lagungarri honek bi letratako kodea VoiceRSS API-ak behar duen hizkuntz-kode formatura pasatzen du
	*/
	function oi_hizkuntzKodeaLortu( $lang ) {
		switch ( $lang ) {
			case 'ca':
				return 'ca-es';
			case 'zh':
				return 'zh-cn';
			case 'da':
				return 'da-dk';
			case 'nl':
				return 'nl-nl';
			case 'en':
				return 'en-gb';
			case 'fi':
				return 'fi-fi';
			case 'fr':
				return 'fr-fr';
			case 'de':
				return 'de-de';
			case 'it':
				return 'it-it';
			case 'ja':
				return 'ja-jp';
			case 'ko':
				return 'ko-kr';
			case 'no':
				return 'nb-no';
			case 'pl':
				return 'pl-pl';
			case 'pt':
				return 'pt-pt';
			case 'ru':
				return 'ru-ru';
			case 'es':
				return 'es-es';
			case 'sv':
				return 'sv-se';
			default:	// Hizkuntza erabilgarri ez badago karaktere-kate hutsa itzuli
				return '';
		}
	}

	/*
	*	2.3: Funtzio honek Aholab zerbitzuari deia egiten dio, euskara eta galizierako audioa sortzeko.
	*		Audioa esaldika sortzen da, mp3 formatuan
	*/
	function oi_aholab($str, $hizk, $outfile) {
		// Entonazioan eraginik ez duten testua esaldika zatitzeko eragozpena suposatzen duten karaktereak definitu
		$ikurBaztergarriak = array( "...", "'", '"' );
		// Ikur baztergarriak  kendu
		$str = str_replace( $ikurBaztergarriak, "", $str );
		// Testua banatu, esaldika
		$esaldiak = explode( ".", $str );
		
		$urlErroa = 'http://tts.elhuyar.org/ahots_sintesia/ahots_sintesia_entzun';
		// Euskarazko testuen kasuan, TTS zerbitzuak arazoak ematen ditu, puntuazio ikurrekin esaldiak guztiz ahoskatzen ez ditu eta
		$puntuazioIkurrak = array( ".", ";", ":", "?", "!" );
		$fitxategiakMP3 = array();
		
		foreach ( $esaldiak as $esaldia ) {
			if ( "eu" === $hizk ) {
				$esaldia = str_replace( $puntuazioIkurrak, ",", $esaldia );
			}
			$url = "?testua=" . urlencode( $esaldia ) . "&hizkuntza=$hizk&speed=85&response=mp3";
			// Uneko esaldiaren fitxategi izena definitu
			$fitxIzena = md5($esaldia);
			$urlOsoa = $urlErroa . $url;
			
			$mp3Emaitza = file_put_contents( $fitxIzena . '.mp3', file_get_contents( $urlOsoa ) );
				
			if ( false === $mp3Emaitza ) { // Deiak ezer itzuli ez badu
				return 7;
			} else {
				$fitxategiakMP3[] = $fitxIzena . '.mp3';
			}
		}
	 
		if( count( $fitxategiakMP3 ) === count( $esaldiak ) ) { // Esaldi bakoitzeko audio fitxategi bat badugu
			if ( false === oi_fitxategiakBateratu( $outfile . ".mp3", $fitxategiakMP3 ) ) {
				return 8;
			}
		} else { // Esaldi adina fitxategi ez badago, arazo bat egon da
			foreach ($fitxategiakMP3 as $fitxategia) { // Esaldikako fitxategiak ezabatu
				unlink($fitxategia);
			}
			return 7;
		}
		
		foreach ($fitxategiakMP3 as $fitxategia) { // Esaldikako fitxategiak ezabatu
			unlink($fitxategia);
		}
		return 0;
	}
	
	/*
	*	2.3: Funtzio honek esaldikako audioak audio fitxategi bakar batean gordetzen ditu
	*/
	function oi_fitxategiakBateratu($fitxategiBateratua, $jatorriFitxategiak) {
		foreach ($jatorriFitxategiak as $fitxategia) {
			if (!is_readable($fitxategia)) {
				return false;
			}
		}
	 
		ob_start();
		if ($fp_output = fopen($fitxategiBateratua, 'wb')) {
			ob_end_clean();
			// Initialize getID3 engine
			$getID3 = new getID3;
			foreach ($jatorriFitxategiak as $fitxategia) {
				$CurrentFileInfo = $getID3->analyze($fitxategia);
				if ( $CurrentFileInfo['fileformat'] === 'mp3' ) {
					ob_start();
					if ( $fp_source = fopen($fitxategia, 'rb') ) {
						ob_end_clean();
						$CurrentOutputPosition = ftell($fp_output);
	 
						// copy audio data from first file
						fseek($fp_source, $CurrentFileInfo['avdataoffset'], SEEK_SET);
						while (!feof($fp_source) && (ftell($fp_source) < $CurrentFileInfo['avdataend'])) {
							fwrite($fp_output, fread($fp_source, 32768));
						}
						fclose($fp_source);
	 
						// trim post-audio data (if any) copied from first file that we don't need or want
						$EndOfFileOffset = $CurrentOutputPosition + ($CurrentFileInfo['avdataend'] - $CurrentFileInfo['avdataoffset']);
						fseek($fp_output, $EndOfFileOffset, SEEK_SET);
						ftruncate($fp_output, $EndOfFileOffset);
					} else {
						$errormessage = ob_get_contents();
						ob_end_clean();
						fclose($fp_output);
						return false;
					}
				} else {
					fclose($fp_output);
					return false;
				}
			}
		} else {
			$errormessage = ob_get_contents();
			ob_end_clean();
			return false;
		}
		fclose($fp_output);
		return true;
	}