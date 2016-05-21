	var developerKey = '';
	var fileId = null;
    var appId = "1059197860223";
  	var pickerApiLoaded = false;
	var token = window.opener.oauthToken;

	// Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
      gapi.load('picker', {'callback': onPickerApiLoad});
    }

    function onPickerApiLoad() {
      pickerApiLoaded = true;
      createPicker();
    }

    function createPicker() {
      if (pickerApiLoaded && token) {
        var view = new google.picker.View(google.picker.ViewId.DOCS);
        view.setMimeTypes("application/vnd.google-apps.spreadsheet");
        var picker = new google.picker.PickerBuilder()
            .enableFeature(google.picker.Feature.NAV_HIDDEN)
            .setAppId(appId)
            .setOAuthToken(token)
            .addView(view)
            .addView(new google.picker.DocsUploadView())
            .setDeveloperKey(developerKey)
            .setCallback(pickerCallback)
            .build();
         picker.setVisible(true);
      }
	  else
	  {
		  alert('Please login to Google');
		  window.open('index.php#logout','_self');
		  }
    }

    // A simple callback implementation.
    function pickerCallback(data) {
      if (data.action == google.picker.Action.PICKED) {
        fileId = data.docs[0].id;
		loadchart();
      }
    }
   
    function loadchart() {
		$.getScript('js/hc.js');
	}
	
	function logout() {
		window.opener.close();
		window.open('index.php#logout','_self');
	}