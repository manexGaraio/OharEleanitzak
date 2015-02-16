# OharEleanitzak
Proiektu honen helburua hizkuntza ezberdinetako hiztunak biltzen diren tokietan eduki kudeaketa eleanitza eskaini ahal izatea da, toki asko behar ez duen eta edukia modu automatikoan kudeatzea ahalbidetzen duen sistema baten bitartez. Izan ere, gaur egun informazioa hizkuntz ezberdin ugaritan edukitzeak (museoetan adibidez) itzulpenak bata bestearen ondoan jartzea dakar. Hizkuntza gutxirentzako edukiak izatekotan jasangarria den antolaketa bat da, baina hizkuntza kopurua igo ahala praktikotasuna galtzen doa, eduki horiek guztientzako toki asko behar da eta. 

Espazio fisiko murritza erabilita edukia hizkuntza ezberdin ugaritan eskaini ahal izateko QR kodeak erabiltzen dira. QR kode bat, funtsean lauki forma duen barra kode bat da. QR kodeak informazio mota ezberdina gordetzeko erabili daitezke (webgune baten helbidea, telefono zenbakia, ohar bat, etab.) baina proiektu honetan helbideak gordetzeko duten gaitasuna erabiliko da. Hortaz, QR kodeak helbide bat gordeko du, kode horri lotutako edukiaren itzulpenak atzitzeko balioko duena.

Aipatutako azpiegitura posible egiteko hiru osagai garatu dira: *Android* aplikazio bat, *WordPress*-eko webgune bat eta webgune horrentzako gehigarri bat.

*Android* aplikazioak *OharEleanitzak* du izena eta QR kodeen irakurketaz arduratzen da. Barnean web helbideak  dituen edozein QR koderen irakurketa burutzen du, baina helbidea proiektuaren webguneari dagokionean, helbideari aldaketa bat egingo dio eta eduki hori aplikazioa ezarrita dagoen hizkuntzan irekiko du. *Android* Sistema Eragilean portaera lehenetsia da aplikazioaren hizkuntza mugikorraren berdina izatea eskuragarri egotekotan, baina erabiltzaileari askatasun gehiago emateko, eta gailu guztietan hizkuntza guztiak ez daudela ikusita (euskararen kasua) aplikazioan bertan hizkuntza hautatzeko aukera dago.

*WordPress* bidez garatutako webgunean QR kodeek seinalatzen dituzten edukiak daude (webgunea [ohareleanitzak.esy.es](http://ohareleanitzak.esy.es) helbidean ikus daiteke). *WordPress*-ek berez ez du gaitasunik webgune eleanitzak egiteko baina *mqtranslate* gehigarriaren bidez funtzionalitate hori lortzen da. Hortaz, webgunea nahi adina hizkuntzatan jar daiteke, horretarako hizkuntzaren zenbait datu (izena, hizkuntza kodea, dataren formatua, etab.) behar diren arren. 

Azkenik, *WordPress*-erako garatutako *Ohar Itzultzailea* izeneko gehigarriak webguneari eduki eleanitza automatikoki sortzeko aukera ematen dio. Honen bidez *WordPress*-en Bidalketa (*post*) eta Orrialdeentzako QR kodeak sortzea ahalbidetzen da. Horretaz gain, gizaki batek sortutako edukien itzulpen automatikoak egiten dira, banaka banaka interfaze bat erabilita edota denak batera modu automatizatu batean. Azkenik, itzulpen horientzako audioa sortzeko aukera ere badago, TTS edo *Text-to-speech* bitartez.

### Internazionalizazio estrategia
Erabiltzaileak aplikazioa eta webgunea bere hizkuntzan atzitzea ahalbidetzeko lehen urratsa erabiltzaileak aplikazioa bere hizkuntzan ezartzea da. Horretarako, aplikazioaren lehen exekuzioan erabiltzaileari galdetuko zaio zein hizkuntza erabili nahi duen (edozein momentutan aldatu ahalko du ezarpenaren balioa). Balio hori kontutan hartuta, webguneari dagokion QR kode bat irakurtzean kodeak gordetzen duen helbidea eraldatuko da.

Webgunearen helbideak ondorengo egitura du. Lehenik eta behin, webgunearen helbidea edo erroa, gero bi letrako hizkuntza kodea edukiko du (hautazkoa da, jartzen ez bada webgunearen lehenetsitako hizkuntza atzituko da) eta, azkenik, bidalketa edota orrialdearen helbidea. Jarraian duzue adibide bat, *elezahar-herria* helbidea duen bidalketa atzitzeko.

Helbidea: *ohareleanitzak.esy.es/arte-ederrak/elezahar-herria/*
+ Erroa: *ohareleanitzak.esy.es/*
+ Bidalketaren helbidea: *arte-ederrak/elezahar-herria/*

Adibidean erakusten den helbideak *elezahar-herria* helbidedun bidalketa webgunearen lehenetsitako hizkuntzan atzituko luke. Erabiltzaileak aplikazioa katalanez ezarrita izango balu, aplikazioak helbide hori moldatu edukiaren katalanezko bertsioa irekiko luke, ondorengo helbidea atzituta: *ohareleanitzak.esy.es/ca/arte-ederrak/elezahar-herria/*

###Dependentziak

Proiektu honek funtzionalitate konkretu batzuk garatzeko kanpo liburutegi edota zerbitzu batzuk erabiltzen ditu:
+	[*Zbar bar code reader*](http://sourceforge.net/p/zbar/news/2012/03/zbar-android-sdk-version-01-released/): *Android* Sistema Eragilerako garatutako liburutegi hau barra kodeak irakurtzeaz arduratzen da. Beste hainbat kode mota irakurtzen dituen arren, QR kodeak irakurtzeko erabiltzen da soilik. *GNU LGPL 2.1* lizentziapean eskuragarri dago.
+	[*Mqtranslate*](https://wordpress.org/plugins/mqtranslate/): Hasiera batean gehigarri honen oinarria zen *qtranslate* gehigarria erabiltzen zen arren, garatzaileen mantenu eza dela eta bere fork bat den *mqtranslate* gehigarria erabiltzen da. *WordPress* Edukiak Kudeatzeko Sistemarako garatutako gehigarri honek webgune eleanitzak edukitzea ahalbidetzen du. Aipatu berri dugun esteken egitura *mqtranslate* gehigarriak ezarritakoa da.
+	[*Google Translate*](https://cloud.google.com/translate/docs): Proiektuaren hasieran *Apertium* itzulpen zerbitzua erabili zen arren, itzulpenen kalitatea eta itzuli zitezkeen hizkuntzen aukera hedatzeko *Google Translate* zerbitzura pasa zen. Zerbitzua erabiltzeagatik ordaindu beharra dago, 20 $ itzulitako milioi bat karaktere bakoitzeko.
+	[*VoiceRSS*](http://www.voicerss.org/): *VoiceRSS* zerbitzua webgunearen edukien audioa sortzeko erabiltzen da. Zerbitzua erabiltzeagatik ordaindu behar den arren, doako bertsio bat ere badauka, egun bakoitzeko egin daitezkeen eskaerak 350era mugatzen dituena.
	
Aipatu beharra dago *Ohar Itzultzailea* gehigarriak itzulpen eta audioak sortzeko deia egiten duenean erantzunak gorde egiten dituela, eduki berdina eskuratzeko behin eta berriz eskaerak egiten ez egoteko. Era berean, portaera horrek dirua aurreztu edota doako zerbitzuaren mugak ez gainditzeko balio du.

Azkenik, webguneari estiloa emateko *WordPress*-erako [*Tiny Forge*](https://wordpress.org/themes/tiny-forge) txantiloia erabili da, webgunearen egitura sinple bat ahalbidetzen duelako. Estilo horri moldaketa batzuk egin zaizkio eta horiek *tiny-forge-child* karpetan topa ditzakezue. 

###Moldaketak

Proiektuaren helburuak betetzeko, erabiltzen den kanpo liburutegi batzuk moldatu dira. Lehenik eta behin, *mqtranslate* gehigarrian egindako moldaketak aipatuko dira. Batetik, *mqtranslate*-ren ezarpenetan esteken egitura *Pre-Path Mode* izateko konfiguratu behar da, *OharEleanitzak* aplikazioak egitura hori hartzen baitu kontutan QR kodea irakurri osteko moldaketa egiteko.  

Bestetik, *Tiny Forge* estilo txantiloia erabiltzekotan, hizkuntzaren hautatzailea webgunearen goiburuan jartzeko ondorengo aldaketak egin beharko lirateke (beste estilo txantiloientzat gehitu beharreko kodea antzekoa litzateke):
+	Estilo txantiloiaren *style.css* fitxategian kode hau gehitu:


```
#qtrans\_select\_mqtranslate-chooser {
			float: right;
			margin-bottom: 5px;
			margin-right: 5px;
		}
```

+	Estilo txantiloiaren *header.php* fitxategian ondorengo kodea gehitu beharko litzateke id bezala *site-description* duen *h2* motako elementuaren ostean, birritan (*if else* baten bi aldaeretan gehitu behar da). 
		
	```
  <div >
		<?php qtrans_generateLanguageSelectCode( 'dropdown' );?>
	</div>
```

Erabiltzen duzun estilo txantiloia erabiltzen duzula, *mqtranslate* gehigarriaren *mqranslate_utils.php* fitxategiko *qtrans_insertDropDownElement* funtzioan aldaketa bat egin behar da. Izan ere, l aldagaiari balioa ematen dion lerroaren ordez (`var l = document.createTextNode('".$q\_config['language_name'][$language]."');`) kode hau jarri behar da:

			```
			var l = document.createTextNode('".strtolower(substr($q_config['language_name'][$language],0,2))."');
			```

Bestalde, *WordPress*-en ezarpenen balioak aldatu egin behar dira. Izan ere, beharrezkoa da audioak eta QR kodeak denak kokapen berean gordetzea. *WordPress*-ek defektuz urte eta hilabeteka sailkatzen ditu, karpeta ezberdinetan antolatzeko. Hori ekiditeko webgunearen ezarpenenetan *Media* atalera joan eta *Igotako fitxategiak hilabete eta urtekako karpetetan antolatu* aukera ezgaitu. Bestetik, derrigorrezkoa ez den arren, ezarpenetako *Eztabaida* ataleko *Allow people to post comments on new articles* aukera ezgaituta erabiltzaileek iruzkinak egin ahal ez izatea lortzen da.

###Instalazioa

Proiektua erabili nahi izatekotan ondorengo urratsak eman beharko lirateke, proiektua webgune berri batera moldatzeko.

1.	Lehen urratsa zure zerbitzarian *WordPress* instalatzea da. Horrekin batera, *mqtranslate* eta *Ohar Itzultzailea* gehigarriak instalatu, eta nahi izatekotan *tiny-forge-child* estilo txantiloia. *mqtranslate* gehigarria *WordPress*-en *Plugin-ak* ataletik instalatu daitekeen arren, *Ohar Itzultzailea* gehigarriaren fitxategiak FTP bidez pasa beharko dituzu *WordPress*-en instalazioko */wp-content/plugins* karpetara.  
*tiny-forge-child* estiloa erabili nahi izatekotan, fitxategiak */wp-content/themes* karpetara pasa beharko dituzu. Horrekin batera, *Aurkezpena* ataleko *Themes* azpiatalean instalatu beharko duzue *Tiny Forge* estilo txantiloia, *tiny-forge-child* txantiloiak berarekiko dependentziak ditu eta.

2.	*Google Translate* eta *VoiceRSS* API-ak erabili ahal izateko gakoak eskuratu eta *Ohar Itzultzailea* gehigarriaren *ohar-itzultzailea.php* fitxategian definitutako *GT_GAKOA* eta *VR_GAKOA* konstanteetan gako horien balioak txertatu. *GT_GAKOA* konstanteak *Google Translate*-ren gakoa gordeko du eta *VR_GAKOA* konstanteak, berriz, *VoiceRSS* zerbitzuarena.
	
3.	*ohareleanitzak.esy.es* ez den webgune bat erabiltzekotan (hori litzateke ohikoena), *Android* aplikazioan moldaketa bat egin behar da. *src/eus/proiektua/ohareleanitzak/KameraJarduera.java* fitxategiaren hasieran definituta dagoen *webgunea* izeneko konstantea eguneratu behar da, webgune berriaren helbidea jartzeko. 
	
4.	Moldaketak atalean aipatzen diren aldaketak ere burutu beharko lirateke.
	
Goiko urrats horiek beharrezkoak dira proiektuko osagaiek ondo funtziona dezaten. 

Bestalde, *Ohar Itzultzailea* gehigarriak itzulpen automatikoak banaka-banaka (interfaze bidez) edota denak batera automatikoki egiteko aukera dauka. Bigarren kasuan, itzulpenen jatorrizko hizkuntza gisa ingelesa erabiltzen da. Hala ere, itzulpenak horiek ez dira gaztelania edota euskarara egingo, hizkuntza horietarako edukia eskuz sortzea normalagoa litzatekeelako. Hori aldatu nahi izatekotan (hizkuntzak gehitu edo kentzeko), *jsFuntzioak.php* fitxategiko *itzulpenAutomatikoakEgin* funtzioan `foreach ( $q_config['enabled_languages'] as $language )` esaten duen *loop*-aren barneko *if*-aren baldintza moldatu beharko litzateke, hizkuntzak gehitu edo kentzeko.
