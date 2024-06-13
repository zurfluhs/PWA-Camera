if(document.querySelector("#camera--çlose") != null)
{

    // Set constraints for the video stream

    var track = null;



    // Define constants

    const cameraView = document.querySelector("#camera--view"),

        cameraOutput = document.querySelector("#camera--output"),

        cameraSensor = document.querySelector("#camera--sensor"),

        cameraTrigger = document.querySelector("#camera--trigger");

        opencamfx = document.querySelector("#opencamfx");

        opencamfx = document.querySelector("#opencamfx");

        cameramain = document.querySelector("#cameramain");

        camerafront = document.querySelector("#camera--front");

        cameraclose = document.querySelector("#camera--çlose");

   

    opencamfx.onclick = function(){

        cameramain.style.display = "block";

        cameraStart("user");

    }



    camerafront.onclick = function(){

        cameramain.style.display = "block";

        facee = camerafront.getAttribute("face");

        

        if(facee == "user")

        {

            cameraStart("user");

            camerafront.setAttribute("face", "environment");

        }

        if(facee == "environment")

        {

            cameraStart("environment");

           camerafront.setAttribute("face", "user");

        }

        

    }





    // Access the device camera and stream to cameraView

    var stopp = () => cameraView.srcObject && cameraView.srcObject.getTracks().forEach(t => t.stop());

    function cameraStart(face) {

        stopp();

        if(face == "user")
        {
            var constraints = { 
                video: {
                            width: { ideal: 4096 }, 
                            height: { ideal: 2160 }, 
                            facingMode: "user" 
                        }, 
                audio: false 
            };    
        }


        if(face == "environment")
        {
            var constraints = { 
                video: {
                            width: { ideal: 4096 }, 
                            height: { ideal: 2160 }, 
                            facingMode: { exact: "environment" } 
                        }, 
                audio: false 
            };    
        }
        navigator.mediaDevices

            .getUserMedia(constraints)

            .then(function(stream) {

                track = stream.getTracks()[0];

                cameraView.srcObject = stream;

            })

            .catch(function(error) {

                console.error("Oops. Something is broken.", error);

            });

    }





    cameraclose.onclick = function(){
        stopp();
        cameramain.style.display = "none";
    }



    // Take a picture when cameraTrigger is tapped

    cameraTrigger.onclick = function() {
        jQuery("#camera--sensor").css('z-index', "99999");
        jQuery(".cambuttons").hide();
        jQuery(".cambuttons_load").show();
        cameraSensor.width = cameraView.videoWidth;
        cameraSensor.height = cameraView.videoHeight;
        cameraSensor.getContext("2d").drawImage(cameraView, 0, 0);
        fxcamoutput = cameraSensor.toDataURL('image/jpeg', 1.0);
        //cameraOutput.src = cameraSensor.toDataURL('image/jpeg', 1.0);
        //cameraOutput.classList.add("taken");
        const formData  = new FormData();
        formData.append("dataURL", fxcamoutput);
        fetch(fxjsdata.ajax_url+"?action=fxcamsave", {
            method: "POST",
            body: formData
         }).
        then(function(){
            stopp();
            location.reload(true);
        });
    };

}



// Start the video stream when the window loads

//window.addEventListener("load", cameraStart, false);





// Install ServiceWorker

/*if ('serviceWorker' in navigator) {

  console.log('CLIENT: service worker registration in progress.');

  navigator.serviceWorker.register( 'sw.js' , { scope : ' ' } ).then(function() {

    console.log('CLIENT: service worker registration complete.');

  }, function() {

    console.log('CLIENT: service worker registration failure.');

  });

} else {

  console.log('CLIENT: service worker is not supported.');

}*/

function update(stream) {
    document.querySelector('video').src = stream.url;
}
