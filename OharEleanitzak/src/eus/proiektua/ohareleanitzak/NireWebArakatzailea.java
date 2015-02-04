 
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
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;

public class NireWebArakatzailea extends Activity {

	private WebView myWebView;
	private ProgressBar progresuBarra;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.web_arakatzailea);

		Intent i = getIntent();
		String urlHelbidea = i.getStringExtra(KameraJarduera.ARG_URL);
		if (null == urlHelbidea) {
			urlHelbidea = "http://oharrak.albaola.com";
		}
		myWebView = (WebView) findViewById(R.id.nabigatzailea);
		progresuBarra = (ProgressBar) findViewById(R.id.progresuBarra);
		progresuBarra.setMax(100);
		//final ProgressDialog progressDialog = ProgressDialog.show(this, "Loading", "The requested page is being loaded");
		myWebView.setWebViewClient(new MyWebViewClient());
		WebSettings webSettings = myWebView.getSettings();
		webSettings.setJavaScriptEnabled(true);
		myWebView.loadUrl(urlHelbidea);
	}

	private class MyWebViewClient extends WebViewClient {
		@Override
		public boolean shouldOverrideUrlLoading(WebView view, String url) {
			if (Uri.parse(url).getHost().equals("oharrak.albaola.com")) {
				// This is my web site, so do not override; let my WebView load
				// the page
				return false;
			}
			// Otherwise, the link is not for a page on my site, so launch
			// another Activity that handles URLs
			Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
			startActivity(intent);
			//progressDialog.show();
			myWebView.loadUrl(url);
			return true;
		}
		
		@Override
		public void onPageStarted(WebView view, String url, Bitmap favicon) {
			progresuBarra.setVisibility(View.VISIBLE);
			progresuBarra.setProgress(0);
			super.onPageStarted(view, url, favicon);
		}
		
		// when finish loading page
        @Override
		public void onPageFinished(WebView view, String url) {
			super.onPageFinished(view, url);
        	progresuBarra.setProgress(100);
        	progresuBarra.setVisibility(View.GONE);
        }
	}
	
	// To handle "Back" key press event for WebView to go back to previous screen.
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event)
    {
        if ((keyCode == KeyEvent.KEYCODE_BACK) && myWebView.canGoBack()) {
        	myWebView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

}