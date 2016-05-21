	var developerKey = '';
    var clientId = "";
    var scope = ['https://www.googleapis.com/auth/drive']; //full access
    var oauthToken;


	// Use the Google API Loader script to load the google.picker script.
    function authenticate() {
      gapi.load('auth', {'callback': onAuthApiLoad});
    }
 
    function onAuthApiLoad() {
		if(!oauthToken){
      window.gapi.auth.authorize(
          {
            'client_id': clientId,
            'scope': scope,
            'immediate': false
          },
          handleAuthResult);
		}
		else
		{
			alert('Already signed in');
			}
    }


    function handleAuthResult(authResult) {
      if (authResult && !authResult.error) {
        oauthToken = authResult.access_token;
        window.open('choose.php');
      }
    }
