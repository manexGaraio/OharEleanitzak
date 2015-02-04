 
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
import android.text.method.LinkMovementMethod;
import android.view.KeyEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * Klase honek Jarduera konkretu baten laguntza erakusteko balio du.
 *
 */
public class LaguntzaAzpiatala extends Activity {

	// Jardueren pilaren kudeaketa egiteko boolearra. True bada, jarduera honetara ez da LaguntzaOrokorra klasetik iritsi, 
	//  false bada, bai
	private boolean zuzenean;
	
	/**
	 * Funtzio honek laguntza konkretu bat irekiko du, parametro gisa jasotako id-an oinarrituta
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		KameraJarduera.hizkuntzaEguneratu(this);
		setContentView(R.layout.laguntza_azpiatala);
		Intent i = getIntent();
		// Deiak jaso dituen parametroak eskuratu
		int textId = i.getIntExtra(LaguntzaOrokorra.ARG_TEXT_ID, 0);
		zuzenean = i.getBooleanExtra(LaguntzaOrokorra.ARG_ZUZENEAN, false);
		int laguntzaAzpiatala;
		switch (textId) {
		case R.id.qrLaguntzaBotoia: // QR irakurlearen laguntza erakutsi, ezkutuan dauden elementuak agerraraziz
			laguntzaAzpiatala = R.string.qrLaguntzaTestua;
			ImageView qrIrudia = (ImageView) findViewById(R.id.qrIrudia);
			qrIrudia.setVisibility(View.VISIBLE);
			TextView laguntzaJarraipena = (TextView) findViewById(R.id.laguntzaTestuaJarraipena);
			laguntzaJarraipena.setVisibility(View.VISIBLE);
			break;
		case R.id.ezarpenakLaguntzaBotoia: // Ezarpenei dagokion laguntza erakutsi
			laguntzaAzpiatala = R.string.ezarpenakLaguntzaTestua;
			break;
		case R.id.historiaLaguntzaBotoia: // Historiari dagokion laguntza erakutsi
			laguntzaAzpiatala = R.string.historiaLaguntzaTestua;
			break;
		default:
			laguntzaAzpiatala = R.string.ezDagoLaguntzarik;
		}
		TextView testuBista = (TextView) findViewById(R.id.laguntzaTestua);
		testuBista.setText(laguntzaAzpiatala);
		testuBista.setMovementMethod(LinkMovementMethod.getInstance());
	}

	/**
	 * LaguntzaOrokorra jarduera abiarazten duen funtzioari deitu, <-Laguntza hasierara itzuli esteka sakatua denean
	 * */
	public void laguntzaHasieraraItzuli(View v) {
		Intent i = new Intent(this, LaguntzaOrokorra.class);
		startActivity(i);
		finish();
	}
	
	/**
	 * Atzera joateko tekla sakatua denean LaguntzaOrokorra jarduera deitzen duen funtzioa, jarduera honetara zuzenean 
	 *  iritsi ez bada
	 * */
	@Override
    public boolean onKeyDown(int keyCode, KeyEvent event)
    {
        if (keyCode == KeyEvent.KEYCODE_BACK && false == zuzenean ) { // Atzera tekla sakatu eta zuzenean iritsi ez bada
	    	Intent i = new Intent(this, LaguntzaOrokorra.class);
			startActivity(i);
			finish();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

}