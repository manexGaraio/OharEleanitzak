 
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
import android.content.Intent;
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
import android.widget.Toast;

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
	
	/* Hautatzailea hasieratzen den lehen aldian onItemSelected exekutatuko da, ezer 
	*	aukeratu ez den arren. Horri aurre egiteko balio du boolear honek
	*/
	private boolean lehenAldia = true;

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
	}
	


	/**
	 * Funtzio hau hautatzaileko elementu bat aukeratua izan denean exekutatuko
	 * da eta ezarpenak eguneratzeaz arduratzen da, aukeratutako hizkuntza
	 * aplikazioarentzako hizkuntza bezala jarriz
	 * */
	@Override
	public void onItemSelected(AdapterView<?> parent, View view, int position,
			long id) {
		if (true == lehenAldia) {
			lehenAldia = false;
		} else {
			String hizkuntza = "";
			switch (position) { // Sakatua izan den indizearen arabera hizkuntza bat
								// esleitu
			case 1:
				hizkuntza = "ca";
				break;
			case 2:
				hizkuntza = "de";
				break;
			case 3:
				hizkuntza = "en";
				break;
			case 4:
				hizkuntza = "es";
				break;
			case 5:
				hizkuntza = "eu";
				break;
			case 6:
				hizkuntza = "fr";
				break;
			case 7:
				hizkuntza = "gl";
				break;
			case 8:
				hizkuntza = "it";
				break;
			case 9:
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
				// Hizkuntza hautatua izan denez, jarduera amaitu, kamera martxan jar dadin
				finish();
			}
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
