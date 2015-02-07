 
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

/**
 * Mugikorraren hizkuntza detektatzerakoan, enum motako objektu hauekin
 * konparatuko da, hautatutako hizkuntza zein den jakiteko. Hizkuntzaren kodea
 * bi letraz osatuta egongo da. Balio posibleak: ca, de, en, es, eu, fr, gl,it eta pt
 * Hizkuntzak kendu edo gehitu nahi izatekotan enum-ak onartzen dituen hizkuntzak nahi duzun
 * bezala jartzeaz gain, res/values/arrays.xml fitxategiko hizkuntz izen eta kodeak, 
 * eta HizkuntzHautatzailea klaseko onCreate eta onItemSelected
 * funtzioetan dauden switch-ak eguneratu beharko dituzu, arrays.xml fitxategian jarrita
 * dagoen ordena errespetatuz
 *
 */
public enum Hizkuntzak {
	ca, de, en, es, eu, fr, gl,it, pt;

	/**
	 * Eragiketa honen bidez ikusiko da ea sarrera izeneko parametroak duen
	 * balioa Hizkuntzak enum klaseak definitutako balio bat al den. Horren
	 * araberako boolear bat itzuliko du.
	 * 
	 * @param sarrera
	 *            Hizkuntzak enum klasean al dagoen jakin nahi den objektua
	 * @return <b>boolean</b> jasotako balioa Hizkuntzak enum objektuaren
	 *         balioen artean al dagoen adierazten duen boolearra
	 * */
	public static boolean badauka(String sarrera) {
		for (Hizkuntzak h : Hizkuntzak.values()) {
			if (h.name().compareTo(sarrera) == 0) {
				return true;
			}
		}
		return false;
	}
}
