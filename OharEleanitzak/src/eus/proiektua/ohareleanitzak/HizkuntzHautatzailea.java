 
/*
 * 
  *	 OharEleanitzak: Android QR reader that enables visualisation of multilingual content with a WordPress site and plugin
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

package eus.proiektua.ohareleanitzak;

import java.util.Locale;

import android.app.Activity;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Spinner;

/**
 * Klase honek erabiltzaileari aplikazioaren hizkuntza aukeratzeko aukera ematen
 * dio. Izan ere, Androideko aplikazioek orokorrean mugikorraren hizkuntzan
 * kargatzen dute aplikazioa(hizkuntza horretara itzulpena egotekotan, noski).
 * Klase honekin, erabiltzaileari dauden itzulpen guztiak eskaintzen zaizkio,
 * berak hautatu dezan.
 * */
public class HizkuntzHautatzailea extends Activity implements
		OnItemSelectedListener {
	private Spinner hautatzailea;

	private ArrayAdapter<CharSequence> moldagailua;

	/**
	 * Funtzio honek jarduera sortzen den unean aplikazioak dauzkan hizkuntza
	 * ezberdinak kargatzen ditu, erabiltzaileari hautatzeko aukera emanez.
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// Interfazea kargatu aurretik hizkuntza egokia dela ziurtatu
		KameraJarduera.hizkuntzaEguneratu(this);
		setContentView(R.layout.hizkuntz_hautatzailea);
		// Hizkuntzak gorde eta erakutsiko dituen objektuaren instantzia lortu
		hautatzailea = (Spinner) findViewById(R.id.hizkuntzHautatzailea);

		// Moldagailua hasieratu, eskuragarri dauden hizkuntzekin eta
		// hautatzailearen instantziari esleitu
		moldagailua = ArrayAdapter.createFromResource(this,
				R.array.hizkuntzArray, android.R.layout.simple_spinner_item); // Specify
																				// the
																				// layout
																				// to
																				// use
																				// when
																				// the
																				// list
																				// of
																				// choices
																				// appears
		moldagailua
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item); // Apply
																							// the
																							// adapter
																							// to
																							// the
																							// spinner
		hautatzailea.setAdapter(moldagailua);

		// Hautatzaileko elementu bat aukeratua denean exekutatuko den funtzioa
		// definitu
		hautatzailea.setOnItemSelectedListener(this);
		// Ezarpenak kargatu
		SharedPreferences ezarpenZerrenda = PreferenceManager
				.getDefaultSharedPreferences(this);
		try {
			// Aplikazioan lehenetsita dagoen hizkuntza lortu(hasiera batean
			// mugikorraren hizkuntza izaten da)
			String mugikorrekoHizkuntza = Locale.getDefault().getLanguage();
			String hizkuntza;
			if (Hizkuntzak.badauka(mugikorrekoHizkuntza)) { // Mugikorreko
															// hizkuntza
															// aplikazioa
															// eskuragarri
															// dagoen hizkuntzen
															// artean badago
				// Erabiltzaileak hautatu duen hizkuntza lortu. Ezarpena hutsik
				// balego, mugikorreko hizkuntza itzuliko du
				hizkuntza = ezarpenZerrenda.getString("hautatutakoHizkuntza",
						mugikorrekoHizkuntza);
			} else {
				// Aplikazioa mugikorreko hizkuntzan eskuragarri ez badago
				// Erabiltzaileak hautatu duen hizkuntza lortu. Ezarpena hutsik
				// balego, ingelera itzuliko du
				hizkuntza = ezarpenZerrenda.getString("hautatutakoHizkuntza",
						"en");
			}
			int indizea = -1;
			switch (Hizkuntzak.valueOf(hizkuntza)) { // Hizkuntza bakoitzeko
														// indize bat esleitu
			case ca:
				indizea = 0;
				break;
			case de:
				indizea = 1;
				break;
			case en:
				indizea = 2;
				break;
			case es:
				indizea = 3;
				break;
			case eu:
				indizea = 4;
				break;
			case fr:
				indizea = 5;
				break;
			case gl:
				indizea = 6;
				break;
			case it:
				indizea = 7;
				break;
			case pt:
				indizea = 8;
				break;
			}
			if (indizea != -1) { // Indizea switch-ean aldatu bada, hizkuntza
									// egokia da
				// Hautatzailean aurrez aukeratutako hizkuntza gisa indize horri
				// dagokion hizkuntza jarri
				hautatzailea.setSelection(indizea);
			} else { // Indizea switch-ean aldatu ez bada, hizkuntza ez da
						// egokia
				// Hautatzailean aurrez aukeratutako hizkuntza gisa ingelera
				// jarri
				hautatzailea.setSelection(1);
			}
		} catch (ClassCastException e) {
			// Erroreren bat gertatzekotan hautatzailean ingelera jarri
			// hautatutako hizkuntz gisa
			hautatzailea.setSelection(1);
			Log.e("AlbaolApp-HizkuntzHautatzailea(OnCreate)",
					e.getLocalizedMessage());
		}
	}

	/**
	 * Funtzio hau hautatzaileko elementu bat aukeratua izan denean exekutatuko
	 * da eta ezarpenak eguneratzeaz arduratzen da, aukeratutako hizkuntza
	 * aplikazioarentzako hizkuntza bezala jarriz
	 * */
	@Override
	public void onItemSelected(AdapterView<?> parent, View view, int position,
			long id) {
		String hizkuntza = "";
		switch (position) { // Sakatua izan den indizearen arabera hizkuntza bat
							// esleitu
		case 0:
			hizkuntza = "ca";
			break;
		case 1:
			hizkuntza = "de";
			break;
		case 2:
			hizkuntza = "en";
			break;
		case 3:
			hizkuntza = "es";
			break;
		case 4:
			hizkuntza = "eu";
			break;
		case 5:
			hizkuntza = "fr";
			break;
		case 6:
			hizkuntza = "gl";
			break;
		case 7:
			hizkuntza = "it";
			break;
		case 8:
			hizkuntza = "pt";
			break;
		}
		if (hizkuntza != "") { // hizkuntza aldagaiak balio egokia badu
			// Hautatutako hizkuntzarekin Locale motako objektu bat sortu
			Locale lokala = new Locale(hizkuntza);
			// Sortu berri dugun Locale-a balio lehenetsi gisa jarri
			Locale.setDefault(lokala);
			// Ezarpenak kargatu, locale gisa sortu berri dugun locale-a jarri
			// eta eguneratu
			Configuration config = new Configuration();
			config.locale = lokala;
			getApplicationContext().getResources().updateConfiguration(config,
					null);
			// Ezarpenak kargatu
			SharedPreferences ezarpenak = PreferenceManager
					.getDefaultSharedPreferences(this);
			// Ezarpenak aldatzeko editorearen instantzia lortu
			SharedPreferences.Editor editorea = ezarpenak.edit();
			// hautatutakoHizkuntza ezarpenari balio berria esleitu
			editorea.putString("hautatutakoHizkuntza", hizkuntza);
			// Aldaketa gorde
			editorea.commit();
		}
	}

	/**
	 * Funtzionalitaterik gabeko funtzioa. OnItemSelectedListener interfazea
	 * inplementatzeagatik egon behar da
	 * */
	@Override
	public void onNothingSelected(AdapterView<?> parent) {
		// HUTSIK
	}

}
