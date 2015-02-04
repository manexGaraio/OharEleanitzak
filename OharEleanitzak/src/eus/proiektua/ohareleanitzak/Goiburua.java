 
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

import java.util.ArrayList;

/**
 * Klase hau erabiltzailearen irakurketa historia erakusteko erabiltzen da. Klase hau atzitu diren elementuak 
 * sailkatzeko erabiltzen da. Klase honetako objektuek denbora-espazio bat adierazten dute(Gaur, Atzo, Azken astea...)
 * eta bere barnean denbora-espazio horren barnean irakurritako kodeak erakutsiko dira.
 */
public class Goiburua 
{
	//Klasearen atributuak
	
	//Goiburuaren izena(Gaur, atzo...)
	private String izena;
	//Goiburuak adierazten duen taldearen barneko elementuak
	private ArrayList<String> elementuak;
	
	//Eraikitzailea
	public Goiburua()
	{
		this.elementuak=new ArrayList<String>();
	}
	
	//Eragiketak
	
	/**
	 * Goiburu objektuaren izena eskuratzeko eragiketa
	 * @return <b>String</b> Goiburuaren izena
	 * */
	public String getIzena() 
	{
		return izena;
	}
	
	/**
	 * Goiburu objektuaren izena aldatzeko eragiketa
	 * @param izena Goiburuaren izen berria, <i>String</i> gisa
	 * */
	public void setIzena(String izena) 
	{
		this.izena = izena;
	}
	
	/**
	 * Goiburu objektuaren barnean dauden helbideak itzultzen dituen eragiketa
	 * @return ArrayList :String motako objektuz betea.
	 */
	public ArrayList<String> getElementuak() 
	{
		return elementuak;
	}
	
	/**
	 * Goiburu objektuaren barnean dauden helbideak aldatzen dituen eragiketa
	 * @param elementuak Goiburuari erantsi nahi zaizkion String-ak, <i>ArrayList</i> batean sartuta
	 */
	public void setElementuak(ArrayList<String> elementuak) 
	{
		this.elementuak = elementuak;
	}
	
	/**
	 * Goiburuaren barnean dauden elementuei beste bat gehitzen dion eragiketa
	 * @param elementua String motako objektua
	 */
	public void elementuaGehitu(String elementua)
	{
		this.elementuak.add(elementua);
	}
}
