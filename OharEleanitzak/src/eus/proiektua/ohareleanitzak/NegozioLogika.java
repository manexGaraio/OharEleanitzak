 
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

import java.util.Vector;

import android.content.Context;
/**
 * Klase hau datu basearen eta datu basea erabiltzen duten interfazeen arteko zubi gisa erabiltzen da,
 *  3-mailako-arkitektura jarraituz
 */
public class NegozioLogika 
{
	//Datu basearekin harremanetan jartzeko erabiliko den objektua
	private IrakurritakoKodeak irakurritakoKodeak;
	
	/**Klasearen hasieratzea. Testuingurua pasatzen zaio, datu baseaz arduratzen den IrakurritakoKodeak klaseak 
	 * erabili dezan
	 * 
	 * @param testuingurua Context
	 */
	public NegozioLogika(Context testuingurua)
	{
		irakurritakoKodeak=new IrakurritakoKodeak(testuingurua);
	}
	
	/**
	 * IrakurritakoKodeak klasearen kodeaGehitu eragiketari deitzen dio: 
	 * @see com.example.prototipoa.IrakurritakoKodeak#kodeaGehitu(String)
	 */
	public void kodeaGehitu(String uri) 
	{
		irakurritakoKodeak.kodeaGehitu(uri);
	}
	
	/**
	 * IrakurritakoKodeak klasearen irakurritakoKodeakItzuli eragiketari deitzen dio: 
	 * @see com.example.prototipoa.IrakurritakoKodeak#irakurritakoKodeakItzuli()
	 */
	public Vector<Kodea> irakurritakoKodeakItzuli()
	{
		return irakurritakoKodeak.irakurritakoKodeakItzuli();
	}
	
	/**
	 * IrakurritakoKodeak klasearen irakurritakoKodeakGarbitu eragiketari deitzen dio: 
	 * @see com.example.prototipoa.IrakurritakoKodeak#irakurritakoKodeakGarbitu()
	 */
	public boolean irakurritakoKodeakGarbitu()
	{
		return irakurritakoKodeak.irakurritakoKodeakGarbitu();
	}
}
