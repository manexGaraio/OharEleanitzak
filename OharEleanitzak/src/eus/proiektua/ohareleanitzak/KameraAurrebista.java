 
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

import java.io.IOException;

import android.util.Log;
import android.view.Display;
import android.view.Surface;
import android.view.SurfaceView;
import android.view.SurfaceHolder;
import android.view.WindowManager;
import android.content.Context;
import android.hardware.Camera;
import android.hardware.Camera.PreviewCallback;
import android.hardware.Camera.AutoFocusCallback;

/** A basic Camera preview class */
public class KameraAurrebista extends SurfaceView implements SurfaceHolder.Callback {
    private SurfaceHolder mHolder;
    private Camera mCamera;
    private PreviewCallback previewCallback;
    private AutoFocusCallback autoFocusCallback;
    private Context testuingurua;

	public KameraAurrebista(Context context, Camera camera,
                         PreviewCallback previewCb,
                         AutoFocusCallback autoFocusCb) {
        super(context);
        mCamera = camera;
        previewCallback = previewCb;
        autoFocusCallback = autoFocusCb;
        this.testuingurua=context;
        
        // Install a SurfaceHolder.Callback so we get notified when the
        // underlying surface is created and destroyed.
        mHolder = getHolder();
        mHolder.addCallback(this);

        // deprecated setting, but required on Android versions prior to 3.0
        mHolder.setType(SurfaceHolder.SURFACE_TYPE_PUSH_BUFFERS);
    }

    @Override
	public void surfaceCreated(SurfaceHolder holder) {
        // The Surface has been created, now tell the camera where to draw the preview.
        try {
            mCamera.setPreviewDisplay(holder);
            // mCamera.startPreview();
        } catch (IOException e) {
            Log.d("DBG", "Error setting camera preview: " + e.getMessage());
        }
    }

    /**
     * Funtzionalitaterik gabeko funtzioa. SurfaceHolder.Callback interfazea inplementatzeagatik egon behar da klasean
     */
    @Override
	public void surfaceDestroyed(SurfaceHolder holder) {
        //HUTSIK
    }

    @Override
	public void surfaceChanged(SurfaceHolder holder, int format, int width, int height) {
        /*
         * If your preview can change or rotate, take care of those events here.
         * Make sure to stop the preview before resizing or reformatting it.
         */
        if (mHolder.getSurface() == null){
          // preview surface does not exist
          return;
        }

        // stop preview before making changes
        try {
            mCamera.stopPreview();
        } catch (Exception e){
          // ignore: tried to stop a non-existent preview
        }

        try {
        	
        	//mugikorraren orientazio aldaketa jasotzeko objektua lortzeko
        	Display display = ((WindowManager)testuingurua.getSystemService(Context.WINDOW_SERVICE)).getDefaultDisplay();

        	//Mugikorrak izan duen errotazioaren araberako funtzionalitatea
            switch (display.getRotation())
            {
                case Surface.ROTATION_0:
                	//Mugikorra erretratu moduan edo zuzen dagoenean kamera ondo fokatzeko
                    mCamera.setDisplayOrientation(90);
                    break;
                case Surface.ROTATION_90:
                	//Mugikorra paisaia moduan edo etzanda dagoenean ondo foka dezan
                	mCamera.setDisplayOrientation(0);
                    break;
                case Surface.ROTATION_180:
                	//Mugikorra buruz gora dagoenean kamera ondo fokatzeko
                	mCamera.setDisplayOrientation(270);
                    break;
                case Surface.ROTATION_270:
                	//Mugikorra alderantzizko paisaia moduan dagoenean ondo foka dezan
                    mCamera.setDisplayOrientation(180);
                    break;
            }         	
        	
            mCamera.setPreviewDisplay(mHolder);
            mCamera.setPreviewCallback(previewCallback);
            mCamera.startPreview();
            mCamera.autoFocus(autoFocusCallback);
        } catch (Exception e){
            Log.d("DBG", "Error starting camera preview: " + e.getMessage());
        }
    }
}
