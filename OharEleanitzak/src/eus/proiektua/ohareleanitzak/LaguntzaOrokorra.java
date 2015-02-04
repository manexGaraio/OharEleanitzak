 
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

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

/**
 * Klase honek aplikazioaren laguntzaren hasiera erakusteko balio du. Hemen jarduera ezberdinen laguntzara joateko estekak 
 * daude
 *
 */
public class LaguntzaOrokorra extends Activity {

	public final static String ARG_TEXT_ID = "text_id";
	
	public final static String ARG_ZUZENEAN = "zuzenean";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		KameraJarduera.hizkuntzaEguneratu(this);
		setContentView(R.layout.laguntza_orokorra);
	}

	/**
	 * Funtzio hau jarduera honetan dauden esteketako bat(laguntza azpiataletara eramaten dutenak) zapaltzean exekutatzen da
	 *  eta LaguntzaAzpiatala jarduera hasieratzen duen jarduerari deia egingo dio
	 * */
	public void onClickLaguntzaAzpiatala(View v) {
		int id = v.getId();
		laguntzaAzpiatalaDeitu(id);
	}

	/**
	 * Funtzio honek, sakatu den botoia kontutan hartuta, LaguntzaAzpiatala jarduera abiaraziko du, zapaldu den botoia 
	 *  parametro bezala pasaz
	 * */
	public void laguntzaAzpiatalaDeitu(int textId) {
		if (textId >= 0) {
			Intent intent = new Intent(this, LaguntzaAzpiatala.class);
			// LaguntzaAzpiatala klaseari parametro gisa zapaldu den botoiaren id-a eta false pasatuko diogu, erabiltzailea 
			//  ez delako laguntzara jardueratik zuzenean joan (laguntza hasieratik joan da)
			intent.putExtra(ARG_TEXT_ID, textId);
			intent.putExtra(ARG_ZUZENEAN, false);
			startActivity(intent);
			// Jarduera bukatuko dugu, pilaren kudeaketa egin ahal izateko
			finish();
		} else {
			Toast.makeText(this, R.string.ezDagoLaguntzarik, Toast.LENGTH_LONG)
					.show();
		}
	}
}